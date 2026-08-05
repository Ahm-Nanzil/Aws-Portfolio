<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use App\Models\{
    InventoryGeneral,
    InventoryColors,
    InventoryProductTypes,
    InventoryProductCategoriesMain,
    InventoryProductCategoriesSub,
    InventoryProductCategoriesChild,
    InventoryModel,
    InventoryStyle,
    InventoryCustomFields,
    InventoryMinmax,
    InventoryApproval,
    User
};


class InventorySetupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventoryGenerals = InventoryGeneral::all();

        return view('inventorySetup.general', compact('inventoryGenerals'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $formName = $request->input('form_name');

        $inventoryGeneral = InventoryGeneral::where('name', $formName)->first();

        if (!$inventoryGeneral) {
            $inventoryGeneral = InventoryGeneral::create([
                'name' => $formName,
                'value' => json_encode($request->except(['form_name', '_token'])),
            ]);
        } else {
            $inventoryGeneral->update([
                'value' => json_encode($request->except(['form_name', '_token'])),
            ]);
        }

        return redirect()->back()->with('success', __('Updated successfully!'));
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
        //
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
    public function items()
    {
        //
    }
    public function colors()
    {
        $colors = InventoryColors::all();

        return view('inventorySetup.colors', compact('colors'));

    }
    public function colorsCreate()
    {
        return view('inventorySetup.colorsCU');

    }
    public function colorsStore(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'color_code' => 'required|string|unique:inventory_colors,color_code|max:255',
            'color_name' => 'required|string|max:255',
            'color_hex' => 'required|string|max:7', // Hex code length
            'order' => 'required|integer|min:0',
            'note' => 'nullable|string',
            'display' => 'nullable|boolean',
        ]);

        // Create a new color entry
        $color = new InventoryColors();
        $color->color_code = $request->input('color_code');
        $color->color_name = $request->input('color_name');
        $color->color_hex = $request->input('color_hex');
        $color->order = $request->input('order');
        $color->note = $request->input('note');
        $color->display = $request->has('display') ? $request->input('display') : false;
        $color->save();

        // Redirect back with success message
        return redirect()->back()->with('success', __('Color added successfully!'));
    }
    public function colorsEdit($id)
    {
        $color = InventoryColors::findOrFail($id);

        return view('inventorySetup.colorsCU', compact('color'));
    }
    public function colorsUpdate(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'color_code' => 'required|string|max:255|unique:inventory_colors,color_code,' . $id, // Ignore current record for uniqueness
            'color_name' => 'required|string|max:255',
            'color_hex' => 'required|string|max:7', // Hex color code
            'order' => 'required|integer|min:0',
            'note' => 'nullable|string',
            'display' => 'nullable|boolean',
        ]);

        // Find the color record by ID
        $color = InventoryColors::findOrFail($id);

        // Update the color data
        $color->color_code = $request->input('color_code');
        $color->color_name = $request->input('color_name');
        $color->color_hex = $request->input('color_hex');
        $color->order = $request->input('order');
        $color->note = $request->input('note');
        $color->display = $request->has('display') ? $request->input('display') : false;

        // Save the changes
        $color->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', __('Color Updated successfully!'));
    }

    public function colorsDestroy($id)
    {
        $color = InventoryColors::findOrFail($id);

        $color->delete();

        return redirect()->back()->with('success', __('Color deleted successfully!'));
    }

    public function productTypes()
    {
        $productTypes = InventoryProductTypes::all();

        return view('inventorySetup.product-types', compact('productTypes'));

    }
    public function productTypesCreate()
    {
        return view('inventorySetup.product-typesCU');

    }
    public function productTypesStore(Request $request)
    {
        $items = $request->get('items', []);

        foreach ($items as $item) {
            // Skip empty rows
            if (empty($item['code']) && empty($item['name'])) {
                continue;
            }

            InventoryProductTypes::create([
                'code'    => $item['code'] ?? null,
                'name'    => $item['name'] ?? null,
                'order'   => $item['order'] ?? 1,
                'display' => isset($item['display']) ? (bool) $item['display'] : false,
                'note'    => $item['note'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Product types saved successfully!');
    }

    public function productTypesEdit($id)
    {
        $productType = InventoryproductTypes::findOrFail($id);
        // dd($productType);

        return view('inventorySetup.product-typesCU', compact('productType'));
    }
    public function productTypesUpdate(Request $request, $id)
    {
        $productType = InventoryproductTypes::findOrFail($id);

        $data = $request->input('items')[0];  // Get the single item data

        $productType->code = $data['code'];
        $productType->name = $data['name'];
        $productType->order = $data['order'];
        $productType->display = isset($data['display']) ? $data['display'] : false;
        $productType->note = $data['note'];

        // Save the updated product type
        $productType->save();

        // Redirect or return response after update
        return redirect()->back()->with('success', 'Product types Updated successfully!');
    }

    public function productTypesDestroy($id)
    {
        $productType = InventoryproductTypes::findOrFail($id);

        $productType->delete();

        return redirect()->back()->with('success', __('Product types deleted successfully!'));
    }

    public function productCategories()
    {
        $mainCategories = InventoryProductCategoriesMain::all();
        $subCategories = InventoryProductCategoriesSub::all();
        $childCategories = InventoryProductCategoriesChild::all();

        return view('inventorySetup.product-categories', compact('mainCategories','subCategories','childCategories'));

    }
    public function productCategoriesMainCreate()
    {
        return view('inventorySetup.product-categories-MainCu');

    }
    public function productCategoriesMainStore(Request $request)
    {
        $items = $request->get('items', []);

        foreach ($items as $item) {
            // Skip empty rows
            if (empty($item['code']) && empty($item['name'])) {
                continue;
            }

            InventoryProductCategoriesMain::create([
                'code'    => $item['code'] ?? null,
                'name'    => $item['name'] ?? null,
                'order'   => $item['order'] ?? 1,
                'display' => isset($item['display']) ? (bool) $item['display'] : false,
                'note'    => $item['note'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Product Categories saved successfully!');
    }
    public function productCategoriesMainEdit($id)
    {
        $mainCategory = InventoryProductCategoriesMain::findOrFail($id);
        // dd($mainCategory);

        return view('inventorySetup.product-categories-MainCu', compact('mainCategory'));
    }
    public function productCategoriesMainUpdate(Request $request, $id)
    {
        $categories = InventoryProductCategoriesMain::findOrFail($id);

        $data = $request->input('items')[0];  // Get the single item data

        $categories->code = $data['code'];
        $categories->name = $data['name'];
        $categories->order = $data['order'];
        $categories->display = isset($data['display']) ? $data['display'] : false;
        $categories->note = $data['note'];

        // Save the updated product type
        $categories->save();

        // Redirect or return response after update
        return redirect()->back()->with('success', 'Product Category Updated successfully!');
    }

    public function productCategoriesMainDestroy($id)
    {
        $categories = InventoryProductCategoriesMain::findOrFail($id);

        $categories->delete();

        return redirect()->back()->with('success', __('Product Categories deleted successfully!'));
    }

    public function productCategoriesSubCreate()
    {
        $mainCategories = InventoryProductCategoriesMain::pluck('name', 'id');

        return view('inventorySetup.product-categories-SubCu',compact('mainCategories'));

    }
    public function productCategoriesSubStore(Request $request)
    {
        $items = $request->get('items', []);

        foreach ($items as $item) {
            // Skip empty rows
            if (empty($item['code']) && empty($item['name'])) {
                continue;
            }

            InventoryProductCategoriesSub::create([
                'main_category_id'    => $item['main_category_id'] ?? null,
                'code'    => $item['code'] ?? null,
                'name'    => $item['name'] ?? null,
                'order'   => $item['order'] ?? 1,
                'display' => isset($item['display']) ? (bool) $item['display'] : false,
                'note'    => $item['note'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Product Categories saved successfully!');
    }
    public function productCategoriesSubEdit($id)
    {
        $subCategory = InventoryProductCategoriesSub::findOrFail($id);
        $mainCategories = InventoryProductCategoriesMain::pluck('name', 'id');

        return view('inventorySetup.product-categories-SubCu', compact('subCategory','mainCategories'));
    }
    public function productCategoriesSubUpdate(Request $request, $id)
    {
        $categories = InventoryProductCategoriesSub::findOrFail($id);

        $data = $request->input('items')[0];  // Get the single item data

        $categories->main_category_id = $data['main_category_id'];
        $categories->code = $data['code'];
        $categories->name = $data['name'];
        $categories->order = $data['order'];
        $categories->display = isset($data['display']) ? $data['display'] : false;
        $categories->note = $data['note'];

        $categories->save();

        return redirect()->back()->with('success', 'Product Category Updated successfully!');
    }

    public function productCategoriesSubDestroy($id)
    {
        $categories = InventoryProductCategoriesSub::findOrFail($id);

        $categories->delete();

        return redirect()->back()->with('success', __('Product Categories deleted successfully!'));
    }

    public function productCategoriesChildCreate()
    {
        $mainCategories = InventoryProductCategoriesMain::pluck('name', 'id');
        $subCategories = InventoryProductCategoriesSub::all(['id', 'name', 'main_category_id']);

        return view('inventorySetup.product-categories-ChildCu',compact('mainCategories','subCategories'));

    }
    public function productCategoriesChildStore(Request $request)
    {
        $items = $request->get('items', []);

        foreach ($items as $item) {
            // Skip empty rows
            if (empty($item['code']) && empty($item['name'])) {
                continue;
            }

            InventoryProductCategoriesChild::create([
                'main_category_id'    => $item['main_category_id'] ?? null,
                'sub_category_id'    => $item['sub_category_id'] ?? null,
                'code'    => $item['code'] ?? null,
                'name'    => $item['name'] ?? null,
                'order'   => $item['order'] ?? 1,
                'display' => isset($item['display']) ? (bool) $item['display'] : false,
                'note'    => $item['note'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Product Categories saved successfully!');
    }
    public function productCategoriesChildEdit($id)
    {
        $childCategory = InventoryProductCategoriesChild::findOrFail($id);
        $mainCategories = InventoryProductCategoriesMain::pluck('name', 'id');
        $subCategories = InventoryProductCategoriesSub::all(['id', 'name', 'main_category_id']);

        return view('inventorySetup.product-categories-ChildCu', compact('childCategory','mainCategories','subCategories'));
    }
    public function productCategoriesChildUpdate(Request $request, $id)
    {
        $categories = InventoryProductCategoriesChild::findOrFail($id);

        $data = $request->input('items')[0];  // Get the single item data

        $categories->main_category_id = $data['main_category_id'];
        $categories->sub_category_id = $data['sub_category_id'];
        $categories->code = $data['code'];
        $categories->name = $data['name'];
        $categories->order = $data['order'];
        $categories->display = isset($data['display']) ? $data['display'] : false;
        $categories->note = $data['note'];

        $categories->save();

        return redirect()->back()->with('success', 'Product Category Updated successfully!');
    }

    public function productCategoriesChildDestroy($id)
    {
        $categories = InventoryProductCategoriesChild::findOrFail($id);

        $categories->delete();

        return redirect()->back()->with('success', __('Product Categories deleted successfully!'));
    }

    public function model()
    {
        $model = InventoryModel::all();

        return view('inventorySetup.model', compact('model'));

    }
    public function modelCreate()
    {
        return view('inventorySetup.modelCU');

    }
    public function modelStore(Request $request)
    {
        $items = $request->get('items', []);

        foreach ($items as $item) {
            // Skip empty rows
            if (empty($item['code']) && empty($item['name'])) {
                continue;
            }

            InventoryModel::create([
                'code'    => $item['code'] ?? null,
                'name'    => $item['name'] ?? null,
                'order'   => $item['order'] ?? 1,
                'display' => isset($item['display']) ? (bool) $item['display'] : false,
                'note'    => $item['note'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Model saved successfully!');
    }

    public function modelEdit($id)
    {
        $model = InventoryModel::findOrFail($id);
        // dd($model);

        return view('inventorySetup.modelCU', compact('model'));
    }
    public function modelUpdate(Request $request, $id)
    {
        $model = InventoryModel::findOrFail($id);

        $data = $request->input('items')[0];  // Get the single item data

        $model->code = $data['code'];
        $model->name = $data['name'];
        $model->order = $data['order'];
        $model->display = isset($data['display']) ? $data['display'] : false;
        $model->note = $data['note'];

        // Save the updated product type
        $model->save();

        // Redirect or return response after update
        return redirect()->back()->with('success', 'Model Updated successfully!');
    }

    public function modelDestroy($id)
    {
        $model = InventoryModel::findOrFail($id);

        $model->delete();

        return redirect()->back()->with('success', __('Model deleted successfully!'));
    }
    public function style()
    {
        $styles = InventoryStyle::all();

        return view('inventorySetup.style', compact('styles'));

    }
    public function styleCreate()
    {
        return view('inventorySetup.styleCU');

    }
    public function styleStore(Request $request)
    {
        $items = $request->get('items', []);

        foreach ($items as $item) {
            // Skip empty rows
            if (empty($item['code']) && empty($item['name'])) {
                continue;
            }

            Inventorystyle::create([
                'code'    => $item['code'] ?? null,
                'barcode'    => $item['barcode'] ?? null,
                'name'    => $item['name'] ?? null,
                'order'   => $item['order'] ?? 1,
                'display' => isset($item['display']) ? (bool) $item['display'] : false,
                'note'    => $item['note'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Style saved successfully!');
    }

    public function styleEdit($id)
    {
        $style = InventoryStyle::findOrFail($id);
        // dd($style);

        return view('inventorySetup.styleCU', compact('style'));
    }
    public function styleUpdate(Request $request, $id)
    {
        $style = Inventorystyle::findOrFail($id);

        $data = $request->input('items')[0];  // Get the single item data

        $style->code = $data['code'];
        $style->barcode = $data['barcode'];
        $style->name = $data['name'];
        $style->order = $data['order'];
        $style->display = isset($data['display']) ? $data['display'] : false;
        $style->note = $data['note'];

        // Save the updated product type
        $style->save();

        // Redirect or return response after update
        return redirect()->back()->with('success', 'Style Updated successfully!');
    }

    public function styleDestroy($id)
    {
        $style = InventoryStyle::findOrFail($id);

        $style->delete();

        return redirect()->back()->with('success', __('Style deleted successfully!'));
    }
    public function custom()
    {
        $custom = InventoryCustomFields::all();

        $fields = InventoryCustomFields::getFieldOptions();
        $warhouses = InventoryCustomFields::getWarehouseOptions();

        return view('inventorySetup.custom', compact('custom','fields','warhouses'));

    }
    public function customCreate()
    {
        $fields = InventoryCustomFields::getFieldOptions();
        $warhouses = InventoryCustomFields::getWarehouseOptions();
        return view('inventorySetup.customCU', compact('fields','warhouses'));

    }
    public function customStore(Request $request)
    {
        $validatedData = $request->validate([
            'field_id' => 'required|integer',
            'warehouses' => 'required|array',
            'warehouses.*' => 'string',
        ]);

        $customField = new InventoryCustomFields();
        $customField->field = $validatedData['field_id'];
        $customField->warhouses = json_encode($validatedData['warehouses']);
        $customField->save();

        return redirect()->back()->with('success', 'Custom saved successfully!');
    }


    public function customEdit($id)
    {
        $custom = InventoryCustomFields::findOrFail($id);
        // dd($custom);
        $fields = InventoryCustomFields::getFieldOptions();
        $warhouses = InventoryCustomFields::getWarehouseOptions();

        return view('inventorySetup.customCU', compact('custom','fields','warhouses'));
    }
    public function customUpdate(Request $request, $id)
    {
        $validatedData = $request->validate([
            'field_id' => 'required|integer', // Ensure field_id is an integer
            'warehouses' => 'required|array', // Ensure warehouses is an array
            'warehouses.*' => 'string', // Each warehouse must be a string
        ]);

        $customField = InventoryCustomFields::findOrFail($id);

        $customField->field = $validatedData['field_id'];
        $customField->warhouses = json_encode($validatedData['warehouses']); // Store warehouses as JSON
        $customField->save();

        return redirect()->back()->with('success', 'Custom updated successfully!');
    }


    public function customDestroy($id)
    {
        $custom = InventoryCustomFields::findOrFail($id);

        $custom->delete();

        return redirect()->back()->with('success', __('custom deleted successfully!'));
    }
    public function prefix()
    {
        $inventoryGenerals = InventoryGeneral::all();


        return view('inventorySetup.prefix', compact('inventoryGenerals'));

    }
    public function reset()
    {


        return view('inventorySetup.reset');

    }

    public function resetInventory(Request $request)
    {
        // Ensure this is an intentional action
        // $this->authorize('admin'); // Optional: Restrict to admin users

        try {
            // Use DB::statement('SET FOREIGN_KEY_CHECKS=0') if there are foreign key constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Truncate all specified tables
            InventoryGeneral::truncate();
            InventoryColors::truncate();
            InventoryProductTypes::truncate();
            InventoryProductCategoriesMain::truncate();
            InventoryProductCategoriesSub::truncate();
            InventoryProductCategoriesChild::truncate();
            InventoryModel::truncate();
            InventoryStyle::truncate();
            InventoryCustomFields::truncate();
            InventoryMinmax::truncate();
            InventoryApproval::truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->back()->with('success', 'Inventory data has been reset successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while resetting inventory data.');
        }
    }
    public function minmax()
    {
        $inventoryData = InventoryMinmax::all(['id','commodity_code', 'commodity_name', 'sku_code', 'min_inventory_value', 'max_inventory_qty']);

        // Convert data to array for Handsontable
        $formattedData = $inventoryData->map(function ($item) {
            return [
                $item->id,
                $item->commodity_code,
                $item->commodity_name,
                $item->sku_code,
                $item->min_inventory_value,
                $item->max_inventory_qty
            ];
        })->toArray();

        // dd($formattedData);

        return view('inventorySetup.minmax', ['tableData' => $formattedData]);

    }
    public function minmaxSave(Request $request)
    {
        $tableData = json_decode($request->table_data, true);

        // dd($tableData);
        foreach ($tableData as $row) {
            // Skip empty rows
            if (empty($row[1]) && empty($row[2]) && empty($row[3])) {
                continue;
            }

            // Check if the row has an ID (existing record)
            if (!empty($row[0])) {
                // Update existing record
                InventoryMinmax::where('id', $row[0])->update([
                    'commodity_code' => $row[1],
                    'commodity_name' => $row[2],
                    'sku_code' => $row[3],
                    'min_inventory_value' => $row[4] ?? 0,
                    'max_inventory_qty' => $row[5] ?? 0,
                ]);
            } else {
                // Insert new record
                InventoryMinmax::create([
                    'commodity_code' => $row[1],
                    'commodity_name' => $row[2],
                    'sku_code' => $row[3],
                    'min_inventory_value' => $row[4] ?? 0,
                    'max_inventory_qty' => $row[5] ?? 0,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Inventory data saved successfully!');
    }

    public function approval()
    {
        $approvals = InventoryApproval::all();

        $relatedOptions = InventoryApproval::getRelatedOptions();

        $actionOptions = InventoryApproval::getActionOptions();

        $staffOptions = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');




        return view('inventorySetup.approval', compact('approvals','relatedOptions', 'staffOptions', 'actionOptions'));

    }
    public function approvalCreate()
    {
        $relatedOptions = [
            'option1' => 'Related Option 1',
            'option2' => 'Related Option 2',
            'option3' => 'Related Option 3',
        ];

        $staffOptions = [
            'staff1' => 'Staff Member 1',
            'staff2' => 'Staff Member 2',
            'staff3' => 'Staff Member 3',
        ];

        $actionOptions = [
            'action1' => 'Action Type 1',
            'action2' => 'Action Type 2',
            'action3' => 'Action Type 3',
        ];

        // Pass to view
        return view('inventorySetup.approvalCU', compact('relatedOptions', 'staffOptions', 'actionOptions'));


    }
    public function approvalStore(Request $request)
    {

    // Validate the request data
    $validated = $request->validate([
        'subject' => 'required|string|max:255',
        'related' => 'required|string|max:255',
        'staff' => 'required',
        'staff.*' => 'required',
        'action' => 'required',
        'action.*' => 'required',
    ]);

    // Combine staff and actions into a single array
    $staffActions = [];
    foreach ($validated['staff'] as $index => $staff) {
        $staffActions[] = [
            'staff' => $staff,
            'action' => $validated['action'][$index] ?? null,
        ];
    }

    // Save the approval
    $approval = InventoryApproval::create([
        'subject' => $validated['subject'],
        'related' => $validated['related'],
        'staff_actions' => json_encode($staffActions),
    ]);



        return redirect()->back()->with('success', 'approval saved successfully!');
    }


    public function approvalEdit($id)
    {
        $approval = InventoryapprovalFields::findOrFail($id);
        // dd($approval);
        $fields = InventoryapprovalFields::getFieldOptions();
        $warhouses = InventoryapprovalFields::getWarehouseOptions();

        return view('inventorySetup.approvalCU', compact('approval','fields','warhouses'));
    }
    public function approvalUpdate(Request $request, $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'related' => 'required|string|max:255',
            'staff' => 'required',
            'staff.*' => 'required',
            'action' => 'required',
            'action.*' => 'required',
        ]);

        // Find the approval or fail
        $approval = InventoryApproval::findOrFail($id);

        // Combine staff and actions into a single array
        $staffActions = [];
        foreach ($validated['staff'] as $index => $staff) {
            $staffActions[] = [
                'staff' => $staff,
                'action' => $validated['action'][$index] ?? null,
            ];
        }

        // Update the approval
        $approval->update([
            'subject' => $validated['subject'],
            'related' => $validated['related'],
            'staff_actions' => json_encode($staffActions),
        ]);

        return redirect()->back()->with('success', 'Approval updated successfully!');
    }
    public function approvalDestroy($id)
    {
        $approval = InventoryApproval::findOrFail($id);

        $approval->delete();

        return redirect()->back()->with('success', __('Approval deleted successfully!'));
    }

    public function permission()
    {
        if(\Auth::user()->can('manage role'))
        {

            $roles = Role::where('created_by', '=', \Auth::user()->creatorId())->where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('inventorySetup.permission')->with('roles', $roles);
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }



}
