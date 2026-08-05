<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Hierarchy;
use App\Models\Utility;
// use Illuminate\Support\Facades\Http;  // Add this line

class HierarchyRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
          // Fetch hierarchies
//     // $hierarchy = Hierarchy::whereNull('child')->get();
    $hierarchy = Hierarchy::whereNull('child')->first();

//     // Get the logged-in user's role
    $userRole = 'Sales Manager';

//     // Decode the hierarchy structure
    // $hierarchyData = json_decode($hierarchy->first()->structure, true);

   // Get all roles under the logged-in user's role
// // Get all roles under the logged-in user's role
$childrenUserIds = Hierarchy::getChildrenUserIds('ceo');

dd($childrenUserIds); // Display all roles under the specified role

// // Get all users under the roles found
// $usersUnder = $hierarchy->getUsersUnderRoles($userRole);
// $mergedUsers = $usersUnder->push($user)->unique('id'); // `unique` prevents duplicates if user is already in collection

// dd($usersUnder); // Display all users under the roles

// $taskArr = [
//     'task_name' => 'test user',
//     'task_priority' => ' test priority',
//     'task_status' => ' project name',
//     'from' => '0',
//     'from_name' => '1',
//     'pipeline' => '7',
//     'stage' => '7',
//     'status' => '7',
//     'price' => '7',


// ];

// $usrs = [
//     'ahmnanzil33@gmail.com'
// ];




// $templateArr = [
//     'visit_creator' => \Auth::user()->name,
//     'visit_name' => 'title',
//     'from' => 'Lead',
//     'from_name' => 'lafflasfj',
//     'pipeline' => 'pipeline',
//     'stage' => 'pipeline',
//     'link' => route('leads.show', 1),


// ];
$content ='Hi

A Visit has been assigned to you by {visit_creator}

Visit Details

     Visit Name: {visit_name}

{from} Information

    Name: {from_name}

    Pipeline:  {pipeline}

    Stage: {stage}


Looking forward to hear from you.
You can see your visit from here: {link}

Kind Regards,

{company_name}';
    // $resp = Utility::sendEmailTemplate('new_visit',  $usrs, $templateArr);
    // $resp =   Utility::sendEmailTemplate('new_task', $usrs, $taskArr);

    // $usr = \Auth::user();
    // $resp = self::replaceVariable($content, $templateArr);
    // dd($resp);

    $data = [
        'customer_id' => '6',
        'stage_id' => '3',
        'order' => ["6"],  // Array of order items
        'pipeline_id' => '3',
        "_token" => csrf_token(),  // CSRF token for security
    ];

    // Send the POST request to the route
    // $response = Http::post(route('customer_stages.order'), $data);
    // return route('leads.tasks.edit', ['lead' => 1, 'task' => 12]);
    return route('leads.index');



    }


    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{app_name}',
            '{company_name}',
            '{app_url}',

        ];
        $arrValue = [
            'app_name' => '-',
            'company_name' => '-',
            'app_url' => '-',

         ];

        foreach ($obj as $key => $val) {
            if (!in_array("{{$key}}", $arrVariable)) {
                $arrVariable[] = "{{$key}}"; // Add new placeholder
            }
            $arrValue[$key] = $val;
        }


        // dd($arrVariable, $arrValue,$content);
        $result =str_replace($arrVariable, array_values($arrValue), $content);
        dd($result);

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    private function findRoles($hierarchy, $role, &$roles)
{
    // Check if the current node matches the specified role
    if ($hierarchy['name'] === $role) {
        // Add this role to the roles array
        $roles[] = $hierarchy['id']; // Assuming you have role ID, if not modify accordingly
    }

    // Recursively check children
    if (isset($hierarchy['children'])) {
        foreach ($hierarchy['children'] as $child) {
            $this->findRoles($child, $role, $roles);
        }
    }
}

    /**
     * Recursive function to create roles from the hierarchy
     */
    public function createRolesFromHierarchy($node, $parentRole = null) {
        // Ensure $node is an array with 'name' key
        if (!isset($node['name'])) {
            return; // Prevent further execution if name is missing
        }

        $role = Role::firstOrCreate(
            ['name' => $node['name']], // Search for an existing role by name
            ['created_by' => \Auth::user()->id] // If not found, create it with this additional data
        );


        // If there is a parent role, inherit the parent's permissions
        if ($parentRole) {
            $role->syncPermissions($parentRole->permissions); // Inherit permissions
        }


        // If the role has children, process them recursively
        if (!empty($node['children'])) {
            foreach ($node['children'] as $child) {
                $this->createRolesFromHierarchy($child, $role); // Recursive call
            }
        }
    }
    public function getFlashMessage()
{
    $message = session('success') ?? session('error');
    $status = session('success') ? 'success' : 'error';

    return response()->json([
        'status' => $status,
        'message' => $message,
    ]);
}

}
