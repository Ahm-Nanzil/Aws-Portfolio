<?php

namespace Modules\Purchases\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ProductService;
use Modules\Purchases\Entities\PurchaseRequest;
use Modules\Purchases\Entities\Estimate;
use Modules\Purchases\Entities\EstimateItems;

class QuotationsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $estimates = Estimate::all();

        return view('purchases::quotations.index',compact('estimates'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

        $purchaseRequests = [
            '1' => ' A',
            '2' => ' B',
        ];
        $saleEstimates = [
            '1' => ' A',
            '2' => ' B',
        ];
        $buyers = [
            '1' => ' A',
            '2' => ' B',
        ];
        $currencies = [
            '1' => ' A',
            '2' => ' B',
        ];
        $departments = [
            '1' => ' A',
            '2' => ' B',
        ];
        $saleInvoices = [
            '1' => ' A',
            '2' => ' B',
        ];
        $requesters = [
            '1' => ' A',
            '2' => ' B',
        ];
        $vendors = [
            '1' => ' A',
            '2' => ' B',
        ];
        $items = ProductService::where('created_by', \Auth::user()->creatorId())
        ->with(['taxes', 'unit', 'category'])
        ->get();
        return view('purchases::quotations.estimate',compact('purchaseRequests','buyers','currencies','vendors','items'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $items = json_decode($request->items, true);

        $validator = \Validator::make(
            array_merge($request->all(), ['items' => $items]),
            [
                'vendor_id' => 'required|integer',
                'purchase_request_id' => 'required|integer',
                'estimate_number' => 'required|string',
                'buyer_id' => 'required|integer',
                'currency' => 'required|string',
                'estimate_date' => 'required|date',
                'expiry_date' => 'required|date',
                'discount_type' => 'required|string',
                'vendor_note' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'items' => 'required|array',
                'items.*.item_id' => 'required|integer',
                'items.*.name' => 'required|string',
                'items.*.unit_price' => 'required|numeric',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.tax_rate' => 'required|numeric',
                'items.*.tax_value' => 'required|numeric',
                'items.*.subtotal_before_tax' => 'required|numeric',
                'items.*.subtotal_after_tax' => 'required|numeric',
                'items.*.discount_percentage' => 'required|numeric|min:0|max:100',
                'items.*.discount_money' => 'required|numeric',
                'items.*.total' => 'required|numeric',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $validatedData = $validator->validated();

        $estimate = Estimate::create([
            'vendor_id' => $validatedData['vendor_id'],
            'purchase_request_id' => $validatedData['purchase_request_id'],
            'estimate_number' => $validatedData['estimate_number'],
            'buyer_id' => $validatedData['buyer_id'],
            'currency' => $validatedData['currency'],
            'estimate_date' => $validatedData['estimate_date'],
            'expiry_date' => $validatedData['expiry_date'],
            'discount_type' => $validatedData['discount_type'],
            'subtotal' => array_sum(array_column($validatedData['items'], 'subtotal_before_tax')),
            'total_discount' => array_sum(array_column($validatedData['items'], 'discount_money')),
            'shipping_fee' => $request->input('shipping_fee', 0),
            'grand_total' => array_sum(array_column($validatedData['items'], 'total')) + $request->input('shipping_fee', 0),
            'vendor_note' => $validatedData['vendor_note'],
            'terms_conditions' => $validatedData['terms_conditions'],
            'created_by' => auth()->id(),
        ]);

        foreach ($validatedData['items'] as $item) {
            EstimateItems::create([
                'estimate_id' => $estimate->id,
                'item_id' => $item['item_id'],
                'item_name' => $item['name'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal_before_tax' => $item['subtotal_before_tax'],
                'tax' => $item['tax_rate'] > 0 ? '10%' : 'No Tax',
                'tax_value' => $item['tax_value'],
                'subtotal_after_tax' => $item['subtotal_after_tax'],
                'discount_percentage' => $item['discount_percentage'],
                'discount_money' => $item['discount_money'],
                'total' => $item['total'],
                'created_by' => auth()->id(),
            ]);
        }

        // dd($items);

        return redirect()->route('quotations.index')->with('success', 'Estimate created successfully!');
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('purchases::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('purchases::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
