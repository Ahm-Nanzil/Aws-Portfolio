<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryRiecevingVoucher;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryHistory;

class InventoryReturnVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventoryVouchers = InventoryRiecevingVoucher::all();

        return view('return_voucher.index',compact('inventoryVouchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deliveryDocketNumber = 'DDN12345';
        $purchaseOrders = [
            'PO123' => 'Purchase Order 123',
            'PO124' => 'Purchase Order 124',
        ];
        $suppliers = [
            '1' => 'Supplier A',
            '2' => 'Supplier B',
        ];
        $buyers = [
            '1' => 'Buyer A',
            '2' => 'Buyer B',
        ];
        $projects = [
            '1' => 'Project X',
            '2' => 'Project Y',
        ];
        $types = [
            'type1' => 'Type 1',
            'type2' => 'Type 2',
        ];
        $departments = [
            '1' => 'Department A',
            '2' => 'Department B',
        ];
        $requesters = [
            '1' => 'Requester A',
            '2' => 'Requester B',
        ];
        $warehouses = [
            '1' => 'Warehouse A',
            '2' => 'Warehouse B',
        ];

        return view('return_voucher.create', compact(
            'deliveryDocketNumber',
            'purchaseOrders',
            'suppliers',
            'buyers',
            'projects',
            'types',
            'departments',
            'requesters',
            'warehouses'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {

    //     $validator = $request->validate([
    //         'delivery_docket_number' => 'required',
    //         'accounting_date' => 'required',
    //         'voucher_date' => 'required',
    //         'purchase_order' => 'required',
    //         'supplier_name' => 'required',
    //         'buyer' => 'required',
    //         'project' => 'required',
    //         // 'type' => 'required',
    //         // 'department' => 'required',
    //         // 'requester' => 'required',
    //         // 'deliverer' => 'required',
    //         // // 'warehouse_name' => 'required',
    //         // 'expiry_date' => 'nullable',
    //         // 'invoice_no' => 'required',
    //         // 'notes' => 'nullable',
    //         // 'items' => 'required',
    //         // 'items.*.description' => 'required',
    //         // 'items.*.quantity' => 'required',
    //         // 'items.*.unit_price' => 'required',
    //         // 'items.*.tax' => 'required',
    //         // 'items.*.lot_number' => 'nullable',
    //         // 'items.*.date_manufacture' => 'nullable',
    //         // 'items.*.expiry_date' => 'nullable',
    //     ]);

    //     // $validated['items'] = json_encode($validated['items']); // Encode items array as JSON

    //     InventoryRiecevingVoucher::create($validated);

    //     return redirect()->back()->with('success', 'Voucher created successfully.');

    // }
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'delivery_docket_number' => 'required',
                'accounting_date' => 'required|date',
                'voucher_date' => 'required|date',
                'purchase_order' => 'required',
                'supplier_name' => 'required',
                'buyer' => 'required',
                'project' => 'required',
                'type' => 'required',
                'department' => 'required',
                'requester' => 'required',
                'deliverer' => 'required',
                'warehouse_name' => 'required',
                'expiry_date' => 'nullable|date',
                'invoice_no' => 'required',

                // Item details validation
                'item_description' => 'required',
                'item_warehouse_name' => 'required',
                'quantity' => 'required|numeric|min:1',
                'unit_price' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'lot_number' => 'nullable',
                'date_manufacture' => 'nullable|date',
                'item_expiry_date' => 'nullable|date',
                'amount' => 'required|numeric',

                // Totals validation
                'total_goods_value' => 'required|numeric',
                'value_of_inventory' => 'required|numeric',
                'total_tax_amount' => 'required|numeric',
                'total_payment' => 'required|numeric',

                'notes' => 'nullable',
            ]);

            DB::beginTransaction();

            // Create the voucher using validated data
            $voucher = InventoryRiecevingVoucher::create($validated);

            DB::commit();

            InventoryHistory::create([
                'product_code' => 9,
                'warehouse_code' => 1,
                'warehouse_name' => 'warehouse A',
                'voucher_date' => $request->voucher_date,
                'opening_stock' => 1200,
                'closing_stock' => 1200 - 120,
                'lot_number_quantity_sold' => 120,
                'expiry_date' => $request->expiry_date ?? null,
                'serial_number' => 123456789,
                'note' => $request->notes,
                'status' => 'Recieving voucher',
            ]);

            return redirect()
                ->route('inventory.return-voucher')  // Make sure this route name is correct
                ->with('success', 'Inventory voucher created successfully.');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating inventory voucher: ' . $e->getMessage());
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
    public function edit(string $id)
    {
        $voucher = InventoryRiecevingVoucher::findOrFail($id);

        $deliveryDocketNumber = 'DDN12345';
        $purchaseOrders = [
            'PO123' => 'Purchase Order 123',
            'PO124' => 'Purchase Order 124',
        ];
        $suppliers = [
            '1' => 'Supplier A',
            '2' => 'Supplier B',
        ];
        $buyers = [
            '1' => 'Buyer A',
            '2' => 'Buyer B',
        ];
        $projects = [
            '1' => 'Project X',
            '2' => 'Project Y',
        ];
        $types = [
            'type1' => 'Type 1',
            'type2' => 'Type 2',
        ];
        $departments = [
            '1' => 'Department A',
            '2' => 'Department B',
        ];
        $requesters = [
            '1' => 'Requester A',
            '2' => 'Requester B',
        ];
        $warehouses = [
            '1' => 'Warehouse A',
            '2' => 'Warehouse B',
        ];

        return view('return_voucher.create', compact(
            'voucher',
            'deliveryDocketNumber',
            'purchaseOrders',
            'suppliers',
            'buyers',
            'projects',
            'types',
            'departments',
            'requesters',
            'warehouses'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Find the voucher or fail
            $voucher = InventoryRiecevingVoucher::findOrFail($id);

            // Validate the request
            $validated = $request->validate([
                'delivery_docket_number' => 'required',
                'accounting_date' => 'required|date',
                'voucher_date' => 'required|date',
                'purchase_order' => 'required',
                'supplier_name' => 'required',
                'buyer' => 'required',
                'project' => 'required',
                'type' => 'required',
                'department' => 'required',
                'requester' => 'required',
                'deliverer' => 'required',
                'warehouse_name' => 'required',
                'expiry_date' => 'nullable|date',
                'invoice_no' => 'required',

                // Item details validation
                'item_description' => 'required',
                'item_warehouse_name' => 'required',
                'quantity' => 'required|numeric|min:1',
                'unit_price' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'lot_number' => 'nullable',
                'date_manufacture' => 'nullable|date',
                'item_expiry_date' => 'nullable|date',
                'amount' => 'required|numeric',

                // Totals validation
                'total_goods_value' => 'required|numeric',
                'value_of_inventory' => 'required|numeric',
                'total_tax_amount' => 'required|numeric',
                'total_payment' => 'required|numeric',

                'notes' => 'nullable',
            ]);

            DB::beginTransaction();

            // Update the voucher with validated data
            $voucher->update($validated);

            DB::commit();

            return redirect()
                ->route('inventory.return-voucher')
                ->with('success', 'Inventory voucher updated successfully.');

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->back()
                ->with('error', 'Inventory voucher not found.');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating inventory voucher: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voucher = InventoryRiecevingVoucher::findOrFail($id);

        $voucher->delete();

        return redirect()->back()->with('success', __('Voucher deleted successfully!'));
    }
}
