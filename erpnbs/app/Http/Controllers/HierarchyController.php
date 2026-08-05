<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hierarchy;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HierarchyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage company settings')) {

            $hierarchies = Hierarchy::whereNull('child')->get();

        // dd($hierarchies);

            foreach ($hierarchies as $hierarchy) {
                $hierarchy->structure = json_decode($hierarchy->structure, true); // Decode JSON to PHP array
            }
            $hasIncompleteHierarchy = Hierarchy::whereNull('child')->exists();

            return view('hierarchy.index', [
                'hierarchies' => $hierarchies,  // Collection of hierarchies
                'hasIncompleteHierarchy' => $hasIncompleteHierarchy,  // Boolean flag
            ]);

            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }

     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {




    return view('hierarchy.new');


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'structure' => 'required|json',
        ]);

        // Create new hierarchy and save it
        $hierarchy = Hierarchy::create([
            'name' => $validated['name'],
            'structure' => $validated['structure'],
        ]);

        // Retrieve the created hierarchy's structure
        $roleStructure = DB::table('hierarchies')->where('id', $hierarchy->id)->first();

        // Decode the structure
        $decodedJson = json_decode($roleStructure->structure);

        // Convert the decoded structure into an associative array
        $structureArray = json_decode($decodedJson, true);

        if (is_null($structureArray)) {
            return response()->json(['error' => 'Invalid JSON structure'], 400);
        }

        // Call the function to build the role hierarchy, passing the hierarchy ID
        $this->createRolesFromHierarchy($structureArray, null, $hierarchy->id);

        return redirect()->route('hierarchy_structure')->with('success', 'Added successfully!');
    }


        /**
     * Recursive function to create roles from the hierarchy
     */

