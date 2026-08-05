<?php

namespace Modules\Purchases\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Vender;
use App\Models\InventoryProductCategoriesMain;
use App\Models\ProductService;
use Modules\Purchases\Entities\VendorProducts;

class VendorItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $vendorProducts = VendorProducts::all();
        return view('purchases::vendor_items.index',compact('vendorProducts'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $vendors = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $groups = InventoryProductCategoriesMain::all()->pluck('name', 'id');
        $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->with(['taxes','unit','category'])->get()->pluck('name', 'id');

        $vendors->prepend('Select Vendor', '');
        $groups->prepend('Select Category', '');

        return view('purchases::vendor_items.item',compact('vendors','groups','products'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                'vendor' => 'required|integer',
                'categories' => 'nullable|integer',
                'products' => 'required|array',

            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $data = $request->all();
        $data['products'] = json_encode($data['products']);
        $data['created_by'] = \Auth::id();
        $data['datecreate'] = now();

        VendorProducts::create($data);


        return redirect()->route('vendor_items.index') // Replace with the correct route
            ->with('success', 'Vendor Products created successfully.');
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
        $vendorItem = VendorProducts::findOrFail($id);

        $vendors = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $groups = InventoryProductCategoriesMain::all()->pluck('name', 'id');
        $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->with(['taxes','unit','category'])->get()->pluck('name', 'id');

        $vendors->prepend('Select Vendor', '');
        $groups->prepend('Select Category', '');

        return view('purchases::vendor_items.item',compact('vendors','groups','products','vendorItem'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'vendor' => 'required|integer',
                'categories' => 'nullable|integer',
                'products' => 'required|array',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $vendorProduct = VendorProducts::findOrFail($id);

        $data = $request->all();
        $data['products'] = json_encode($data['products']);
        $data['updated_at'] = now();

        $vendorProduct->update($data);

        return redirect()->route('vendor_items.index') // Replace with the correct route
            ->with('success', 'Vendor Products updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $vendorProduct = VendorProducts::find($id);

        if (!$vendorProduct) {
            return redirect()->route('vendor_items.index')->with('error', 'Vendor Product not found.');
        }

        $vendorProduct->delete();

        return redirect()->route('vendor_items.index')->with('success', 'Vendor Product deleted successfully.');
    }

}
