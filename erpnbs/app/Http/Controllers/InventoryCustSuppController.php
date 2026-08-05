<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryCustSupp;
use Rinvex\Country\CountryLoader;

class InventoryCustSuppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $custSuppLists = InventoryCustSupp::all();

        return view('cust_supp.index',compact('custSuppLists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $relatedTypes = [
            1 => 'Type A',
            2 => 'Type B',
            3 => 'Type C',
        ];

        $relatedData = [
            1 => 'Data X',
            2 => 'Data Y',
            3 => 'Data Z',
        ];

        $customers = [
            1 => 'Customer One',
            2 => 'Customer Two',
            3 => 'Customer Three',
        ];

        $returnTypes = [
            1 => 'Return A',
            2 => 'Return B',
            3 => 'Return C',
        ];
        $taxes = [
            1 => 'No Tax',
            2 => '5%',
            3 => '10%',
            4 => '15%',
        ];
        $orderReturnPrefix='ReReturn-10-';

        return view('cust_supp.create', compact('orderReturnPrefix','relatedTypes', 'relatedData', 'customers', 'returnTypes','taxes'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = \Validator::make($request->all(), [
            'related_type_id' => 'required',
            'related_data_id' => 'required',
            'order_number' => 'required|string',
            'order_date' => 'required|date',
            'customer_id' => 'required',
            'date_created' => 'nullable|date',
            'return_type_id' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'order_return_number' => 'nullable|string',
            'reason' => 'nullable|string',
            'admin_note' => 'nullable|string',
            'items' => 'nullable|array',
            'subtotal' => 'nullable',
            'additional_discount' => 'nullable',
            'total_discount' => 'nullable',
            'total_payment' => 'nullable',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // Prepare data for storage
        $data = $request->all();
        $data['created_by'] = \Auth::id();
        $data['order_return_number']= $request->order_return_number_prefix. $request->order_return_number_suffix;

        // Store in the database
        InventoryCustSupp::create([
            'related_type_id' => $data['related_type_id'],
            'related_data_id' => $data['related_data_id'],
            'order_number' => $data['order_number'],
            'order_date' => $data['order_date'],
            'customer_id' => $data['customer_id'],
            'date_created' => $data['date_created'],
            'return_type_id' => $data['return_type_id'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'order_return_number' => $data['order_return_number'],
            'reason' => $data['reason'],
            'admin_note' => $data['admin_note'],
            // 'subtotal' => $data['subtotal'],
            // 'additional_discount' => $data['additional_discount'],
            // 'total_discount' => $data['total_discount'],
            // 'total_payment' => $data['total_payment'],
            // 'items' => json_encode($data['items']),
            'created_by' => $data['created_by'],
        ]);

        // dd($data);

        return redirect()->route('inventory.cust-supp')->with('success', 'Order Return created successfully.');
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
        $orderReturn = InventoryCustSupp::findOrFail($id);

        $relatedTypes = [
            1 => 'Type A',
            2 => 'Type B',
            3 => 'Type C',
        ];

        $relatedData = [
            1 => 'Data X',
            2 => 'Data Y',
            3 => 'Data Z',
        ];

        $customers = [
            1 => 'Customer One',
            2 => 'Customer Two',
            3 => 'Customer Three',
        ];

        $returnTypes = [
            1 => 'Return A',
            2 => 'Return B',
            3 => 'Return C',
        ];
        $taxes = [
            1 => 'No Tax',
            2 => '5%',
            3 => '10%',
            4 => '15%',
        ];
        $orderReturnPrefix='ReReturn-10-';

        return view('cust_supp.create', compact('orderReturnPrefix','orderReturn','relatedTypes', 'relatedData', 'customers', 'returnTypes','taxes'));

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
        //
    }

    public function deCreate()
    {
        $relatedTypes = [
            1 => 'Type A',
            2 => 'Type B',
            3 => 'Type C',
        ];

        $relatedData = [
            1 => 'Data X',
            2 => 'Data Y',
            3 => 'Data Z',
        ];

        $vendors = [
            1 => 'vendor One',
            2 => 'vendor Two',
            3 => 'vendor Three',
        ];

        $returnTypes = [
            1 => 'Return A',
            2 => 'Return B',
            3 => 'Return C',
        ];
        $taxes = [
            1 => 'No Tax',
            2 => '5%',
            3 => '10%',
            4 => '15%',
        ];
        $orderReturnPrefix='DEReturn-10-';
        return view('cust_supp.deReturn', compact('orderReturnPrefix','relatedTypes', 'relatedData', 'vendors', 'returnTypes','taxes'));
    }
    public function deStore(Request $request)
    {
        // Validate the incoming request data
        $validator = \Validator::make($request->all(), [
            'related_type_id' => 'required',
            'related_data_id' => 'required',
            'order_number' => 'required|string',
            'order_date' => 'required|date',
            'vendor_id' => 'required',
            'date_created' => 'nullable|date',
            'return_type_id' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'order_return_number_suffix' => 'nullable|string',
            'reason' => 'nullable|string',
            'admin_note' => 'nullable|string',
            'items' => 'nullable|array',
            'subtotal' => 'nullable',
            'additional_discount' => 'nullable',
            'total_discount' => 'nullable',
            'total_payment' => 'nullable',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // Prepare data for storage
        $data = $request->all();
        $data['created_by'] = \Auth::id();
        $data['order_return_number']= $request->order_return_number_prefix. $request->order_return_number_suffix;

        // Store in the database
        InventoryCustSupp::create([
            'related_type_id' => $data['related_type_id'],
            'related_data_id' => $data['related_data_id'],
            'order_number' => $data['order_number'],
            'order_date' => $data['order_date'],
            'vendor_id' => $data['vendor_id'],
            'date_created' => $data['date_created'],
            'return_type_id' => $data['return_type_id'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'order_return_number' => $data['order_return_number'],
            'reason' => $data['reason'],
            'admin_note' => $data['admin_note'],
            // 'subtotal' => $data['subtotal'],
            // 'additional_discount' => $data['additional_discount'],
            // 'total_discount' => $data['total_discount'],
            // 'total_payment' => $data['total_payment'],
            // 'items' => json_encode($data['items']),
            'created_by' => $data['created_by'],
        ]);

        // dd($data);

        return redirect()->route('inventory.cust-supp')->with('success', 'Order Return created successfully.');
    }
}
