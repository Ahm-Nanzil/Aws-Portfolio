@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Cust & Supp Returns')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @if(isset($packing))
    <li class="breadcrumb-item">{{__('Reciept Return Edit')}}</li>
    @else
    <li class="breadcrumb-item">{{__('Reciept Return Create')}}</li>
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
                    @if(isset($orderReturn))
                        {{ Form::model($orderReturn, array('route' => array('inventory.cust-supp.update', $orderReturn->id), 'method' => 'post','enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @else
                        {{ Form::open(array('route' => ['inventory.cust-supp.store'], 'enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <!-- Related Type -->
                            <div class="col-6 form-group">
                                {{ Form::label('related_type', __('Related Type'), ['class'=>'form-label']) }}
                                {{ Form::select('related_type_id', $relatedTypes, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <!-- Related Data -->
                            <div class="col-6 form-group">
                                {{ Form::label('related_data', __('Related Data'), ['class'=>'form-label']) }}
                                {{ Form::select('related_data_id', $relatedData, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">
                            <!-- Order Number -->
                            <div class="col-6 form-group">
                                {{ Form::label('order_number', __('Order Number'), ['class'=>'form-label']) }}
                                {{ Form::text('order_number', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <!-- Order Date -->
                            <div class="col-6 form-group">
                                {{ Form::label('order_date', __('Order Date'), ['class'=>'form-label']) }}
                                {{ Form::date('order_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">
                            <!-- Customer -->
                            <div class="col-6 form-group">
                                {{ Form::label('customer', __('Customer'), ['class'=>'form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <!-- Date Created -->
                            <div class="col-6 form-group">
                                {{ Form::label('date_created', __('Date Created'), ['class'=>'form-label']) }}
                                {{ Form::date('date_created', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">
                            <!-- Return Type -->
                            <div class="col-6 form-group">
                                {{ Form::label('return_type', __('Return Type'), ['class'=>'form-label']) }}
                                {{ Form::select('return_type_id', $returnTypes, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <!-- Email -->
                            <div class="col-6 form-group">
                                {{ Form::label('email', __('Email'), ['class'=>'form-label']) }}
                                {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">
                            <!-- Phone Number -->
                            <div class="col-6 form-group">
                                {{ Form::label('phone_number', __('Phone Number'), ['class'=>'form-label']) }}
                                {{ Form::text('phone_number', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <!-- Order Return Number -->
                            <div class="col-6 input-group">
                                {{ Form::text('order_return_number_prefix', $orderReturnPrefix, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                                {{ Form::text('order_return_number_suffix', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>

                        <!-- Items Table Section -->
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Sale Price') }}</th>
                                            <th>{{ __('Tax') }}</th>
                                            <th>{{ __('Subtotal') }}</th>
                                            <th>{{ __('Discount') }}</th>
                                            <th>{{ __('Discount (money)') }}</th>
                                            <th>{{ __('Total Payment') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ Form::text('item_description[]', null, ['class' => 'form-control', 'placeholder' => __('Item Description')]) }}</td>
                                            <td>{{ Form::number('quantity[]', null, ['class' => 'form-control', 'placeholder' => __('Quantity')]) }}</td>
                                            <td>{{ Form::number('sale_price[]', null, ['class' => 'form-control', 'placeholder' => __('Sale Price'), 'step' => '0.01']) }}</td>
                                            <td>{{ Form::select('tax[]', $taxes, null, ['class' => 'form-control']) }}</td>
                                            <td>{{ Form::text('subtotal[]', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::number('discount_percentage[]', null, ['class' => 'form-control', 'placeholder' => __('%')]) }}</td>
                                            <td>{{ Form::number('discount_amount[]', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::text('total_payment', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Total Section -->
                        <div class="row mt-4">
                            <div class="col-lg-4 offset-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Total Summary') }}</h5>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Subtotal:') }}</strong></label>
                                            {{ Form::text('summary_subtotal', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Additional Discount:') }}</strong></label>
                                            {{ Form::text('summary_additional_discount', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Discount:') }}</strong></label>
                                            {{ Form::text('summary_total_discount', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>{{ __('Total Payment:') }}</strong></label>
                                            {{ Form::text('summary_total_payment', '0.00', ['class' => 'form-control', 'readonly' => true]) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('Reason') }}"></textarea>
                            </div>
                        </div>

                        <!-- Admin Note -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <textarea name="admin_note" class="form-control" rows="3" placeholder="{{ __('Admin Note') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
                        @if(isset($orderReturn))
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



{{-- <script>
    function calculateItemRow(row) {
        const quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
        const salePrice = parseFloat(row.querySelector('input[name="sale_price[]"]').value) || 0;
        const taxRate = parseFloat(row.querySelector('select[name="tax[]"]').value) || 0;
        const discountPercent = parseFloat(row.querySelector('input[name="discount[]"]').value) || 0;

        // Calculate raw subtotal before any discount or tax
        const subtotal = quantity * salePrice;

        // Calculate discount amount from raw subtotal
        const discountAmount = (subtotal * discountPercent) / 100;

        // Calculate amount after discount
        const afterDiscount = subtotal - discountAmount;

        // Calculate tax on the discounted amount
        const taxAmount = (afterDiscount * taxRate) / 100;

        // Calculate final total payment for the row
        const totalPayment = afterDiscount + taxAmount;

        // Update row fields
        row.querySelector('input[name="subtotal[]"]').value = subtotal.toFixed(2);
        row.querySelector('input[name="discount_money[]"]').value = discountAmount.toFixed(2);
        row.querySelector('input[name="total_payment[]"]').value = totalPayment.toFixed(2);

        return {
            subtotal,
            discountAmount,
            taxAmount,
            totalPayment
        };
    }

    function calculateTotal() {
        const rows = document.querySelectorAll('table tbody tr');
        let totalSubtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;
        let totalBeforeExtras = 0;

        // Calculate totals from all rows
        rows.forEach(row => {
            const rowTotals = calculateItemRow(row);
            totalSubtotal += rowTotals.subtotal;
            totalDiscount += rowTotals.discountAmount;
            totalTax += rowTotals.taxAmount;
            totalBeforeExtras += rowTotals.totalPayment;
        });

        // Get additional values
        const additionalDiscount = parseFloat(document.querySelector('input[name="additional_discount"]').value) || 0;
        const shippingFee = parseFloat(document.querySelector('input[name="shipping_fee"]').value) || 0;

        // Calculate final totals
        const totalDiscountWithAdditional = totalDiscount + additionalDiscount;

        // Apply additional discount before adding shipping fee
        const finalTotalPayment = totalBeforeExtras - additionalDiscount + shippingFee;

        // Update total section fields
        document.querySelector('input[name="subtotal"]').value = totalSubtotal.toFixed(2);
        document.querySelector('input[name="total_discount"]').value = totalDiscountWithAdditional.toFixed(2);
        document.querySelector('input[name="total_payment"]').value = finalTotalPayment.toFixed(2);
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all input fields in the items table
        const table = document.querySelector('table');
        table.addEventListener('input', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') {
                calculateTotal();
            }
        });

        // Add event listeners to additional discount and shipping fee
        document.querySelector('input[name="additional_discount"]').addEventListener('input', calculateTotal);
        document.querySelector('input[name="shipping_fee"]').addEventListener('input', calculateTotal);

        // Initial calculation
        calculateTotal();
    });

    // Update total after clicking the "tick" button
    function updateTotalAfterTick() {
        // Recalculate totals after clicking the tick button
        calculateTotal();
    }
</script> --}}
<script>
    function calculateTotals() {
        // Retrieve values from the input fields
        const quantity = parseFloat(document.querySelector('input[name="quantity"]').value) || 0;
        const salePrice = parseFloat(document.querySelector('input[name="sale_price"]').value) || 0;
        // const taxRate = parseFloat(document.querySelector('select[name="tax"]').value) || 0;
        const discountPercentage = parseFloat(document.querySelector('input[name="discount_percentage"]').value) || 0;
        const shippingFee = parseFloat(document.querySelector('input[name="shipping_fee"]').value) || 0;
        const taxOption = parseInt(document.querySelector('select[name="tax"]').value) || 0;
        let taxRate = 0;
    switch(taxOption) {
        case 1: taxRate = 0; break;  // No Tax
        case 2: taxRate = 5; break;  // 5%
        case 3: taxRate = 10; break; // 10%
        case 4: taxRate = 15; break; // 15%
        default: taxRate = 0; break;
    }
        console.log(taxRate);

        // Calculate subtotal (quantity * sale price)
        const subtotal = quantity * salePrice;

        console.log(subtotal);
        // Calculate tax amount (subtotal * tax rate / 100)
        const taxAmount = (taxRate / 100) * subtotal;
        console.log(taxAmount);

        // Calculate discount amount (subtotal * discount percentage / 100)
        const discountAmount = (discountPercentage / 100) * subtotal;
        console.log(discountAmount);

        // Calculate total payment (subtotal + tax - discount + shipping fee)
        const totalPayment = subtotal + taxAmount - discountAmount + shippingFee;
        console.log(totalPayment);

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

@endsection

