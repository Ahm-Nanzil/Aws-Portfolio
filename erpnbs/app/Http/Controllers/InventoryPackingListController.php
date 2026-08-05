<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryPackingList;
use Rinvex\Country\CountryLoader;

class InventoryPackingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packingLists = InventoryPackingList::all();
        $countries = collect(CountryLoader::countries())->pluck('name.common', 'iso_3166_1_alpha2');

        return view('packing_list.index',compact('packingLists','countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Static data for dropdowns and other fields
        $stockExports = [
            'SE001' => 'Stock Export 001',
            'SE002' => 'Stock Export 002',
            'SE003' => 'Stock Export 003',
        ];

        $customers = [
            'CUST001' => 'Customer 001',
            'CUST002' => 'Customer 002',
            'CUST003' => 'Customer 003',
        ];

        // Example packing list number
        $packingListNumber = 'PLN-12345';
        $countries = collect(CountryLoader::countries())->pluck('name', 'iso_3166_1_alpha2');
        // dd($countries);
        $taxes = [
            1 => 'No Tax',
            2 => '5%',
            3 => '10%',
            4 => '15%',
        ];

        return view('packing_list.create', compact('stockExports', 'taxes','customers', 'packingListNumber','countries'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = \Validator::make(
            $request->all(), [
                'stock_export_id' => 'required',
                'customer_id' => 'required',
                'bill_to' => 'nullable|string',
                'ship_to' => 'nullable|string',
                'packing_list_number' => 'nullable|string',
                'width' => 'nullable|numeric',
                'height' => 'nullable|numeric',
                'length' => 'nullable|numeric',
                'weight' => 'nullable|numeric',
                'volume' => 'nullable|numeric',
                'client_note' => 'nullable|string',
                'admin_note' => 'nullable|string',
                'items' => 'nullable|array', // Assuming items are submitted as an array
                'subtotal' => 'required|numeric',
                'additional_discount' => 'nullable|numeric',
                'total_discount' => 'nullable|numeric',
                'shipping_fee' => 'nullable|numeric',
                'total_payment' => 'required|numeric',
            ]
        );

        // Check if validation fails
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $data = $request->all();
        $data['created_by'] = \Auth::id(); // Assuming the logged-in user is storing the data
        $data['total_discount'] = $request->discount_amount; // Assuming the logged-in user is storing the data

        InventoryPackingList::create($data);
        // dd($data);

        return redirect()->route('inventory.packing-list')
            ->with('success', 'Packing List created successfully.');
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
        $packing = InventoryPackingList::findOrFail($id);

       // Static data for dropdowns and other fields
       $stockExports = [
        'SE001' => 'Stock Export 001',
        'SE002' => 'Stock Export 002',
        'SE003' => 'Stock Export 003',
    ];

    $customers = [
        'CUST001' => 'Customer 001',
        'CUST002' => 'Customer 002',
        'CUST003' => 'Customer 003',
    ];

    // Example packing list number
    $packingListNumber = 'PLN-12345';
    $countries = collect(CountryLoader::countries())->pluck('name', 'iso_3166_1_alpha2');
    // dd($countries);
    $taxes = [
        1 => 'No Tax',
        2 => '5%',
        3 => '10%',
        4 => '15%',
    ];

    return view('packing_list.create', compact('packing','taxes','stockExports', 'customers', 'packingListNumber','countries'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $packingList = InventoryPackingList::find($id);

        if (!$packingList) {
            return redirect()->route('inventory.packing-list')
                ->with('error', 'Packing list not found.');
        }

        $validator = \Validator::make(
            $request->all(), [
                'stock_export_id' => 'required',
                'customer_id' => 'required',
                'bill_to' => 'nullable|string',
                'ship_to' => 'nullable|string',
                'packing_list_number' => 'nullable|string',
                'width' => 'nullable|numeric',
                'height' => 'nullable|numeric',
                'length' => 'nullable|numeric',
                'weight' => 'nullable|numeric',
                'volume' => 'nullable|numeric',
                'client_note' => 'nullable|string',
                'admin_note' => 'nullable|string',
                'items' => 'nullable|array',
                'subtotal' => 'required|numeric',
                'additional_discount' => 'nullable|numeric',
                'total_discount' => 'nullable|numeric',
                'shipping_fee' => 'nullable|numeric',
                'total_payment' => 'required|numeric',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $data = $request->all();
        $data['total_discount'] = $request->discount_amount; // Assuming the logged-in user is storing the data

        // dd($data);
        $packingList->update($data);


        if ($request->has('items')) {
            foreach ($request->items as $key => $item) {

                $packingList->items()->updateOrCreate(
                    ['id' => $item['id'] ?? null],
                    $item
                );
            }
        }

        return redirect()->route('inventory.packing-list')
            ->with('success', 'Packing List updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $packingList = InventoryPackingList::findOrFail($id);

        $packingList->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }
}