public function createRolesFromHierarchy($node, $parentRole = null, $hierarchyId)
{
    // Ensure $node is an array with 'name' key
    if (!isset($node['name'])) {
        return; // Prevent further execution if name is missing
    }

    // Create or find the role, associating it with the hierarchy
    $role = Role::firstOrCreate(
        ['name' => $node['name']], // Search for an existing role by name
        ['created_by' => \Auth::user()->id, 'hierarical_id' => $hierarchyId] // Additional data for role creation
    );

    // If there is a parent role, inherit the parent's permissions
    if ($parentRole) {
        $role->syncPermissions($parentRole->permissions); // Inherit permissions
    }

    // If the role has children, process them recursively
    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            $this->createRolesFromHierarchy($child, $role, $hierarchyId); // Recursive call with hierarchy ID
        }
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the specific hierarchy record by ID
        $hierarchy = Hierarchy::findOrFail($id); // If not found, it will throw a 404 error

        // Return the edit view with the hierarchy data
        return view('hierarchy.edit', [
            'hierarchy' => $hierarchy, // Pass the retrieved hierarchy object to the view
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        // Retrieve the existing hierarchy by its ID
        $hierarchy = Hierarchy::findOrFail($id);

        $oldStructure = json_decode($hierarchy->structure, true);

        // Validate incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'structure' => 'required|json',
        ]);

        // Create new hierarchy and save it
        $hierarchy_new = Hierarchy::create([
            'name' => $validated['name'],
            'structure' => $validated['structure'],
        ]);

        // Process the hierarchy chain if needed
        while ($hierarchy->child != null) {
            $hierarchy = Hierarchy::findOrFail($hierarchy->child);
        }

        // Update hierarchy's child property
        $hierarchy->child = $hierarchy_new->id;
        $hierarchy->save();

        $newStructure = json_decode($validated['structure'], true);

        // Process node changes with old and new hierarchy IDs
        $this->processNodeChanges($oldStructure, $newStructure, $id, $hierarchy_new->id);

        return redirect()->route('hierarchy.edit', $hierarchy_new->id)
                         ->with('success', 'Hierarchy updated successfully.');
    }

    private function processNodeChanges($oldStructure, $newStructure, $oldHierarchyId, $newHierarchyId)
    {
        // Find roles using the old hierarical_id
        $roles = Role::where('hierarical_id', $oldHierarchyId)->get();

        // Detect deleted, edited, and added nodes
        $deletedNodes = $this->findDeletedNodes($oldStructure, $newStructure);
        $editedNodes = $this->findEditedNodes($oldStructure, $newStructure); // This now handles multiple edited nodes
        $addedNodes = $this->findAddedNodes($oldStructure, $newStructure);

        // Handle deleted nodes
        foreach ($deletedNodes as $deletedNode) {
            $this->deleteRoleByNodeNameAndHierarchyId($deletedNode, $oldHierarchyId);
        }

        // Handle edited nodes (multiple nodes update)
        foreach ($editedNodes as $editedNode) {
            $role = Role::where('name', $editedNode['old_name'])->where('hierarical_id', $oldHierarchyId)->first();
            if ($role) {
                // Update role name and hierarchical ID
                $role->name = $editedNode['new_name'];
                $role->hierarical_id = $newHierarchyId;
                $role->save();
            }
        }

        // Handle added nodes (create new roles)
        foreach ($addedNodes as $addedNode) {
            $this->createRoleByNodeNameAndHierarchyId($addedNode, $newHierarchyId);
        }

        // Update all roles' hierarical_id to the new hierarchy ID
        Role::where('hierarical_id', $oldHierarchyId)->update(['hierarical_id' => $newHierarchyId]);
    }

    private function deleteRoleByNodeNameAndHierarchyId($nodeName, $hierarchyId)
    {
        $role = Role::where('name', $nodeName)->where('hierarical_id', $hierarchyId)->first();
        if ($role) {
            $role->delete();
        }
    }

    private function createRoleByNodeNameAndHierarchyId($nodeName, $hierarchyId)
    {
        $role = Role::where('name', $nodeName)->where('hierarical_id', $hierarchyId)->first();
        if (!$role) {
            Role::create([
                'name' => $nodeName,
                'hierarical_id' => $hierarchyId,
                'created_by' => Auth::user()->id
            ]);
        }
    }

    // New method to detect all edited nodes
    private function findEditedNodes($oldStructure, $newStructure)
    {
        $editedNodes = [];

        // Check if the root node has changed
        if (isset($oldStructure['name']) && isset($newStructure['name']) && $oldStructure['name'] !== $newStructure['name']) {
            $editedNodes[] = ['old_name' => $oldStructure['name'], 'new_name' => $newStructure['name']];
        }

        // Recursively check for changes in children
        if (isset($newStructure['children'])) {
            foreach ($newStructure['children'] as $index => $child) {
                if (isset($oldStructure['children'][$index])) {
                    $editedNodes = array_merge($editedNodes, $this->findEditedNodes($oldStructure['children'][$index], $child));
                }
            }
        }

        return $editedNodes;
    }

    private function findDeletedNodes($oldStructure, $newStructure)
    {
        $deletedNodes = [];

        // If old node is not found in the new structure, it means it's deleted
        if ($oldStructure['name'] !== $newStructure['name'] && !isset($newStructure['name'])) {
            $deletedNodes[] = $oldStructure['name'];
        }

        if (isset($oldStructure['children'])) {
            foreach ($oldStructure['children'] as $index => $child) {
                if (!isset($newStructure['children'][$index])) {
                    // Node deleted
                    $deletedNodes[] = $child['name'];
                } else {
                    // Recursively check for deleted nodes in children
                    $deletedNodes = array_merge(
                        $deletedNodes,
                        $this->findDeletedNodes($child, $newStructure['children'][$index])
                    );
                }
            }
        }

        return $deletedNodes;
    }

    private function findAddedNodes($oldStructure, $newStructure)
{
    $addedNodes = [];

    // If the old structure doesn't have a node but the new one does, it's an added node
    if (empty($oldStructure) && !empty($newStructure)) {
        $addedNodes[] = $newStructure['name'];
    }

    // Check if new structure has children
    if (isset($newStructure['children'])) {
        foreach ($newStructure['children'] as $index => $child) {
            // Recursively check if nodes are added
            if (!isset($oldStructure['children'][$index])) {
                // Entire child branch is new
                $addedNodes = array_merge($addedNodes, $this->getAllNodeNames($child));
            } else {
                // Recursively find added nodes in the same position
                $addedNodes = array_merge(
                    $addedNodes,
                    $this->findAddedNodes($oldStructure['children'][$index], $child)
                );
            }
        }
    }

    return $addedNodes;
}

