<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerStage;
use App\Models\Pipeline;

class CustomerStageController extends Controller
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
     */
    public function index()
    {
        if(\Auth::user()->can('manage lead stage'))
        {
            $customer_stages = CustomerStage::select('customer_stages.*', 'pipelines.name as pipeline')->join('pipelines', 'pipelines.id', '=', 'customer_stages.pipeline_id')->where('pipelines.created_by', '=', \Auth::user()->ownerId())->where('customer_stages.created_by', '=', \Auth::user()->ownerId())->orderBy('customer_stages.pipeline_id')->orderBy('customer_stages.order')->get();
            $pipelines   = [];

            foreach($customer_stages as $customer_stage)
            {
                if(!array_key_exists($customer_stage->pipeline_id, $pipelines))
                {
                    $pipelines[$customer_stage->pipeline_id]                = [];
                    $pipelines[$customer_stage->pipeline_id]['name']        = $customer_stage['pipeline'];
                    $pipelines[$customer_stage->pipeline_id]['customer_stages'] = [];
                }
                $pipelines[$customer_stage->pipeline_id]['customer_stages'][] = $customer_stage;
            }

            return view('customer_stages.index')->with('pipelines', $pipelines);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(\Auth::user()->can('create lead stage'))
        {
            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines->pluck('name', 'id');
            return view('customer_stages.create')->with('pipelines', $pipelines);
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('create lead stage'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'pipeline_id' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('customer_stages.index')->with('error', $messages->first());
            }
            $customer_stage              = new CustomerStage();
            $customer_stage->name        = $request->name;
            $customer_stage->pipeline_id = $request->pipeline_id;
            $customer_stage->created_by  = \Auth::user()->id;
            $customer_stage->save();

            return redirect()->route('customer_stages.index')->with('success', __('Customer Stage successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerStage $customerStage)
    {
        if(\Auth::user()->can('edit lead stage'))
        {
            if($customerStage)
            {
                $pipelines = Pipeline::all(); // Fetch all pipelines
                $user = \Auth::user(); // or Auth::user()

                $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

                $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                    $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                    return in_array($permissionName, $userPermissions);
                });



                $pipelines = $filteredPipelines->pluck('name', 'id');
                return view('customer_stages.edit', compact('customerStage', 'pipelines'));
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
     */
    public function update(Request $request, CustomerStage $customerStage)
    {
        if(\Auth::user()->can('edit lead stage'))
        {

            if($customerStage)
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'pipeline_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('customer_stages.index')->with('error', $messages->first());
                }

                $customerStage->name        = $request->name;
                $customerStage->pipeline_id = $request->pipeline_id;
                $customerStage->save();

                return redirect()->route('customer_stages.index')->with('success', __('Customer Stage successfully updated!'));
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
     */
    public function destroy(CustomerStage $customerStage)
    {
        if(\Auth::user()->can('delete lead stage'))
        {
            $customerStage->delete();

            return redirect()->route('customer_stages.index')->with('success', __('Customer Stage successfully deleted!'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function order(Request $request)
    {
        $post = $request->all();
        foreach($post['order'] as $key => $item)
        {
            $stage        = CustomerStage::where('id', '=', $item)->first();
            $stage->order = $key;
            $stage->save();
        }
    }
}
