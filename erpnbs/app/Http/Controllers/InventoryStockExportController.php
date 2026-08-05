<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryStockExport;
use App\Models\InventoryHistory;

class InventoryStockExportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $deliveryVouchers = InventoryStockExport::all();

        return view('stock_export.index',compact('deliveryVouchers'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Static Data for Dropdowns
        $invoices = [
            1 => 'INV-001',
            2 => 'INV-002',
            3 => 'INV-003',
        ];

        $customers = [
            1 => 'Customer A',
            2 => 'Customer B',
            3 => 'Customer C',
        ];

        $projects = [
            1 => 'Project Alpha',
            2 => 'Project Beta',
            3 => 'Project Gamma',
        ];

        $types = [
            1 => 'Type 1',
            2 => 'Type 2',
            3 => 'Type 3',
        ];

        $departments = [
            1 => 'Department X',
            2 => 'Department Y',
            3 => 'Department Z',
        ];

        $requesters = [
            1 => 'Requester 1',
            2 => 'Requester 2',
            3 => 'Requester 3',
        ];

        $salesPersons = [
            1 => 'John Doe',
            2 => 'Jane Smith',
            3 => 'Emily Johnson',
        ];

        $warehouses = [
            1 => 'Warehouse A',
            2 => 'Warehouse B',
            3 => 'Warehouse C',
        ];

        $items = [
            1 => 'Item 101 - Description 1',
            2 => 'Item 102 - Description 2',
            3 => 'Item 103 - Description 3',
        ];

        $taxes = [
            1 => 'No Tax',
            2 => '5%',
            3 => '10%',
            4 => '15%',
        ];


        // Static Non-Editable Fields
        $documentNumber = 'DOC-' . rand(1000, 9999); // Generate a random document number
        $availableQuantity = 100; // Example available quantity for selected item

        // Pass data to the view
        return view('stock_export.create', compact(
            'invoices',
            'customers',
            'projects',
            'types',
            'departments',
            'requesters',
            'salesPersons',
            'warehouses',
            'items',
            'taxes',
            'documentNumber',
            'availableQuantity'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validator = \Validator::make(
        $request->all(), [
            'accounting_date' => 'required',
            'voucher_date' => 'required',
            'invoice_id' => 'required',
            'customer_id' => 'required',
            'receiver' => 'required',
            'address' => 'required',
            'project_id' => 'required',
            'type_id' => 'required',
            'department_id' => 'required',
            'requester_id' => 'required',
            'sales_person_id' => 'required',
            'invoice_no' => 'required',
            'item_dropdown' => 'nullable',
            'items' => 'nullable|array',  // Handling the item rows
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.shipping_fee' => 'nullable|numeric|min:0',
            'items.*.total_payment' => 'nullable|numeric|min:0',
            'summary_subtotal' => 'nullable|numeric|min:0',
            'summary_discount' => 'nullable|numeric|min:0',
            'summary_shipping_fee' => 'nullable|numeric|min:0',
            'summary_total_payment' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',  // Notes can be optional and have a max length of 1000 characters
        ]
    );


    if ($validator->fails()) {
        $messages = $validator->getMessageBag();
        return redirect()->back()->with('error', $messages->first());
    }

    $data = $request->all();
    $data['created_by'] = \Auth::id();  // Store the user who created the stock export

    $items = $request->input('items', []);

    if (!empty($items)) {
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'item_id' => $item['item_id'], // Store item ID
                'warehouse_name' => $item['warehouse_name'],
                'quantity' => $item['quantity'],
                'sale_price' => $item['sale_price'],
                'tax_id' => $item['tax'] ?? null,
                'discount_percentage' => $item['discount_percentage'],
                'discount_amount' => $item['discount_amount'],
                'shipping_fee' => $item['shipping_fee'],
                'subtotal' => $item['subtotal'], // You can calculate this dynamically on the front-end
                'total_payment' => $item['total_payment'], // You can calculate this dynamically on the front-end
            ];
        }

        $data['items'] = json_encode($formattedItems);
    }

    $stockExport = InventoryStockExport::create($data);
     // Add entry to InventoryHistory
            InventoryHistory::create([
                'product_code' => 1,
                'warehouse_code' => 3,
                'warehouse_name' => 'warehouse 3',
                'voucher_date' => $request->voucher_date,
                'opening_stock' => 99,
                'closing_stock' => 99 - 23,
                'lot_number_quantity_sold' => 23,
                'expiry_date' => $request->expiry_date ?? null,
                'serial_number' => 123456789,
                'note' => $request->notes,
                'status' => 'cust delivery notes',
            ]);



    return redirect()->route('inventory.stock-export') // Adjust route as needed
        ->with('success', 'Stock Export created successfully.');
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
        $stockExport = InventoryStockExport::findOrFail($id);

         // Static Data for Dropdowns
         $invoices = [
            1 => 'INV-001',
            2 => 'INV-002',
            3 => 'INV-003',
        ];

        $customers = [
            1 => 'Customer A',
            2 => 'Customer B',
            3 => 'Customer C',
        ];

        $projects = [
            1 => 'Project Alpha',
            2 => 'Project Beta',
            3 => 'Project Gamma',
        ];

        $types = [
            1 => 'Type 1',
            2 => 'Type 2',
            3 => 'Type 3',
        ];

        $departments = [
            1 => 'Department X',
            2 => 'Department Y',
            3 => 'Department Z',
        ];

        $requesters = [
            1 => 'Requester 1',
            2 => 'Requester 2',
            3 => 'Requester 3',
        ];

        $salesPersons = [
            1 => 'John Doe',
            2 => 'Jane Smith',
            3 => 'Emily Johnson',
        ];

        $warehouses = [
            1 => 'Warehouse A',
            2 => 'Warehouse B',
            3 => 'Warehouse C',
        ];

        $items = [
            1 => 'Item 101 - Description 1',
            2 => 'Item 102 - Description 2',
            3 => 'Item 103 - Description 3',
        ];

        $taxes = [
            1 => 'No Tax',
            2 => '5%',
            3 => '10%',
            4 => '15%',
        ];


        // Static Non-Editable Fields
        $documentNumber = 'DOC-' . rand(1000, 9999); // Generate a random document number
        $availableQuantity = 100; // Example available quantity for selected item

        // Pass data to the view
        return view('stock_export.create', compact(
            'stockExport',
            'invoices',
            'customers',
            'projects',
            'types',
            'departments',
            'requesters',
            'salesPersons',
            'warehouses',
            'items',
            'taxes',
            'documentNumber',
            'availableQuantity'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                'accounting_date' => 'required',
                'voucher_date' => 'required',
                'invoice_id' => 'required',
                'customer_id' => 'required',
                'receiver' => 'required',
                'address' => 'required',
                'project_id' => 'required',
                'type_id' => 'required',
                'department_id' => 'required',
                'requester_id' => 'required',
                'sales_person_id' => 'required',
                'invoice_no' => 'required',
                'item_dropdown' => 'nullable',
                'items' => 'nullable|array',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.sale_price' => 'required|numeric|min:0',
                'items.*.subtotal' => 'nullable|numeric|min:0',
                'items.*.tax' => 'nullable|numeric|min:0',
                'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
                'items.*.discount_amount' => 'nullable|numeric|min:0',
                'items.*.shipping_fee' => 'nullable|numeric|min:0',
                'items.*.total_payment' => 'nullable|numeric|min:0',
                'summary_subtotal' => 'nullable|numeric|min:0',
                'summary_discount' => 'nullable|numeric|min:0',
                'summary_shipping_fee' => 'nullable|numeric|min:0',
                'summary_total_payment' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $stockExport = InventoryStockExport::find($id);

        if (!$stockExport) {
            return redirect()->route('inventory.stock-export')->with('error', 'Stock export not found.');
        }

        $stockExport->accounting_date = $request->input('accounting_date');
        $stockExport->voucher_date = $request->input('voucher_date');
        $stockExport->invoice_id = $request->input('invoice_id');
        $stockExport->customer_id = $request->input('customer_id');
        $stockExport->receiver = $request->input('receiver');
        $stockExport->address = $request->input('address');
        $stockExport->project_id = $request->input('project_id');
        $stockExport->type_id = $request->input('type_id');
        $stockExport->department_id = $request->input('department_id');
        $stockExport->requester_id = $request->input('requester_id');
        $stockExport->sales_person_id = $request->input('sales_person_id');
        $stockExport->invoice_no = $request->input('invoice_no');
        $stockExport->notes = $request->input('notes');

        // Handle the items
        if ($request->has('items')) {
            foreach ($request->input('items') as $itemData) {
                // Assuming you have a related model for items, like StockExportItem
                // Update each item (or create new ones, depending on your logic)
                foreach ($itemData as $key => $value) {
                    // Example: Assuming StockExportItem is a related model
                    // Update item or create a new one, based on your application's requirements
                    $stockExport->items()->updateOrCreate(
                        ['item_id' => $value['item_id']], // Assuming items have an item_id
                        [
                            'quantity' => $value['quantity'],
                            'sale_price' => $value['sale_price'],
                            'subtotal' => $value['subtotal'],
                            'tax' => $value['tax'],
                            'discount_percentage' => $value['discount_percentage'],
                            'discount_amount' => $value['discount_amount'],
                            'shipping_fee' => $value['shipping_fee'],
                            'total_payment' => $value['total_payment'],
                        ]
                    );
                }
            }
        }

        // Update the summary fields
        $stockExport->summary_subtotal = $request->input('summary_subtotal');
        $stockExport->summary_discount = $request->input('summary_discount');
        $stockExport->summary_shipping_fee = $request->input('summary_shipping_fee');
        $stockExport->summary_total_payment = $request->input('summary_total_payment');

        // Save the updated stock export
        $stockExport->save();

        // Redirect back with success message
        return redirect()->route('inventory.stock-export')->with('success', 'Stock export updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stockExport = InventoryStockExport::findOrFail($id);

        $stockExport->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }
}
