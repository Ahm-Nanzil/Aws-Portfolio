@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('General')}}</li>
@endsection
@push('css-page')
<script src="https://cdn.ckeditor.com/4.22.0/standard/ckeditor.js"></script>
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/44.1.0/classic/ckeditor.js"></script> --}}

@endpush
@push('script-page')

@endpush

@section('action-btn')

@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.inventory_setup')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body table-border-style">

                    <div class="container mt-4">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="true">
                                    {{ __('Items') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="receiving-delivery-tab" data-bs-toggle="tab" data-bs-target="#receiving-delivery" type="button" role="tab" aria-controls="receiving-delivery" aria-selected="false">
                                    {{ __('Receiving and Delivery') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="return-export-tab" data-bs-toggle="tab" data-bs-target="#return-export" type="button" role="tab" aria-controls="return-export" aria-selected="false">
                                    {{ __('Request for Return of Receipt-Export') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="packing-lists-tab" data-bs-toggle="tab" data-bs-target="#packing-lists" type="button" role="tab" aria-controls="packing-lists" aria-selected="false">
                                    {{ __('Packing Lists') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pdf-tab" data-bs-toggle="tab" data-bs-target="#pdf" type="button" role="tab" aria-controls="pdf" aria-selected="false">
                                    {{ __('PDF') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipments-tab" data-bs-toggle="tab" data-bs-target="#shipments" type="button" role="tab" aria-controls="shipments" aria-selected="false">
                                    {{ __('Shipments') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="serial-numbers-tab" data-bs-toggle="tab" data-bs-target="#serial-numbers" type="button" role="tab" aria-controls="serial-numbers" aria-selected="false">
                                    {{ __('Serial Numbers') }}
                                </button>
                            </li>
                        </ul>
                        @php
                            $items = $inventoryGenerals->firstWhere('name', 'items');
                            $recieve_delivery = $inventoryGenerals->firstWhere('name', 'recieve_delivery');
                            $return_export = $inventoryGenerals->firstWhere('name', 'return_export');
                            $packing = $inventoryGenerals->firstWhere('name', 'packing');
                            $pdf = $inventoryGenerals->firstWhere('name', 'pdf');
                            $shipments = $inventoryGenerals->firstWhere('name', 'shipments');
                            $serial_number = $inventoryGenerals->firstWhere('name', 'serial_number');

                            // dd($recieve_delivery);

                        @endphp

                        <!-- Tab Content -->
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="items" role="tabpanel" aria-labelledby="items-tab">

                                {{ isset($items) ? Form::model($items, ['route' => ['inventory-general.store', $items->id], 'method' => 'POST', 'class' => 'mt-4']) : Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4']) }}

                                {{ Form::hidden('form_name', 'items') }}

                                {{-- Check if itemss have an entry with 'items' in name --}}


                                <div class="mb-3">
                                    {{ Form::label('profit_rate', __('Default Profit Rate (%)'), ['class' => 'form-label']) }}
                                    {{ Form::number('profit_rate', $items ? json_decode($items->value)->profit_rate : 25, ['class' => 'form-control', 'required' => true]) }}
                                </div>

                                <div class="mb-3">
                                    {{ Form::label('sale_price_calculation', __('Sale Price Calculation'), ['class' => 'form-label']) }}
                                    <div class="form-check">
                                        {{ Form::radio('sale_price_calculation', 'cost_profit', $items && isset(json_decode($items->value)->sale_price_calculation) && json_decode($items->value)->sale_price_calculation == 'cost_profit', ['class' => 'form-check-input', 'id' => 'calc_cost_profit', 'required' => true]) }}
                                        {{ Form::label('calc_cost_profit', __('Calculate the selling price using the cost and profit rate'), ['class' => 'form-check-label']) }}
                                    </div>
                                    <div class="form-check">
                                        {{ Form::radio('sale_price_calculation', 'price_profit', $items && isset(json_decode($items->value)->sale_price_calculation) && json_decode($items->value)->sale_price_calculation == 'price_profit', ['class' => 'form-check-input', 'id' => 'calc_price_profit', 'required' => true]) }}
                                        {{ Form::label('calc_price_profit', __('Calculate the selling price using the selling price and profit rate'), ['class' => 'form-check-label']) }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {{ Form::label('fractional_portion', __('Fractional Portion (Number of digits rounded)'), ['class' => 'form-label']) }}
                                    {{ Form::number('fractional_portion', $items ? json_decode($items->value)->fractional_portion : 8, ['class' => 'form-control', 'required' => true]) }}
                                </div>

                                <div class="mb-3">
                                    {{ Form::label('integer_portion', __('Integer Portion (Number of digits rounded)'), ['class' => 'form-label']) }}
                                    {{ Form::number('integer_portion', $items ? json_decode($items->value)->integer_portion : 0, ['class' => 'form-control', 'required' => true]) }}
                                </div>

                                <div class="mb-3">
                                    {{ Form::label('barcodes', __('Barcodes'), ['class' => 'form-label']) }}
                                    <div class="form-check">
                                        {{ Form::checkbox('short_description', 1, $items && isset(json_decode($items->value)->short_description) ? json_decode($items->value)->short_description : false, ['class' => 'form-check-input', 'id' => 'short_description']) }}
                                        {{ Form::label('short_description', __('Display a short description below the printed barcode'), ['class' => 'form-check-label']) }}
                                    </div>
                                    <div class="form-check">
                                        {{ Form::checkbox('display_price', 1, $items && isset(json_decode($items->value)->display_price) ? json_decode($items->value)->display_price : false, ['class' => 'form-check-input', 'id' => 'display_price']) }}
                                        {{ Form::label('display_price', __('Display price when print barcode'), ['class' => 'form-check-label']) }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {{ Form::label('search_threshold', __('If there are more than X products, the search will be displayed on the product selection box'), ['class' => 'form-label']) }}
                                    {{ Form::number('search_threshold', $items ? json_decode($items->value)->search_threshold : 200, ['class' => 'form-control', 'required' => true]) }}
                                </div>

                                <div class="mb-3">
                                    {{ Form::submit(isset($items) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                                </div>

                                {{ Form::close() }}




                            </div>
                            <div class="tab-pane fade" id="receiving-delivery" role="tabpanel" aria-labelledby="receiving-delivery-tab">

                                {{ isset($recieve_delivery) ? Form::model($recieve_delivery, ['route' => ['inventory-general.store', $recieve_delivery->id], 'method' => 'POST', 'class' => 'mt-4']) : Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4']) }}

                                {{ Form::hidden('form_name', 'recieve_delivery') }} <!-- Hidden field for storing 'receiving-delivery' -->

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>General</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('allow_delete_voucher', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->allow_delete_voucher) ? json_decode($recieve_delivery->value)->allow_delete_voucher : false, ['class' => 'form-check-input', 'id' => 'allow_delete_voucher']) }}
                                            {{ Form::label('allow_delete_voucher', __('Allow delete of Inventory receipt voucher or Inventory delivery voucher (after approval)'), ['class' => 'form-check-label']) }}
                                        </div>

                                        <div class="mb-3">
                                            {{ Form::label('export_method', __('Export method'), ['class' => 'form-label']) }}
                                            {{ Form::select('export_method', ['method1' => __('Method 1'), 'method2' => __('Method 2')], $recieve_delivery && isset(json_decode($recieve_delivery->value)->export_method) ? json_decode($recieve_delivery->value)->export_method : null, ['class' => 'form-select', 'placeholder' => __('Select an export method')]) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Inventory Receiving Voucher</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('create_inventory_receipt', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->create_inventory_receipt) ? json_decode($recieve_delivery->value)->create_inventory_receipt : false, ['class' => 'form-check-input', 'id' => 'create_inventory_receipt']) }}
                                            {{ Form::label('create_inventory_receipt', __('Create an inventory receipt when the Purchase Order is approved'), ['class' => 'form-check-label']) }}
                                        </div>

                                        <div class="mb-3">
                                            {{ Form::label('auto_receive_warehouse', __('Select the warehouse to auto-receive inventory generated from purchase orders'), ['class' => 'form-label']) }}
                                            {{ Form::text('auto_receive_warehouse', $recieve_delivery && isset(json_decode($recieve_delivery->value)->auto_receive_warehouse) ? json_decode($recieve_delivery->value)->auto_receive_warehouse : '', ['class' => 'form-control', 'id' => 'auto_receive_warehouse']) }}
                                        </div>

                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('mandatory_purchase_order', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->mandatory_purchase_order) ? json_decode($recieve_delivery->value)->mandatory_purchase_order : false, ['class' => 'form-check-input', 'id' => 'mandatory_purchase_order']) }}
                                            {{ Form::label('mandatory_purchase_order', __('It is mandatory to select a Purchase order when entering an Inventory receipt'), ['class' => 'form-check-label']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Stock Export</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('create_delivery_note', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->create_delivery_note) ? json_decode($recieve_delivery->value)->create_delivery_note : false, ['class' => 'form-check-input', 'id' => 'create_delivery_note']) }}
                                            {{ Form::label('create_delivery_note', __('Create an inventory delivery voucher note when the Invoice is created'), ['class' => 'form-check-label']) }}
                                        </div>

                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('delete_inventory_on_cancel', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->delete_inventory_on_cancel) ? json_decode($recieve_delivery->value)->delete_inventory_on_cancel : false, ['class' => 'form-check-input', 'id' => 'delete_inventory_on_cancel']) }}
                                            {{ Form::label('delete_inventory_on_cancel', __('When canceling an Invoice, automatically delete the corresponding Inventory delivery generated from the Invoice'), ['class' => 'form-check-label']) }}
                                        </div>

                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('invoice_cancellation', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->invoice_cancellation) ? json_decode($recieve_delivery->value)->invoice_cancellation : false, ['class' => 'form-check-input', 'id' => 'invoice_cancellation']) }}
                                            {{ Form::label('invoice_cancellation', __('When unchecked, the invoice cancellation and the Inventory delivery will be automatically generated'), ['class' => 'form-check-label']) }}
                                        </div>

                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('mandatory_delivery_order', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->mandatory_delivery_order) ? json_decode($recieve_delivery->value)->mandatory_delivery_order : false, ['class' => 'form-check-input', 'id' => 'mandatory_delivery_order']) }}
                                            {{ Form::label('mandatory_delivery_order', __('It is mandatory to select a Purchase order when entering an Inventory delivery'), ['class' => 'form-check-label']) }}
                                        </div>

                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('notify_customers', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->notify_customers) ? json_decode($recieve_delivery->value)->notify_customers : false, ['class' => 'form-check-input', 'id' => 'notify_customers']) }}
                                            {{ Form::label('notify_customers', __('Notify customers when delivery status changes'), ['class' => 'form-check-label']) }}
                                        </div>

                                        <div class="form-check mb-3">
                                            {{ Form::checkbox('hide_shipping_fee', 1, $recieve_delivery && isset(json_decode($recieve_delivery->value)->hide_shipping_fee) ? json_decode($recieve_delivery->value)->hide_shipping_fee : false, ['class' => 'form-check-input', 'id' => 'hide_shipping_fee']) }}
                                            {{ Form::label('hide_shipping_fee', __('Hide shipping fee'), ['class' => 'form-check-label']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {{ Form::submit(isset($recieve_delivery) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                                </div>

                                {{ Form::close() }}
                            </div>

                            <div class="tab-pane fade" id="return-export" role="tabpanel" aria-labelledby="return-export-tab">
                                {{ isset($return_export) ?
                                    Form::model($return_export, ['route' => ['inventory-general.store', $return_export->id], 'method' => 'POST', 'class' => 'mt-4']) :
                                    Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4'])
                                }}

                                {{ Form::hidden('form_name', 'return_export') }} <!-- Hidden field to store form name -->

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>The warehouse receives the return order</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="col-6 form-group">

                                                {{
                                                    Form::select(
                                                        'rc[]',
                                                        [],
                                                        isset($return_export) && isset(json_decode($return_export->value)->rc)
                                                            ? json_decode($return_export->value)->rc
                                                            : null,
                                                        [
                                                            'class' => 'form-control select2',
                                                            'multiple' => 'multiple',
                                                            'id' => 'assigned-users-select',
                                                            'required' => false
                                                        ]
                                                    )
                                                }}

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Return Policy Information</h5>
                                    </div>
                                    <div class="card-body">
                                        {{ Form::textarea('return_policy', $return_export ? json_decode($return_export->value)->return_policy : null, ['class' => 'form-control', 'id' => 'returnPolicy']) }}

                                        <script>
                                            CKEDITOR.replace('returnPolicy', {
                                                height: 300,
                                                filebrowserUploadUrl: '/upload',
                                                filebrowserUploadMethod: 'form',
                                                toolbar: [
                                                    { name: 'document', items: ['Source', '-', 'NewPage', 'Preview', 'Print'] },
                                                    { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'Undo', 'Redo'] },
                                                    { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
                                                    '/',
                                                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting'] },
                                                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                                                    { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                                                    { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                                                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                                                    { name: 'tools', items: ['Maximize'] }
                                                ]
                                            });
                                        </script>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    {{ Form::submit(isset($return_export) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                                </div>

                                {{ Form::close() }}
                            </div>

                            <div class="tab-pane fade" id="packing-lists" role="tabpanel" aria-labelledby="packing-lists-tab">
                                {{ isset($packing) ?
                                    Form::model($packing, ['route' => ['inventory-general.store', $packing->id], 'method' => 'POST', 'class' => 'mt-4']) :
                                    Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4'])
                                }}

                                {{ Form::hidden('form_name', 'packing') }} <!-- Hidden field to store form name -->

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Custom Measurement Name</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="mb-3">
                                                <label for="custom_meter" class="form-label">Custom name for meter (m)</label>
                                                {{ Form::text('custom_meter', $packing ? json_decode($packing->value)->custom_meter : null, ['class' => 'form-control', 'id' => 'custom_meter']) }}
                                            </div>

                                            <div class="mb-3">
                                                <label for="custom_kilogram" class="form-label">Custom name for kilogram (kg)</label>
                                                {{ Form::text('custom_kilogram', $packing ? json_decode($packing->value)->custom_kilogram : null, ['class' => 'form-control', 'id' => 'custom_kilogram']) }}
                                            </div>

                                            <div class="mb-3">
                                                <label for="custom_cubic_meter" class="form-label">Custom name for cubic meter (m³)</label>
                                                {{ Form::text('custom_cubic_meter', $packing ? json_decode($packing->value)->custom_cubic_meter : null, ['class' => 'form-control', 'id' => 'custom_cubic_meter']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {{ Form::submit(isset($packing) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                                </div>

                                {{ Form::close() }}
                            </div>

                            <div class="tab-pane fade" id="pdf" role="tabpanel" aria-labelledby="pdf-tab">
                                {{ isset($pdf) ?
                                    Form::model($pdf, ['route' => ['inventory-general.store', $pdf->id], 'method' => 'POST', 'class' => 'mt-4']) :
                                    Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4'])
                                }}

                                {{ Form::hidden('form_name', 'pdf') }} <!-- Hidden field to store form name -->

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>In General</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('display_warehouse_info', 1, $pdf ? json_decode($pdf->value)->display_warehouse_info ?? false : false, ['class' => 'form-check-input', 'id' => 'display_warehouse_info']) }}
                                                <label class="form-check-label" for="display_warehouse_info">
                                                    Display "Warehouse Name", "Batch Number", "Sign Information" on Inventory Delivery PDF; Display "Sign Information" on Inventory Receipt PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('show_custom_item_fields', 1, $pdf ? json_decode($pdf->value)->show_custom_item_fields ?? false : false, ['class' => 'form-check-input', 'id' => 'show_custom_item_fields']) }}
                                                <label class="form-check-label" for="show_custom_item_fields">
                                                    Show custom item fields in PDF
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Export of Shares</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('display_price_details', 1, $pdf ? json_decode($pdf->value)->display_price_details ?? false : false, ['class' => 'form-check-input', 'id' => 'display_price_details']) }}
                                                <label class="form-check-label" for="display_price_details">
                                                    Display unit price, subtotal, total payment in stock delivery PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('show_pending_status', 1, $pdf ? json_decode($pdf->value)->show_pending_status ?? false : false, ['class' => 'form-check-input', 'id' => 'show_pending_status']) }}
                                                <label class="form-check-label" for="show_pending_status">
                                                    Show "Pending" on Inventory Delivery PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('short_form_pdf', 1, $pdf ? json_decode($pdf->value)->short_form_pdf ?? false : false, ['class' => 'form-check-input', 'id' => 'short_form_pdf']) }}
                                                <label class="form-check-label" for="short_form_pdf">
                                                    Short Form PDF
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Packing Lists</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('pdf_display_rate', 1, $pdf ? json_decode($pdf->value)->pdf_display_rate ?? false : false, ['class' => 'form-check-input', 'id' => 'pdf_display_rate']) }}
                                                <label class="form-check-label" for="pdf_display_rate">
                                                    PDF display rate
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('display_tax', 1, $pdf ? json_decode($pdf->value)->display_tax ?? false : false, ['class' => 'form-check-input', 'id' => 'display_tax']) }}
                                                <label class="form-check-label" for="display_tax">
                                                    Display tax in PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('show_subtotal', 1, $pdf ? json_decode($pdf->value)->show_subtotal ?? false : false, ['class' => 'form-check-input', 'id' => 'show_subtotal']) }}
                                                <label class="form-check-label" for="show_subtotal">
                                                    Show subtotal in PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('display_discount_percentage', 1, $pdf ? json_decode($pdf->value)->display_discount_percentage ?? false : false, ['class' => 'form-check-input', 'id' => 'display_discount_percentage']) }}
                                                <label class="form-check-label" for="display_discount_percentage">
                                                    Display discount percentage in PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('display_discount_amount', 1, $pdf ? json_decode($pdf->value)->display_discount_amount ?? false : false, ['class' => 'form-check-input', 'id' => 'display_discount_amount']) }}
                                                <label class="form-check-label" for="display_discount_amount">
                                                    Display discount amount in PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('display_total_payment', 1, $pdf ? json_decode($pdf->value)->display_total_payment ?? false : false, ['class' => 'form-check-input', 'id' => 'display_total_payment']) }}
                                                <label class="form-check-label" for="display_total_payment">
                                                    Display total payment in PDF
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                {{ Form::checkbox('view_summary', 1, $pdf ? json_decode($pdf->value)->view_summary ?? false : false, ['class' => 'form-check-input', 'id' => 'view_summary']) }}
                                                <label class="form-check-label" for="view_summary">
                                                    View summary in PDF
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {{ Form::submit(isset($pdf) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                                </div>

                                {{ Form::close() }}
                            </div>

                            <div class="tab-pane fade" id="shipments" role="tabpanel" aria-labelledby="shipments-tab">
                                {{ isset($shipments) ?
                                    Form::model($shipments, ['route' => ['inventory-general.store', $shipments->id], 'method' => 'POST', 'class' => 'mt-4']) :
                                    Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4'])
                                }}

                                {{ Form::hidden('form_name', 'shipments') }} <!-- Hidden field to store form name -->

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>In General</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                {{ Form::checkbox('view_shipping_info', 1, $shipments ? json_decode($shipments->value)->view_shipping_info ?? false : false, ['class' => 'form-check-input', 'id' => 'view_shipping_info']) }}
                                                <label class="form-check-label" for="view_shipping_info">
                                                    View Shipping Information in the Customer Portal
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {{ Form::submit(isset($shipments) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                                </div>

                                {{ Form::close() }}
                            </div>

                            <div class="tab-pane fade" id="serial-numbers" role="tabpanel" aria-labelledby="serial-numbers-tab">
                                {{ isset($serial_number) ?
                                    Form::model($serial_number, ['route' => ['inventory-general.store', $serial_number->id], 'method' => 'POST', 'class' => 'mt-4']) :
                                    Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4'])
                                }}

                                {{ Form::hidden('form_name', 'serial_number') }} <!-- Hidden field to store form name -->

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>In General</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                {{ Form::checkbox('products_by_series', 1, $serial_number ? json_decode($serial_number->value)->products_by_series ?? false : false, ['class' => 'form-check-input', 'id' => 'products_by_series']) }}
                                                <label class="form-check-label" for="products_by_series">Products by series</label>
                                            </div>

                                            <div class="form-check">
                                                {{ Form::checkbox('wh_serial_number_as_mandatory', 1, $serial_number ? json_decode($serial_number->value)->wh_serial_number_as_mandatory ?? false : false, ['class' => 'form-check-input', 'id' => 'wh_serial_number_as_mandatory']) }}
                                                <label class="form-check-label" for="wh_serial_number_as_mandatory">WH Serial Number as Mandatory</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {{ Form::submit(isset($serial_number) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                                </div>

                                {{ Form::close() }}
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


@endsection