private function getAllNodeNames($structure)
{
    $nodeNames = [];

    if (isset($structure['name'])) {
        $nodeNames[] = $structure['name'];
    }

    if (isset($structure['children'])) {
        foreach ($structure['children'] as $child) {
            $nodeNames = array_merge($nodeNames, $this->getAllNodeNames($child));
        }
    }

    return $nodeNames;
}


    private function findNewNodeName($oldStructure, $newStructure, $oldNodeName)
    {
        if ($oldStructure['name'] === $oldNodeName) {
            return $newStructure['name'];
        }

        if (isset($newStructure['children'])) {
            foreach ($newStructure['children'] as $index => $child) {
                $newNodeName = $this->findNewNodeName($oldStructure['children'][$index] ?? [], $child, $oldNodeName);
                if ($newNodeName) {
                    return $newNodeName;
                }
            }
        }

        return null;
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
                // Find the hierarchy by its ID or fail if it doesn't exist
        $hierarchy = Hierarchy::findOrFail($id);

                   // Check if roles exist with the given hierarical_id
                   $roles = Role::where('hierarical_id', $id)->get();

                   if ($roles->isNotEmpty()) {
                       // Delete roles that have hierarical_id equal to the hierarchy ID
                       Role::where('hierarical_id', $id)->delete();
                   }

        // Find the previous hierarchy where the current hierarchy is a child
        $previous = Hierarchy::where('child', '=', $id)->orderBy('id', 'desc')->first();


        if ($previous != null) {

          // If $previous->structure is already a decoded object, no need for another json_decode
            $structureArray = is_string($previous->structure) ? json_decode($previous->structure, true) : (array)$previous->structure;

            $this->createRolesFromHierarchy($structureArray, null, $previous->id);


            // If the current hierarchy has no child, set the previous hierarchy's child to null
            if ($hierarchy->child == null) {
                $previous->child = null;
            }
            // Otherwise, update the previous hierarchy's child to the current hierarchy's child
            else {
                $previous->child = $hierarchy->child;
            }

            // Save the updated previous hierarchy record
            $previous->save();
        }




        // Delete the hierarchy
        $hierarchy->delete();

        // Redirect back or to a specific route with a success message
        return redirect()->route('hierarchy_structure')->with('success', 'Hierarchy deleted successfully!');
    }


            public function previous($id)
        {
            $previous = Hierarchy::where('child', '=', $id)->orderBy('id', 'desc')->first();

            if($previous==null){
                return redirect()->back()->with('success', 'There is no previous history');
            }
            return view('hierarchy.history', [
                'hierarchy' => $previous, // Pass the retrieved hierarchy object to the view
                'latest' => $id,
            ]);
            // if ($previous) {
            //     return response()->json(['status' => 'success', 'data' => $previous], 200);
            // }

            // return response()->json(['status' => 'error', 'message' => 'No previous hierarchy found'], 404);
        }

        public function next($id)
        {
            $hierarchy = Hierarchy::findOrFail($id);

            $next = Hierarchy::where('id', '=', $hierarchy->child)->orderBy('id', 'asc')->first();

            if($next==null){
                return redirect()->back()->with('success', 'This is the latest structure');
            }

            return view('hierarchy.history', [
                'hierarchy' => $next, // Pass the retrieved hierarchy object to the view
                'latest' => $id,

            ]);

            // if ($next) {
            //     return response()->json(['status' => 'success', 'data' => $next], 200);
            // }

            // return response()->json(['status' => 'error', 'message' => 'No next hierarchy found'], 404);
        }


}
