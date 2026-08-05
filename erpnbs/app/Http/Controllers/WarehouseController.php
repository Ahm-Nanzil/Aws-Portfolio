<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use App\Models\warehouse;
use App\Models\WarehouseProduct;
use DB;
use Illuminate\Http\Request;
use App\Models\User;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $warehouses = warehouse::where('created_by', '=', \Auth::user()->creatorId())->get();

        return view('warehouse.index',compact('warehouses'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('warehouse.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('create warehouse'))
        {
            // Validate the incoming request data, including the new fields
            $validator = \Validator::make(
                $request->all(), [
                    'name'                  => 'required',
                    'address'               => 'nullable',
                    'city'                  => 'nullable',
                    'city_zip'              => 'nullable',
                    'code'                  => 'nullable|string',       // Warehouse code validation
                    'state'                 => 'nullable|string',       // State validation
                    'postal_code'           => 'nullable|string',       // Postal code validation
                    'staffs'                => 'nullable|array',        // Staff members validation (as an array)
                    'display'               => 'nullable|boolean',      // Display checkbox validation
                    'hide_when_out_of_stock'=> 'nullable|boolean',      // Hide when out of stock checkbox validation
                    'note'                  => 'nullable|string',       // Note validation
                ]
            );

            // Check if validation fails
            if ($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Create a new Warehouse instance
            $warehouse = new warehouse();

            // Set the values for the fields, including the new ones
            $warehouse->name               = $request->name;
            $warehouse->address            = $request->address;
            $warehouse->city               = $request->city;
            $warehouse->city_zip           = $request->postal_code;
            $warehouse->created_by         = \Auth::user()->creatorId();
            $warehouse->code               = $request->code;                        // Set warehouse code
            $warehouse->state              = $request->state;                       // Set state
            $warehouse->postal_code        = $request->postal_code;                 // Set postal code
            $warehouse->staffs             = $request->staffs ? json_encode($request->staffs) : null;  // Staff members (JSON)
            $warehouse->display            = $request->display ?? true;             // Display checkbox (default true)
            $warehouse->hide_when_out_of_stock = $request->hide_when_out_of_stock ?? false; // Hide when out of stock checkbox (default false)
            $warehouse->note               = $request->note;                        // Set note

            // Save the new warehouse record
            $warehouse->save();

            // Redirect with success message
            return redirect()->route('warehouse.index')->with('success', __('Warehouse successfully created.'));
        }
        else
        {
            // If user doesn't have permission
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(warehouse $warehouse)
    {

        $id = WarehouseProduct::where('warehouse_id' , $warehouse->id)->first();

        if(\Auth::user()->can('show warehouse'))
        {
//            dd($warehouse->id);

            if(WarehouseProduct::where('warehouse_id' , $warehouse->id)->exists())
            {

                $warehouse = WarehouseProduct::where('warehouse_id' , $warehouse->id)->where('created_by', '=', \Auth::user()->creatorId())->get();

//                $data = DB::table('warehouse_products')
//                    ->select(DB::raw("SUM(quantity) as count"),'product_id')
//                    ->groupBy('product_id')
//                    ->get();
//                dd($data);


                return view('warehouse.show', compact('warehouse'));
            }
            else
            {


                $warehouse = [];
                return view('warehouse.show', compact('warehouse'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(warehouse $warehouse)
    {

        if(\Auth::user()->can('edit warehouse'))
        {
            if($warehouse->created_by == \Auth::user()->creatorId())
            {
                $staffs = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');

                return view('warehouse.edit', compact('warehouse','staffs'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, warehouse $warehouse)
    {
        if(\Auth::user()->can('edit warehouse'))
        {
            // Ensure the warehouse belongs to the authenticated user
            if($warehouse->created_by == \Auth::user()->creatorId())
            {
                // Validate the incoming request, including the new fields
                $validator = \Validator::make(
                    $request->all(), [
                        'name'                  => 'required',
                        'address'               => 'nullable',
                        'city'                  => 'nullable',
                        'city_zip'              => 'nullable',
                        'code'                  => 'nullable|string',       // Warehouse code validation
                        'state'                 => 'nullable|string',       // State validation
                        'postal_code'           => 'nullable|string',       // Postal code validation
                        'staffs'                => 'nullable|array',        // Staff members validation
                        'display'               => 'nullable|boolean',      // Display checkbox validation
                        'hide_when_out_of_stock'=> 'nullable|boolean',      // Hide when out of stock checkbox validation
                        'note'                  => 'nullable|string',       // Note validation
                    ]
                );

                // Check if validation fails
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                // Update the warehouse record with the new fields
                $warehouse->name               = $request->name;
                $warehouse->address            = $request->address;
                $warehouse->city               = $request->city;
                $warehouse->city_zip           = $request->postal_code;
                $warehouse->code               = $request->code;                         // Update warehouse code
                $warehouse->state              = $request->state;                        // Update state
                $warehouse->postal_code        = $request->postal_code;                  // Update postal code
                $warehouse->staffs             = $request->staffs ? json_encode($request->staffs) : null;  // Update staff members (JSON)
                $warehouse->display            = $request->display ?? true;              // Update display checkbox (default true)
                $warehouse->hide_when_out_of_stock = $request->hide_when_out_of_stock ?? false; // Update hide when out of stock checkbox (default false)
                $warehouse->note               = $request->note;                         // Update note

                // Save the updated warehouse record
                $warehouse->save();

                // Redirect with success message
                return redirect()->route('warehouse.index')->with('success', __('Warehouse successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(warehouse $warehouse)
    {
        if(\Auth::user()->can('delete warehouse'))
        {
            if($warehouse->created_by == \Auth::user()->creatorId())
            {
                $warehouse->delete();


                return redirect()->route('warehouse.index')->with('success', __('Warehouse successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
