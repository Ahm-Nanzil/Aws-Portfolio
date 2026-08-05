@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Prefix')}}</li>
@endsection
@push('css-page')

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
            <div class="card mb-3">

                <div class="row">

                                            @php
                                                $prefix = $inventoryGenerals->firstWhere('name', 'prefix');
                                                // $ = json_decode($inventory_settings->value, true);



                                            @endphp
                    {{ isset($prefix) ? Form::model($prefix, ['route' => ['inventory-general.store', $prefix->id], 'method' => 'POST', 'class' => 'mt-4']) : Form::open(['route' => 'inventory-general.store', 'class' => 'mt-4']) }}

                    {{ Form::hidden('form_name', 'prefix') }}

                    <!-- Inventory Received Note -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Inventory Received Note</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{ Form::label('inventory_received_prefix', __('Inventory Received Number Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('inventory_received_prefix', $prefix ? json_decode($prefix->value)->inventory_received_prefix : 'NK-', ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('next_inventory_received_number', __('Next Inventory Received Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_inventory_received_number', $prefix ? json_decode($prefix->value)->next_inventory_received_number : 332, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Delivery Note -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Inventory Delivery Note</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{ Form::label('inventory_delivery_prefix', __('Inventory Delivery Number Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('inventory_delivery_prefix', $prefix ? json_decode($prefix->value)->inventory_delivery_prefix : 'XK-', ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('next_inventory_delivery_number', __('Next Inventory Delivery Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_inventory_delivery_number', $prefix ? json_decode($prefix->value)->next_inventory_delivery_number : 669, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Internal Delivery Note -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Internal Delivery Note</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{ Form::label('internal_delivery_prefix', __('Internal Delivery Number Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('internal_delivery_prefix', $prefix ? json_decode($prefix->value)->internal_delivery_prefix : 'ID-', ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('next_internal_delivery_number', __('Next Internal Delivery Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_internal_delivery_number', $prefix ? json_decode($prefix->value)->next_internal_delivery_number : 23, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Packing List -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Packing List</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{ Form::label('packing_list_prefix', __('Packing List Number Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('packing_list_prefix', $prefix ? json_decode($prefix->value)->packing_list_prefix : 'PL-', ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('next_packing_list_number', __('Next Packing List Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_packing_list_number', $prefix ? json_decode($prefix->value)->next_packing_list_number : 41, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Receiving-Exporting Return Order -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Receiving-Exporting Return Order</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{ Form::label('receipt_return_prefix', __('Receipt Return Order Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('receipt_return_prefix', $prefix ? json_decode($prefix->value)->receipt_return_prefix : 'ReReturn-', ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('next_receipt_return_number', __('Next Receipt Return Order Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_receipt_return_number', $prefix ? json_decode($prefix->value)->next_receipt_return_number : 20, ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('delivery_return_prefix', __('Delivery Return Purchasing Goods Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('delivery_return_prefix', $prefix ? json_decode($prefix->value)->delivery_return_prefix : 'DEReturn-', ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('next_delivery_return_number', __('Next Delivery Return Purchasing Goods Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_delivery_return_number', $prefix ? json_decode($prefix->value)->next_delivery_return_number : 7, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <!-- SKU Prefix -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>SKU Prefix</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{ Form::label('sku_prefix', __('SKU Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('sku_prefix', $prefix ? json_decode($prefix->value)->sku_prefix : 'SKU-', ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Lot Numbers -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Lot Numbers</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                {{ Form::checkbox('auto_generate_batch', 1, $prefix ? json_decode($prefix->value)->auto_generate_batch : false, ['class' => 'form-check-input', 'id' => 'auto_generate_batch']) }}
                                {{ Form::label('auto_generate_batch', __('Automatically generate batch numbers'), ['class' => 'form-check-label']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('lot_number_prefix', __('Lot Number Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('lot_number_prefix', $prefix ? json_decode($prefix->value)->lot_number_prefix : 'LOT', ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                {{ Form::label('next_lot_number', __('Next Lot Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_lot_number', $prefix ? json_decode($prefix->value)->next_lot_number : 492, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Serial Numbers -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Serial Numbers</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{ Form::label('next_serial_number', __('Next Serial Number'), ['class' => 'form-label']) }}
                                {{ Form::number('next_serial_number', $prefix ? json_decode($prefix->value)->next_serial_number : 1592597, ['class' => 'form-control']) }}
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Serial Number Format') }}</label>
                                <div class="form-check">
                                    {{ Form::radio('serial_number_format', 'number_based', $prefix ? json_decode($prefix->value)->serial_number_format === 'number_based' : false, ['class' => 'form-check-input', 'id' => 'number_based']) }}
                                    {{ Form::label('number_based', __('Number Based (0000001)'), ['class' => 'form-check-label']) }}
                                </div>
                                <div class="form-check">
                                    {{ Form::radio('serial_number_format', 'date_based', $prefix ? json_decode($prefix->value)->serial_number_format === 'date_based' : false, ['class' => 'form-check-input', 'id' => 'date_based']) }}
                                    {{ Form::label('date_based', __('Date Based (YYYYMMDD000001)'), ['class' => 'form-check-label']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        {{ Form::submit(isset($prefix) ? __('Update') : __('Save'), ['class' => 'btn btn-primary']) }}
                    </div>

                    {{ Form::close() }}




                </div>
            </div>
        </div>
    </div>
@endsection
