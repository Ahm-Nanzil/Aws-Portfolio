@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Cust Delivery Notes')}}
@endsection
@push('script-page')
<script>
    function calculateTotals() {
        // Retrieve values from the input fields
        const quantity = parseFloat(document.querySelector('input[name="quantity"]').value) || 0;
        const salePrice = parseFloat(document.querySelector('input[name="sale_price"]').value) || 0;
        const taxRate = parseFloat(document.querySelector('select[name="tax"]').value) || 0;
        const discountPercentage = parseFloat(document.querySelector('input[name="discount_percentage"]').value) || 0;
        const shippingFee = parseFloat(document.querySelector('input[name="shipping_fee"]').value) || 0;

        // Calculate subtotal (quantity * sale price)
        const subtotal = quantity * salePrice;

        // Calculate tax amount (subtotal * tax rate / 100)
        const taxAmount = (taxRate / 100) * subtotal;

        // Calculate discount amount (subtotal * discount percentage / 100)
        const discountAmount = (discountPercentage / 100) * subtotal;

        // Calculate total payment (subtotal + tax - discount + shipping fee)
        const totalPayment = subtotal + taxAmount - discountAmount + shippingFee;

        // Update the form fields with the calculated values
        document.querySelector('input[name="subtotal"]').value = subtotal.toFixed(2);
        document.querySelector('input[name="discount_amount"]').value = discountAmount.toFixed(2);
        document.querySelector('input[name="total_payment"]').value = totalPayment.toFixed(2);

        // Update the summary fields
        document.querySelector('input[name="summary_subtotal"]').value = subtotal.toFixed(2);
        document.querySelector('input[name="summary_discount"]').value = discountAmount.toFixed(2);
        document.querySelector('input[name="summary_shipping_fee"]').value = shippingFee.toFixed(2);
        document.querySelector('input[name="summary_total_payment"]').value = totalPayment.toFixed(2);

        // Make Discount (Amount) field non-editable
        document.querySelector('input[name="discount_amount"]').setAttribute('readonly', 'readonly');
    }

    // Attach the calculation to the tick icon button
    document.querySelector('.settings-btn').addEventListener('click', calculateTotals);
</script>

@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @if(isset($stockExport))
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
                    @if(isset($stockExport))
                        {{ Form::model($stockExport, array('route' => array('inventory.stock-export.update', $stockExport->id), 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'modalForm')) }}
                    @else
                        {{ Form::open(array('route' => ['inventory.stock-export.store'], 'enctype' => 'multipart/form-data', 'class' => 'modalForm')) }}
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 form-group">
                                {{ Form::label('document_number', __('Document Number'), ['class' => 'form-label']) }}
                                {{ Form::text('document_number', $documentNumber, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('accounting_date', __('Accounting Date'), ['class' => 'form-label']) }}
                                {{ Form::date('accounting_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('voucher_date', __('Voucher Date'), ['class' => 'form-label']) }}
                                {{ Form::date('voucher_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('invoice_id', __('Invoices'), ['class' => 'form-label']) }}
                                {{ Form::select('invoice_id', $invoices, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('customer_id', __('Customer Name'), ['class' => 'form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('receiver', __('Receiver'), ['class' => 'form-label']) }}
                                {{ Form::text('receiver', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
                                {{ Form::text('address', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('project_id', __('Project'), ['class' => 'form-label']) }}
                                {{ Form::select('project_id', $projects, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('type_id', __('Type'), ['class' => 'form-label']) }}
                                {{ Form::select('type_id', $types, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}
                                {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('requester_id', __('Requester'), ['class' => 'form-label']) }}
                                {{ Form::select('requester_id', $requesters, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('sales_person_id', __('Sales Person'), ['class' => 'form-label']) }}
                                {{ Form::select('sales_person_id', $salesPersons, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('invoice_no', __('Invoice No.'), ['class' => 'form-label']) }}
                                {{ Form::text('invoice_no', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Item Selection -->
                            <div class="col-12 form-group">
                                {{ Form::label('item_dropdown', __('Item'), ['class' => 'form-label']) }}
                                {{ Form::select('item_dropdown', $items, null, ['class' => 'form-control select2', 'placeholder' => __('Select Item')]) }}
                            </div>

                            <!-- Items Table Section -->
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('Warehouse Name') }}</th>
                                            <th>{{ __('Available Quantity') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Sale Price') }}</th>
                                            <th>{{ __('Subtotal') }}</th>
                                            <th>{{ __('Tax') }}</th>
                                            <th>{{ __('Discount (%)') }}</th>
                                            <th>{{ __('Discount (Amount)') }}</th>
                                            <th>{{ __('Shipping Fee') }}</th>
                                            <th>{{ __('Total Payment') }}</th>
                                            <th>{{ __('Settings') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ Form::text('item', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::select('warehouse_name', $warehouses, null, ['class' => 'form-control select2']) }}</td>
                                            <td>{{ Form::text('available_quantity', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::number('quantity', null, ['class' => 'form-control', 'placeholder' => __('Quantity')]) }}</td>
                                            <td>{{ Form::number('sale_price', null, ['class' => 'form-control', 'placeholder' => __('Sale Price'), 'step' => '0.01']) }}</td>
                                            <td>{{ Form::text('subtotal', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::select('tax', $taxes, null, ['class' => 'form-control']) }}</td>
                                            <td>{{ Form::number('discount_percentage', null, ['class' => 'form-control', 'placeholder' => __('%')]) }}</td>
                                            <td>{{ Form::number('discount_amount', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::number('shipping_fee', null, ['class' => 'form-control', 'placeholder' => __('Shipping Fee')]) }}</td>
                                            <td>{{ Form::text('total_payment', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-light settings-btn" onclick="calculateTotals()">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Total Summary Section -->
                            <div class="col-lg-4 offset-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Total Summary') }}</h5>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Subtotal:') }}</strong></label>
                                            {{ Form::text('summary_subtotal', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Discount:') }}</strong></label>
                                            {{ Form::text('summary_discount', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Shipping Fee:') }}</strong></label>
                                            {{ Form::text('summary_shipping_fee', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Payment:') }}</strong></label>
                                            {{ Form::text('summary_total_payment', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('Note') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
                        @if(isset($stockExport))
                            <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
                        @else
                            <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
                        @endif
                    </div>
                    {{ Form::close() }}


                </div>

            </div>
        </div>
    </div>
@endsection

