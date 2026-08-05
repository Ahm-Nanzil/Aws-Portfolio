<?php

namespace Modules\Purchases\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ProductService;
use Modules\Purchases\Entities\PurchaseRequest;
use Modules\Purchases\Entities\PurchaseItems;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $purchaseRequests = PurchaseRequest::all();

        return view('purchases::purchase_request.index',compact('purchaseRequests'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $purchaseRequestCode="123";
        $projects = [
            '1' => ' A',
            '2' => ' B',
        ];
        $saleEstimates = [
            '1' => ' A',
            '2' => ' B',
        ];
        $types = [
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
        return view('purchases::purchase_request.purchase',compact('purchaseRequestCode','projects','saleEstimates','types','currencies','departments','saleInvoices','requesters','vendors','items'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'purchase_request_code' => 'required',
            'purchase_request_name' => 'required|string|max:255',
            'project_id' => 'required',
            'sale_estimate_id' => 'required',
            'type_id' => 'required',
            'currency' => 'required',
            'department_id' => 'required',
            'sale_invoice_id' => 'required',
            'requester_id' => 'required',
            'vendor_id' => 'required',
            'description' => 'nullable',
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.name' => 'required',
            'items.*.price' => 'required',
            'items.*.quantity' => 'nullable',
            'items.*.subtotal' => 'nullable',
            'items.*.tax' => 'nullable',
            'items.*.tax_value' => 'nullable',
            'items.*.total' => 'nullable',
        ]);

        $purchaseRequest = PurchaseRequest::create([
            'purchase_request_code' => $validatedData['purchase_request_code'],
            'purchase_request_name' => $validatedData['purchase_request_name'],
            'project_id' => $validatedData['project_id'],
            'sale_estimate_id' => $validatedData['sale_estimate_id'],
            'type_id' => $validatedData['type_id'],
            'currency' => $validatedData['currency'],
            'department_id' => $validatedData['department_id'],
            'sale_invoice_id' => $validatedData['sale_invoice_id'],
            'requester_id' => $validatedData['requester_id'],
            'vendor_id' => $validatedData['vendor_id'],
            'description' => $validatedData['description'],
            'created_by' =>   \Auth::id(),
        ]);

        if ($validatedData['items']) {
            foreach ($validatedData['items'] as $item) {
                PurchaseItems::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'item_id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'] ?? 1, // Default to 1 if no quantity is provided
                    'subtotal' => $item['subtotal'] ?? ($item['price'] * ($item['quantity'] ?? 1)),
                    'tax' => $item['tax'] ?? 'No Tax',
                    'tax_value' => $item['tax_value'] ?? 0,
                    'total' => $item['total'] ?? ($item['subtotal'] + $item['tax_value']),
                    'created_by' =>   \Auth::id(),

                ]);
            }
        }

        return redirect()->route('purchase_request.index')->with('success', 'Purchase Request created successfully!');
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
        $purchaseRequest = PurchaseRequest::findOrFail($id);

        $purchaseRequestCode="123";
        $projects = [
            '1' => ' A',
            '2' => ' B',
        ];
        $saleEstimates = [
            '1' => ' A',
            '2' => ' B',
        ];
        $types = [
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
        return view('purchases::purchase_request.purchase',compact('purchaseRequest','purchaseRequestCode','projects','saleEstimates','types','currencies','departments','saleInvoices','requesters','vendors','items'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'purchase_request_code' => 'required',
            'purchase_request_name' => 'required|string|max:255',
            'project_id' => 'required',
            'sale_estimate_id' => 'required',
            'type_id' => 'required',
            'currency' => 'required',
            'department_id' => 'required',
            'sale_invoice_id' => 'required',
            'requester_id' => 'required',
            'vendor_id' => 'required',
            'description' => 'nullable',
        ]);

        $purchaseRequest = PurchaseRequest::findOrFail($id);

        $purchaseRequest->update([
            'purchase_request_code' => $validatedData['purchase_request_code'],
            'purchase_request_name' => $validatedData['purchase_request_name'],
            'project_id' => $validatedData['project_id'],
            'sale_estimate_id' => $validatedData['sale_estimate_id'],
            'type_id' => $validatedData['type_id'],
            'currency' => $validatedData['currency'],
            'department_id' => $validatedData['department_id'],
            'sale_invoice_id' => $validatedData['sale_invoice_id'],
            'requester_id' => $validatedData['requester_id'],
            'vendor_id' => $validatedData['vendor_id'],
            'description' => $validatedData['description'],
            'created_by' => \Auth::id(),
        ]);

        return redirect()->route('purchase_request.index')->with('success', 'Purchase Request updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $purchaseRequest = PurchaseRequest::find($id);

        if (!$purchaseRequest) {
            return redirect()->route('purchase_request.index')->with('error', 'Not found.');
        }

        $purchaseRequest->delete();

        return redirect()->route('purchase_request.index')->with('success', 'Deleted successfully.');
    }
}
