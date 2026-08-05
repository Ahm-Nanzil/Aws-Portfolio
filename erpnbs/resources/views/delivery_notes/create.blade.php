@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Delivery Notes')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @if(isset($internalDelivery))
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
                    @if(isset($internalDelivery))
                        {{ Form::model($internalDelivery, array('route' => array('inventory.delivery-notes.update', $internalDelivery->id), 'method' => 'post','enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @else
                        {{ Form::open(array('route' => ['inventory.delivery-notes.store'], 'enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 form-group">
                                {{ Form::label('internal_delivery_name', __('Internal Delivery Note Name'), ['class'=>'form-label']) }}
                                {{ Form::text('internal_delivery_name', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-6 form-group">
                                {{ Form::label('internal_delivery_number', __('Internal Delivery Note Number'), ['class'=>'form-label']) }}
                                {{ Form::text('internal_delivery_number', $internalDeliveryNumber, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                            </div>

                            <div class="col-4 form-group">
                                {{ Form::label('accounting_date', __('Accounting Date'), ['class'=>'form-label']) }}
                                {{ Form::date('accounting_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-4 form-group">
                                {{ Form::label('voucher_date', __('Voucher Date'), ['class'=>'form-label']) }}
                                {{ Form::date('voucher_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-4 form-group">
                                {{ Form::label('deliverer', __('Deliverer'), ['class'=>'form-label']) }}
                                {{ Form::select('deliverer', $deliverers, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('From Stock Name') }}</th>
                                            <th>{{ __('To Stock Name') }}</th>
                                            <th>{{ __('Available Quantity') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Unit Price') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Settings') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ Form::text('item_description', null, ['class' => 'form-control', 'placeholder' => __('Item'), 'required' => 'required']) }}
                                            </td>
                                            <td>
                                                {{ Form::select('from_stock_name', $stockLocations, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                                            </td>
                                            <td>
                                                {{ Form::select('to_stock_name', $stockLocations, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('available_quantity', null, ['class' => 'form-control', 'readonly' => true]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('quantity', null, ['class' => 'form-control', 'id' => 'quantity', 'required' => 'required', 'min' => '1']) }}
                                            </td>
                                            <td>
                                                {{ Form::number('unit_price', null, ['class' => 'form-control', 'id' => 'unit_price', 'required' => 'required', 'min' => '0', 'step' => '0.01']) }}
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
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Total Summary') }}</h5>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Amount:') }}</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                {{ Form::text('total_amount', '0.00', ['class' => 'form-control', 'id' => 'total_amount', 'readonly' => true]) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mt-3">
                            <div class="col-12">
                                {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('Note')]) }}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
                        @if(isset($internalDelivery))
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
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;

        // Calculate amount
        const amount = quantity * unitPrice;
        document.getElementById('amount').value = amount.toFixed(2);

        // Update total amount
        document.getElementById('total_amount').value = amount.toFixed(2);
    }

    // Auto calculate when quantity or unit price changes
    document.getElementById('quantity').addEventListener('input', calculateTotal);
    document.getElementById('unit_price').addEventListener('input', calculateTotal);

    // Check available quantity when from_stock_name changes
    document.querySelector('select[name="from_stock_name"]').addEventListener('change', async function() {
        const stockId = this.value;
        const itemId = document.querySelector('input[name="item_description"]').value;

        if (stockId && itemId) {
            try {
                const response = await fetch(`/api/stock-quantity/${stockId}/${itemId}`);
                const data = await response.json();
                document.querySelector('input[name="available_quantity"]').value = data.quantity;
            } catch (error) {
                console.error('Error fetching available quantity:', error);
            }
        }
    });
    </script>
