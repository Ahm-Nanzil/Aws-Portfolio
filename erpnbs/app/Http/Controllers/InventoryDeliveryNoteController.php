<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryDeliveryNotes;
use App\Models\InventoryHistory;

class InventoryDeliveryNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $deliveryNotes = InventoryDeliveryNotes::all();

        return view('delivery_notes.index',compact('deliveryNotes'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $internalDeliveryNumber = '111';
        $deliverers = [
            '1' => 'Deliverer A',
            '2' => 'Deliverer B',
        ];
        $stockLocations = [
            '1' => 'stockLocations A',
            '2' => 'stockLocations B',
        ];

        return view('delivery_notes.create', compact(
            'internalDeliveryNumber',
            'deliverers',
            'stockLocations'

        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $validator = \Validator::make(
            $request->all(), [
                'internal_delivery_name' => 'required',
                'internal_delivery_number' => 'required',
                'accounting_date' => 'required|date',
                'voucher_date' => 'required|date',
                'deliverer' => 'required',
                'item_description' => 'required',
                'from_stock_name' => 'required',
                'to_stock_name' => 'required',
                'available_quantity' => 'nullable',
                'quantity' => 'required',
                'unit_price' => 'required',
                'amount' => 'nullable',
                'total_amount' => 'required',
                ]
        );
        if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

        // dd("hi");
        InventoryDeliveryNotes::create($request->all());
        InventoryHistory::create([
            'product_code' => 1,
            'warehouse_code' => 8,
            'warehouse_name' => 'warehouse H',
            'voucher_date' => $request->voucher_date,
            'opening_stock' => 990,
            'closing_stock' => 990 - 230,
            'lot_number_quantity_sold' => 230,
            'expiry_date' => $request->expiry_date ?? null,
            'serial_number' => 123456789,
            'note' => $request->notes?? null,
            'status' => 'Internal delivery notes',
        ]);

        return redirect()->route('inventory.delivery-notes')
            ->with('success', 'Delivery Note created successfully.');
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
    public function edit(string $id)
    {
        $internalDelivery = InventoryDeliveryNotes::findOrFail($id);
        $internalDeliveryNumber = '111';
        $deliverers = [
            '1' => 'Deliverer A',
            '2' => 'Deliverer B',
        ];
        $stockLocations = [
            '1' => 'stockLocations A',
            '2' => 'stockLocations B',
        ];


        return view('delivery_notes.create', compact(
            'internalDelivery',
            'internalDeliveryNumber',
            'deliverers',
            'stockLocations'

        ));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Fetch the record by ID
        $internalDelivery = InventoryDeliveryNotes::findOrFail($id);

        // Validate the input data
        $validator = \Validator::make(
            $request->all(), [
                'internal_delivery_name' => 'required',
                'internal_delivery_number' => 'required',
                'accounting_date' => 'required|date',
                'voucher_date' => 'required|date',
                'deliverer' => 'required',
                'item_description' => 'required',
                'from_stock_name' => 'required',
                'to_stock_name' => 'required',
                'available_quantity' => 'nullable',
                'quantity' => 'required',
                'unit_price' => 'required',
                'amount' => 'nullable',
                'total_amount' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // Update the record with the validated data
        $internalDelivery->update($request->all());

        return redirect()->route('inventory.delivery-notes')
            ->with('success', 'Delivery Note updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $note = InventoryDeliveryNotes::findOrFail($id);

        $note->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }
}
