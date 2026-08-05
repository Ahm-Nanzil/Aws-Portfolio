<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

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




public function findChildrenByNameWithRoleIds($name)
{
    $structure = $this->structure;

    if (is_string($structure)) {
        $structure = json_decode($structure, true);
    }

    $children = [];
    $this->findChildrenRecursiveId($structure, $name, $children);

    // Get role IDs for all children names
    return $this->mapNamesToRoleIds($children);
}

public function findParentsByNameWithRoleIds($name)
{
    $structure = $this->structure;

    if (is_string($structure)) {
        $structure = json_decode($structure, true);
    }

    $parents = [];
    $this->findParentsRecursiveID($structure, $name, $parents);

    // Reverse to maintain hierarchy order and get role IDs
    $parents = array_reverse($parents);
    return $this->mapNamesToRoleIds($parents);
}

private function mapNamesToRoleIds($names)
{
    // Get all roles that match our names in a single query
    $roles = Role::whereIn('name', $names)->get();

    // Create a mapping of names to role details
    $result = [];
    foreach ($names as $name) {
        $role = $roles->firstWhere('name', $name);
        if ($role) {
            $result[] = [
                'name' => $name,
                'role_id' => $role->id
            ];
        }
    }

    return $result;
}

private function findChildrenRecursiveId($node, $targetName, &$children)
{
    if ($node['name'] === $targetName) {
        $this->getAllChildrenNamesID($node, $children);
        return true;
    }

    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            if ($this->findChildrenRecursiveId($child, $targetName, $children)) {
                return true;
            }
        }
    }

    return false;
}

private function getAllChildrenNamesID($node, &$children)
{
    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            $children[] = $child['name'];
            $this->getAllChildrenNamesID($child, $children);
        }
    }
}

private function findParentsRecursiveID($node, $targetName, &$parents, $currentPath = [])
{
    if ($node['name'] === $targetName) {
        $parents = $currentPath;
        return true;
    }

    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            $newPath = array_merge($currentPath, [$node['name']]);

            if ($this->findParentsRecursiveID($child, $targetName, $parents, $newPath)) {
                return true;
            }
        }
    }

    return false;
}


    // find roles id from roles table start //
    public function findChildrenRoleIds($name)
    {
        $structure = $this->structure;

        if (is_string($structure)) {
            $structure = json_decode($structure, true);
        }

        $children = [];
        $this->findChildrenRecursive($structure, $name, $children);

        return Role::whereIn('name', $children)->pluck('id')->toArray();
    }

    public function findParentRoleIds($name)
    {
        $structure = $this->structure;

        if (is_string($structure)) {
            $structure = json_decode($structure, true);
        }

        $parents = [];
        $this->findParentsRecursive($structure, $name, $parents);
        $parents = array_reverse($parents);

        return Role::whereIn('name', $parents)->pluck('id')->toArray();
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
        // find roles id from roles table End //


        // find children start
public function findChildrenByName($name)
{
    $structure = $this->structure;

    if (is_string($structure)) {
        $structure = json_decode($structure, true);
    }

    $children = [];
    $this->findChildrenRecursive($structure, $name, $children);

    return $children;
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



    // find children end

    // New methods for finding parents
public function findParentsByName($name)
{
    $structure = $this->structure;

    if (is_string($structure)) {
        $structure = json_decode($structure, true);
    }

    $parents = [];
    $this->findParentsRecursive($structure, $name, $parents);

    return array_reverse($parents); // Return from top to bottom hierarchy
}

private function findParentsRecursive($node, $targetName, &$parents, $currentPath = [])
{
    // If we found the target node, return true to indicate success
    if ($node['name'] === $targetName) {
        $parents = $currentPath;
        return true;
    }

    // If this node has children, search them
    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            // Add current node to the path before recursing
            $newPath = array_merge($currentPath, [$node['name']]);

            if ($this->findParentsRecursive($child, $targetName, $parents, $newPath)) {
                return true;
            }
        }
    }

    return false;
}
// end parent
        // New methods to get user IDs
        public function findChildrenUserIds($name)
        {
            $childrenNames = $this->findChildrenByName($name);
            return $this->getUserIdsByTypes($childrenNames);
        }

        public function findParentUserIds($name)
        {
            $parentNames = $this->findParentsByName($name);
            return $this->getUserIdsByTypes($parentNames);
        }

        private function getUserIdsByTypes($types)
        {
            return User::whereIn('type', $types)
                      ->pluck('id')
                      ->toArray();
        }
}

