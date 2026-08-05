<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryLossAdjustment;

class InventoryLossAdjustController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $lossAdjustments = InventoryLossAdjustment::all();

        return view('loss_adjustment.index',compact('lossAdjustments'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = [
            '1' => 'Loss',
            '2' => 'Adjustment',
        ];
        $warehouses = [
            '1' => 'Warehouse A',
            '2' => 'Warehouse B',
        ];

        return view('loss_adjustment.create',compact('types','warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                'time' => 'required|date',
                'type' => 'required',
                'warehouse' => 'required',
                'item' => 'required',
                'lot_number' => 'required',
                'expiration_date' => 'required|date',
                'quantity_available' => 'nullable|integer',
                'quantity_in_stock' => 'required|integer|min:1',
                'reason' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        $data = $request->all();
        $data['created_by'] = \Auth::id();

        InventoryLossAdjustment::create($data);

        return redirect()->route('inventory.loss-adjustment')
            ->with('success', 'Loss Adjustment created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lossAdjustment = InventoryLossAdjustment::findOrFail($id);

        $types = [
            '1' => 'Loss',
            '2' => 'Adjustment',
        ];
        $warehouses = [
            '1' => 'Warehouse A',
            '2' => 'Warehouse B',
        ];
        return view('loss_adjustment.create',compact('types','warehouses','lossAdjustment'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                'time' => 'required|date',
                'type' => 'required',
                'warehouse' => 'required',
                'item' => 'required',
                'lot_number' => 'required',
                'expiration_date' => 'required|date',
                'quantity_available' => 'nullable|integer',
                'quantity_in_stock' => 'required|integer|min:1',
                'reason' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $adjustment = InventoryLossAdjustment::findOrFail($id);

        $data = $request->all();

        $data['created_by'] = $adjustment->created_by;

        $adjustment->update($data);

        return redirect()->route('inventory.loss-adjustment')
            ->with('success', 'Loss Adjustment updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lossAdjustment = InventoryLossAdjustment::findOrFail($id);

        $lossAdjustment->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }
}
