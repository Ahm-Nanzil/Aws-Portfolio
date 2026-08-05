<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pipeline;


class RoleController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage role'))
        {

            $roles = Role::where('created_by', '=', \Auth::user()->creatorId())->where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('role.index')->with('roles', $roles);
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }


    public function create()
    {
        if(\Auth::user()->can('create role'))
        {
            $user = \Auth::user();
            if($user->type == 'super admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();
            }
            else
            {
                $permissions = new Collection();
                foreach($user->roles as $role)
                {
                    $permissions = $permissions->merge($role->permissions);
                }
                $permissions = $permissions->pluck('name', 'id')->toArray();
            }

            return view('role.create', ['permissions' => $permissions]);
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create role'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:100|unique:roles,name,NULL,id,created_by,' . \Auth::user()->creatorId(),
                                   'permissions' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $name             = $request['name'];
            $role             = new Role();
            $role->name       = $name;
            $role->created_by = \Auth::user()->creatorId();
            $permissions      = $request['permissions'];
            $role->save();

            foreach($permissions as $permission)
            {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role->givePermissionTo($p);
            }

            return redirect()->route('roles.index')->with('success' , 'Role successfully created.', 'Role ' . $role->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }

    public function edit(Role $role)
    {
        if(\Auth::user()->can('edit role'))
        {

            $user = \Auth::user();
            if($user->type == 'super admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();
            }
            else
            {
                $permissions = new Collection();
                foreach($user->roles as $role1)
                {
                    $permissions = $permissions->merge($role1->permissions);
                }
                $permissions = $permissions->pluck('name', 'id')->toArray();
            }

            $pipelines = Pipeline::all(); // Fetch all pipelines


            return view('role.edit', compact('role', 'permissions','pipelines'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }

    public function update(Request $request, Role $role)
    {
        if (\Auth::user()->can('edit role')) {
            // Validate input
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:100|unique:roles,name,' . $role['id'] . ',id,created_by,' . \Auth::user()->creatorId(),
                    'permissions' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Update the role's basic info (name, etc.)
            $input = $request->except(['permissions']);
            $role->fill($input)->save();

            // dd($role);
            // Get the new permissions to be assigned to the role
            $permissions = $request['permissions'];

            // Remove all existing permissions from the role
            $p_all = Permission::all();
            foreach ($p_all as $p) {
                $role->revokePermissionTo($p);
            }

            // Assign the new permissions to the role
            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role->givePermissionTo($p);
            }

            // Fetch the role structure (hierarchy)
            $roleStructure = DB::table('hierarchies')->where('id', $role->hierarical_id)->first();
            $decodedJson = json_decode($roleStructure->structure);
            $hierarchy = json_decode($decodedJson, true);

            // dd($hierarchy);
            // Update the role's parents with the same permissions
            $this->updateParentRolesPermissions($hierarchy, $role->name, $permissions);

            return redirect()->route('roles.index')->with('success', 'Role successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Recursive function to update parent roles' permissions based on the hierarchy
     */
/**
 * Recursive function to update and print all parent roles of a given role.
 */
private function updateParentRolesPermissions($node, $roleName, $permissions, $parent = null, $ancestors = [])
{
    // If the current node is the role we're looking for
    if ($node['name'] == $roleName) {
        // If there are ancestors, print the full chain of parents
        if (!empty($ancestors)) {
            echo "Role: {$roleName}\n";
            foreach ($ancestors as $ancestor) {
                echo "Parent: {$ancestor['name']}\n";
            }
        } else {
            echo "{$roleName} has no parent." . PHP_EOL;
        }

        // Assign permissions to this role
        $this->assignPermissionsToRole($roleName, $permissions);

        // Assign permissions to all ancestors
        foreach ($ancestors as $ancestor) {
            $this->assignPermissionsToRole($ancestor['name'], $permissions);
        }

        return; // Stop recursion when the role is found
    }

    // If there are children, keep searching recursively
    if (isset($node['children'])) {
        foreach ($node['children'] as $child) {
            // Pass the current node as parent and accumulate ancestors
            $this->updateParentRolesPermissions($child, $roleName, $permissions, $node, array_merge($ancestors, [$node]));
        }
    }
}

/**
 * Function to assign permissions to a role by its name.
 */
private function assignPermissionsToRole($roleName, $permissions)
{
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        foreach ($permissions as $permissionId) {
            $permission = Permission::find($permissionId);
            if ($permission) {
                $role->givePermissionTo($permission);
            }
        }
    }
}


    // /**
    //  * Function to assign permissions to the parent roles
    //  */
    // private function assignPermissionsToParents($node, $permissions)
    // {
    //     if (isset($node['name'])) {
    //         // Get the parent role
    //         $parentRole = Role::where('name', $node['name'])->first();

    //         if ($parentRole) {
    //             // Assign permissions to the parent role
    //             foreach ($permissions as $permissionId) {
    //                 $permission = Permission::find($permissionId);
    //                 if ($permission) {
    //                     $parentRole->givePermissionTo($permission);
    //                 }
    //             }
    //         }
    //     }

    //     // Continue to assign permissions to the next parent (if there are any)
    //     if (isset($node['children'])) {
    //         foreach ($node['children'] as $child) {
    //             $this->assignPermissionsToParents($child, $permissions);
    //         }
    //     }
    // }



    public function destroy(Role $role)
    {
        if(\Auth::user()->can('delete role'))
        {
            $role->delete();

            return redirect()->route('roles.index')->with(
                'success', 'Role successfully deleted.'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }
    public function editSingle(Role $role)
    {

        if(\Auth::user()->can('edit role'))
        {

            $user = \Auth::user();
            if($user->type == 'super admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();
            }
            else
            {
                $permissions = new Collection();
                foreach($user->roles as $role1)
                {
                    $permissions = $permissions->merge($role1->permissions);
                }
                $permissions = $permissions->pluck('name', 'id')->toArray();
            }

            $pipelines = Pipeline::all(); // Fetch all pipelines


            return view('role.edit-single', compact('role', 'permissions','pipelines'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }
    public function updateSingle(Request $request, Role $role)
    {
        if (\Auth::user()->can('edit role')) {
            // Validate input
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:100|unique:roles,name,' . $role['id'] . ',id,created_by,' . \Auth::user()->creatorId(),
                    'permissions' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Update the role's basic info (name, etc.)
            $input = $request->except(['permissions']);
            $role->fill($input)->save();

            // dd($role);
            // Get the new permissions to be assigned to the role
            $permissions = $request['permissions'];

            // Remove all existing permissions from the role
            $p_all = Permission::all();
            foreach ($p_all as $p) {
                $role->revokePermissionTo($p);
            }

            // Assign the new permissions to the role
            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role->givePermissionTo($p);
            }


            return redirect()->route('roles.index')->with('success', 'Role successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
