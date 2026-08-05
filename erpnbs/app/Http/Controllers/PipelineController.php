<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ClientDeal;
use App\Models\Deal;
use App\Models\DealDiscussion;
use App\Models\DealFile;
use App\Models\DealTask;
use App\Models\Pipeline;
use App\Models\UserDeal;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth; // Make sure to include this at the top

class PipelineController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('manage pipeline'))
        {
            // $pipelines = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get();

            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = Auth::user(); // Get the logged-in user

            // Fetch all permissions of the user as an array of names
            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            // Filter the pipelines to only include those with a corresponding user permission
            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                // Construct the permission name for the pipeline
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                // Check if the permission exists in the user's permissions array
                return in_array($permissionName, $userPermissions);
            });



            $pipelines=$filteredPipelines;

            return view('pipelines.index')->with('pipelines', $pipelines);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('create pipeline'))
        {
            return view('pipelines.create');
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('create pipeline'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('pipelines.index')->with('error', $messages->first());
            }

            $pipeline             = new Pipeline();
            $pipeline->name       = $request->name;
            $pipeline->created_by = \Auth::user()->creatorId();
                // Define the permission actions
                $actions = ['manage', 'create', 'edit', 'delete'];

                // Get the logged-in user
                $user = \Auth::user(); // or Auth::user()
                $roleName=$user->type;
                $role = Role::where('name', $roleName)->first();

            if ($role) {
                                // Loop through actions and create corresponding permissions
                                foreach ($actions as $action) {
                        $permissionName = $action . ' pipeline ' . $request->name;

                        // Check if permission already exists to avoid duplication
                        if (!Permission::where('name', $permissionName)->exists()) {
                            // Create the permission
                            $permission = Permission::create([
                                'name' => $permissionName,
                                'guard_name' => 'web'
                            ]);

                            // Assign the newly created permission to the role
                            $role->givePermissionTo($permissionName); // This assigns the permission to the role

                            // Clear the permission cache to ensure the new permission is available immediately
                            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                        }
                    }
                }

                // Optionally, you can fetch and check the role's permissions
                $rolePermissions = $role->getAllPermissions(); // Fetch all permissions of the role
                $permissionsList = $rolePermissions->pluck('name', 'id')->toArray();



            $pipeline->save();



            return redirect()->route('pipelines.index')->with('success', __('Pipeline successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Pipeline $pipeline
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Pipeline $pipeline)
    {
        return redirect()->route('pipelines.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Pipeline $pipeline
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Pipeline $pipeline)
    {
        if(\Auth::user()->can('edit pipeline'))
        {
            if($pipeline->created_by == \Auth::user()->creatorId())
            {
                return view('pipelines.edit', compact('pipeline'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Pipeline $pipeline
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pipeline $pipeline)
    {
        if(\Auth::user()->can('edit pipeline'))
        {

            if($pipeline->created_by == \Auth::user()->creatorId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('pipelines.index')->with('error', $messages->first());
                }

                $pipeline->name = $request->name;
                $pipeline->save();

                return redirect()->route('pipelines.index')->with('success', __('Pipeline successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Pipeline $pipeline
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pipeline $pipeline)
    {
        if(\Auth::user()->can('delete pipeline'))
        {
            if($pipeline->created_by == \Auth::user()->creatorId())
            {
                if(count($pipeline->stages) == 0)
                {
                    foreach($pipeline->stages as $stage)
                    {
                        $deals = Deal::where('pipeline_id', '=', $pipeline->id)->where('stage_id', '=', $stage->id)->get();
                        foreach($deals as $deal)
                        {
                            DealDiscussion::where('deal_id', '=', $deal->id)->delete();
                            DealFile::where('deal_id', '=', $deal->id)->delete();
                            ClientDeal::where('deal_id', '=', $deal->id)->delete();
                            UserDeal::where('deal_id', '=', $deal->id)->delete();
                            DealTask::where('deal_id', '=', $deal->id)->delete();
                            ActivityLog::where('deal_id', '=', $deal->id)->delete();

                            $deal->delete();
                        }

                        $stage->delete();
                    }

                    $pipeline->delete();

                    return redirect()->route('pipelines.index')->with('success', __('Pipeline successfully deleted!'));
                }
                else
                {
                    return redirect()->route('pipelines.index')->with('error', __('There are some Stages and Deals on Pipeline, please remove it first!'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
