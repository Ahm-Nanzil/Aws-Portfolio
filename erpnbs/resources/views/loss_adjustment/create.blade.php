@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Loss & adjustment')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @if(isset($lossAdjustment))
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
                    @if(isset($lossAdjustment))
                        {{ Form::model($lossAdjustment, array('route' => array('inventory.loss-adjustment.update', $lossAdjustment->id), 'method' => 'post','enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @else
                        {{ Form::open(array('route' => ['inventory.loss-adjustment.store'], 'enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 form-group">
                                {{ Form::label('time', __('Time (Lost or Adjusted)'), ['class'=>'form-label']) }}
                                {{ Form::datetimeLocal('time', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-6 form-group">
                                {{ Form::label('type', __('Type'), ['class'=>'form-label']) }}
                                {{ Form::select('type', $types, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-6 form-group">
                                {{ Form::label('warehouse', __('warehouse'), ['class'=>'form-label']) }}
                                {{ Form::select('warehouse', $warehouses, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('Lot Number') }}</th>
                                            <th>{{ __('Expiration Date') }}</th>
                                            <th>{{ __('Quantity Available') }}</th>
                                            <th>{{ __('Quantity in Stock') }}</th>
                                            <th>{{ __('Settings') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ Form::text('item', null, ['class' => 'form-control', 'placeholder' => __('Description'), 'required' => 'required']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('lot_number', null, ['class' => 'form-control', 'placeholder' => __('Lot Number'), 'required' => 'required']) }}
                                            </td>
                                            <td>
                                                {{ Form::date('expiration_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('quantity_available', null, ['class' => 'form-control', 'readonly' => true]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('quantity_in_stock', null, ['class' => 'form-control', 'min' => '0', 'required' => 'required']) }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-light settings-btn" onclick="adjustStock()">
                                                    <i class="fas fa-cogs"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                {{ Form::label('reason', __('Reason'), ['class'=>'form-label']) }}
                                {{ Form::textarea('reason', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('Reason'), 'required' => 'required']) }}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
                        @if(isset($lossAdjustment))
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
