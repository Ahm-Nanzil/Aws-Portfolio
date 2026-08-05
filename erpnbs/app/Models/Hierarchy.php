<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use App\Models\User;

class Hierarchy extends Model
{
    use HasFactory;

    // The table associated with the model (optional, if the model name matches the table name)
    protected $table = 'hierarchies';

    // Define which fields are mass assignable
    protected $fillable = [
        'name',
        'structure',
        'child',
    ];

    // Cast the structure attribute as a JSON object
    protected $casts = [
        'structure' => 'array', // Automatically cast JSON string to array when retrieving, and array to JSON when storing
    ];

    /**
     * Get all roles under a specified role in the hierarchy.
     *
     * @param string $roleName
     * @return array
     */
    public function getRolesUnder($roleName)
    {
        $roles = [];

        // Ensure structure is decoded as an array
        $structure = $this->structure;
        if (is_string($structure)) {
            $structure = json_decode($structure, true);
        }

        if (is_array($structure)) {
            $this->findRoles($structure, $roleName, $roles);
        }

        return array_unique($roles);
    }


    /**
     * Get all users under a specified role in the hierarchy.
     *
     * @param string $roleName
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersUnderRoles($roleName)
    {
        // Get roles under the specified role
        $roles = $this->getRolesUnder($roleName);

        // Fetch users associated with those roles
        return User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->get();
    }

    /**
     * Recursively find roles under a specified role.
     *
     * @param array $hierarchy
     * @param string $roleName
     * @param array &$roles
     */
    private function findRoles($hierarchy, $roleName, &$roles)
    {
        // Check if the current node matches the specified role
        if ($hierarchy['name'] === $roleName) {
            $this->addChildRoles($hierarchy, $roles);
        }

        // Recursively check children
        foreach ($hierarchy['children'] ?? [] as $child) {
            $this->findRoles($child, $roleName, $roles);
        }
    }

    /**
     * Add the child roles to the roles array.
     *
     * @param array $hierarchy
     * @param array &$roles
     */
    private function addChildRoles($hierarchy, &$roles)
    {
        // Add the current role to the list if it's not already present
        if (!in_array($hierarchy['name'], $roles)) {
            $roles[] = $hierarchy['name'];
        }

        // Add all children roles
        foreach ($hierarchy['children'] ?? [] as $child) {
            if (!in_array($child['name'], $roles)) {
                $roles[] = $child['name'];
            }
            $this->addChildRoles($child, $roles); // Recursively add child roles
        }
    }

     // Static method to get hierarchy instance
     private static function getHierarchyInstance()
     {
         return static::whereNull('child')->first();
     }

     // Static methods for easy access from controllers
     public static function getChildrenUserIds($positionName)
     {
         $hierarchy = static::getHierarchyInstance();
         if (!$hierarchy) {
             return [];
         }
         return $hierarchy->findChildrenUserIds($positionName);
     }

     public static function getParentUserIds($positionName)
     {
         $hierarchy = static::getHierarchyInstance();
         if (!$hierarchy) {
             return [];
         }
         return $hierarchy->findParentUserIds($positionName);
     }

     // Instance methods
     private function findChildrenUserIds($name)
     {
         $childrenNames = $this->findChildrenByName($name);
         return $this->getUserIdsByTypes($childrenNames);
     }

     private function findParentUserIds($name)
     {
         $parentNames = $this->findParentsByName($name);
         return $this->getUserIdsByTypes($parentNames);
     }

     private function findChildrenByName($name)
     {
         $structure = $this->structure;
         if (is_string($structure)) {
             $structure = json_decode($structure, true);
         }
         $children = [];
         $this->findChildrenRecursive($structure, $name, $children);
         return $children;
     }

     private function findParentsByName($name)
     {
         $structure = $this->structure;
         if (is_string($structure)) {
             $structure = json_decode($structure, true);
         }
         $parents = [];
         $this->findParentsRecursive($structure, $name, $parents);
         return array_reverse($parents);
     }

     private function getUserIdsByTypes($types)
     {
         return User::whereIn('type', $types)
                   ->pluck('id')
                   ->toArray();
     }

     private function findChildrenRecursive($node, $targetName, &$children)
     {
         if ($node['name'] === $targetName) {
             $this->getAllChildrenNames($node, $children);
             return true;
         }
         if (!empty($node['children'])) {
             foreach ($node['children'] as $child) {
                 if ($this->findChildrenRecursive($child, $targetName, $children)) {
                     return true;
                 }
             }
         }
         return false;
     }

     private function getAllChildrenNames($node, &$children)
     {
         if (!empty($node['children'])) {
             foreach ($node['children'] as $child) {
                 $children[] = $child['name'];
                 $this->getAllChildrenNames($child, $children);
             }
         }
     }

     private function findParentsRecursive($node, $targetName, &$parents, $currentPath = [])
     {
         if ($node['name'] === $targetName) {
             $parents = $currentPath;
             return true;
         }
         if (!empty($node['children'])) {
             foreach ($node['children'] as $child) {
                 $newPath = array_merge($currentPath, [$node['name']]);
                 if ($this->findParentsRecursive($child, $targetName, $parents, $newPath)) {
                     return true;
                 }
             }
         }
         return false;
     }

}
