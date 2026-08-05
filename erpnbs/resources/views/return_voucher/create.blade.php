@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Reciept Return')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @if(isset($voucher))
    <li class="breadcrumb-item">{{__('Edit')}}</li>
    @else
    <li class="breadcrumb-item">{{__('Create')}}</li>
    @endif
@endsection
@section('action-btn')
    <div class="float-end">


    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    @if(isset($voucher))
                        {{ Form::model($voucher, array('route' => array('inventory.return-voucher.update', $voucher->id), 'method' => 'post','enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @else
                        {{ Form::open(array('route' => ['inventory.return-voucher.store'], 'enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 form-group">
                                {{ Form::label('delivery_docket_number', __('Delivery Docket Number'), ['class'=>'form-label']) }}
                                {{ Form::text('delivery_docket_number', $deliveryDocketNumber, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('accounting_date', __('Accounting Date'), ['class'=>'form-label']) }}
                                {{ Form::date('accounting_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('voucher_date', __('Voucher Date'), ['class'=>'form-label']) }}
                                {{ Form::date('voucher_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('purchase_order', __('Choose from a Purchase Order'), ['class'=>'form-label']) }}
                                {{ Form::select('purchase_order', $purchaseOrders, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('supplier_name', __('Supplier Name'), ['class'=>'form-label']) }}
                                {{ Form::select('supplier_name', $suppliers, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('buyer', __('Buyer'), ['class'=>'form-label']) }}
                                {{ Form::select('buyer', $buyers, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('project', __('Project'), ['class'=>'form-label']) }}
                                {{ Form::select('project', $projects, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('type', __('Type'), ['class'=>'form-label']) }}
                                {{ Form::select('type', $types, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('department', __('Department'), ['class'=>'form-label']) }}
                                {{ Form::select('department', $departments, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('requester', __('Requester'), ['class'=>'form-label']) }}
                                {{ Form::select('requester', $requesters, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('deliverer', __('Deliverer'), ['class'=>'form-label']) }}
                                {{ Form::text('deliverer', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('warehouse_name', __('Warehouse Name'), ['class'=>'form-label']) }}
                                {{ Form::select('warehouse_name', $warehouses, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('expiry_date', __('Expiry Date'), ['class'=>'form-label']) }}
                                {{ Form::date('expiry_date', null, ['class' => 'form-control']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('invoice_no', __('Invoice No.'), ['class'=>'form-label']) }}
                                {{ Form::text('invoice_no', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="row">
                            <!-- Items Table Section  -->
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('Warehouse Name') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Unit Price') }}</th>
                                            <th>{{ __('Tax') }}</th>
                                            <th>{{ __('Lot Number') }}</th>
                                            <th>{{ __('Date Manufacture') }}</th>
                                            <th>{{ __('Expiry Date') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Settings') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ Form::text('item_description', null, ['class' => 'form-control', 'placeholder' => __('Description')]) }}
                                                <small>{{ __('Commodity Notes') }}</small>
                                            </td>
                                            <td>
                                                {{ Form::select('item_warehouse_name', [
                                                    '' => __('None Selected'),
                                                    'Warehouse A' => 'Warehouse A',
                                                    'Warehouse B' => 'Warehouse B'
                                                ], null, ['class' => 'form-control select2']) }}
                                            </td>
                                            <td>
                                                {{ Form::number('quantity', null, ['class' => 'form-control', 'id' => 'quantity', 'placeholder' => __('Quantity')]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('unit_price', null, ['class' => 'form-control', 'id' => 'unit_price', 'placeholder' => __('Unit Price'), 'step' => '0.01']) }}
                                            </td>
                                            <td>
                                                {{ Form::select('tax', [
                                                    '0' => __('No Tax'),
                                                    '10' => __('10%'),
                                                    '15' => __('15%')
                                                ], null, ['class' => 'form-control', 'id' => 'tax']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('lot_number', null, ['class' => 'form-control', 'placeholder' => __('Lot Number')]) }}
                                            </td>
                                            <td>
                                                {{ Form::date('date_manufacture', null, ['class' => 'form-control']) }}
                                            </td>
                                            <td>
                                                {{ Form::date('item_expiry_date', null, ['class' => 'form-control']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('amount', null, ['class' => 'form-control', 'id' => 'amount', 'readonly' => true]) }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-light settings-btn" onclick="calculateTotal()">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-lg-8">

                            </div>
                            <!-- Totals Section  -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Total Summary') }}</h5>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Goods Value:') }}</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                {{ Form::text('total_goods_value', '0.00', ['class' => 'form-control', 'id' => 'total_goods_value', 'readonly' => true]) }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Value of Inventory:') }}</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                {{ Form::text('value_of_inventory', '0.00', ['class' => 'form-control', 'id' => 'value_of_inventory', 'readonly' => true]) }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Tax Amount:') }}</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                {{ Form::text('total_tax_amount', '0.00', ['class' => 'form-control', 'id' => 'total_tax_amount', 'readonly' => true]) }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Payment:') }}</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                {{ Form::text('total_payment', '0.00', ['class' => 'form-control', 'id' => 'total_payment', 'readonly' => true]) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Notes -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('Note') }}"></textarea>
                            </div>
                        </div>
                    </div>






                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
                        @if(isset($voucher))
                            <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
                        @else
                            <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
                        @endif
                    </div>
                    {{ Form::close() }}


                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function calculateTotal() {
        // Get input values
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
        const taxRate = parseFloat(document.getElementById('tax').value) || 0;

        // Calculate amount (before tax)
        const amount = quantity * unitPrice;
        document.getElementById('amount').value = amount.toFixed(2);

        // Calculate totals
        const totalGoodsValue = amount;
        const valueOfInventory = amount;
        const totalTaxAmount = (amount * taxRate) / 100;
        const totalPayment = totalGoodsValue + totalTaxAmount;

        // Update total fields
        document.getElementById('total_goods_value').value = totalGoodsValue.toFixed(2);
        document.getElementById('value_of_inventory').value = valueOfInventory.toFixed(2);
        document.getElementById('total_tax_amount').value = totalTaxAmount.toFixed(2);
        document.getElementById('total_payment').value = totalPayment.toFixed(2);
    }

    // Auto calculate when any input changes
    document.getElementById('quantity').addEventListener('input', calculateTotal);
    document.getElementById('unit_price').addEventListener('input', calculateTotal);
    document.getElementById('tax').addEventListener('change', calculateTotal);

    // Add validation before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        // Recalculate totals before submission to ensure latest values
        calculateTotal();

        // All total fields are already part of the form as input fields,
        // so they will be automatically submitted with the form
    });
    </script>
