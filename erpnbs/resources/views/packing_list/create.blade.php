@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Packing List')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @if(isset($packing))
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
                <style>
                    #billToDisplay, #shipToDisplay {
                    padding: 10px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    }

                </style>
                <div class="card-body table-border-style">
                    @if(isset($packing))
                        {{ Form::model($packing, array('route' => array('inventory.packing-list.update', $packing->id), 'method' => 'post','enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @else
                        {{ Form::open(array('route' => ['inventory.packing-list.store'], 'enctype' => 'multipart/form-data','class' => 'modalForm')) }}
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <!-- Stock Export -->
                            <div class="col-6 form-group">
                                {{ Form::label('stock_export', __('Stock Export'), ['class'=>'form-label']) }}
                                {{ Form::select('stock_export_id', $stockExports, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <!-- Customer -->
                            <div class="col-6 form-group">
                                {{ Form::label('customer', __('Customer'), ['class'=>'form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">
                            <!-- Bill To -->
                            <div class="col-6 form-group">
                                {{ Form::label('bill_to', __('Bill To'), ['class'=>'form-label']) }}
                                <button type="button" class="btn btn-light btn-icon" data-bs-toggle="modal" data-bs-target="#billToModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <div id="billToDisplay" class="mt-2">{{ old('bill_to', $packing->bill_to ?? '') }}</div> <!-- Display billing address here -->
                            </div>

                            <!-- Ship To -->
                            <div class="col-6 form-group">
                                {{ Form::label('ship_to', __('Ship To'), ['class'=>'form-label']) }}
                                <button type="button" class="btn btn-light btn-icon" data-bs-toggle="modal" data-bs-target="#shipToModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <div id="shipToDisplay" class="mt-2">{{ old('ship_to', $packing->ship_to ?? '') }}</div> <!-- Display shipping address here -->
                            </div>

                            {{ Form::hidden('bill_to', null, ['id' => 'billToHidden']) }}
                            {{ Form::hidden('ship_to', null, ['id' => 'shipToHidden']) }}

                        </div>

                        <div class="row">
                            <!-- Packing List Number -->
                            <div class="col-6 form-group">
                                {{ Form::label('packing_list_number', __('Packing List Number'), ['class'=>'form-label']) }}
                                {{ Form::text('packing_list_number', $packingListNumber, ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="row">
                            <!-- Dimensions and Weight -->
                            <div class="col-3 form-group">
                                {{ Form::label('width', __('Width (m)'), ['class'=>'form-label']) }}
                                {{ Form::number('width', 0, ['class' => 'form-control']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('height', __('Height (m)'), ['class'=>'form-label']) }}
                                {{ Form::number('height', 0, ['class' => 'form-control']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('length', __('Length (m)'), ['class'=>'form-label']) }}
                                {{ Form::number('length', 0, ['class' => 'form-control']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('weight', __('Weight (kg)'), ['class'=>'form-label']) }}
                                {{ Form::number('weight', 0, ['class' => 'form-control']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('volume', __('Volume (m3)'), ['class'=>'form-label']) }}
                                {{ Form::number('volume', 0, ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <!-- Client Note -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <textarea name="client_note" class="form-control" rows="3" placeholder="{{ __('Client Note') }}"></textarea>
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
                                            <th>{{ __('Shipping Fee') }}</th>
                                            <th>{{ __('Total Payment') }}</th>
                                            <th>{{ __('Settings') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ Form::text('item_description[]', null, ['class' => 'form-control', 'placeholder' => __('Item Description')]) }}</td>

                                            {{-- <td>{{ Form::number('quantity[]', null, ['class' => 'form-control', 'placeholder' => __('Quantity')]) }}</td> --}}
                                            <td>{{ Form::number('quantity', null, ['class' => 'form-control', 'placeholder' => __('Quantity')]) }}</td>

                                            <td>{{ Form::number('sale_price', null, ['class' => 'form-control', 'placeholder' => __('Sale Price'), 'step' => '0.01']) }}</td>
                                            <td>{{ Form::select('tax', $taxes, null, ['class' => 'form-control']) }}</td>
                                            <td>{{ Form::text('subtotal', null, ['class' => 'form-control', 'readonly' => true]) }}</td>

                                            {{-- <td>{{ Form::number('sale_price[]', null, ['class' => 'form-control', 'step' => '0.01', 'placeholder' => __('Sale Price')]) }}</td> --}}
                                            {{-- <td>{{ Form::select('tax[]', [0 => __('No Tax'), 10 => __('10%'), 15 => __('15%')], null, ['class' => 'form-control']) }}</td> --}}
                                            {{-- <td>{{ Form::text('subtotal[]', null, ['class' => 'form-control', 'readonly' => true]) }}</td> --}}
                                            {{-- <td>{{ Form::number('discount[]', null, ['class' => 'form-control', 'placeholder' => __('Discount (%)')]) }}</td>
                                            <td>{{ Form::text('discount_money[]', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::text('total_payment[]', null, ['class' => 'form-control', 'readonly' => true]) }}</td> --}}
                                            <td>{{ Form::number('discount_percentage', null, ['class' => 'form-control', 'placeholder' => __('%')]) }}</td>
                                            <td>{{ Form::number('discount_amount', null, ['class' => 'form-control', 'readonly' => true]) }}</td>
                                            <td>{{ Form::number('shipping_fee', null, ['class' => 'form-control', 'placeholder' => __('Shipping Fee')]) }}</td>
                                            <td>{{ Form::text('total_payment', null, ['class' => 'form-control', 'readonly' => true]) }}</td>

                                            <td>
                                                <button type="button" class="btn btn-light settings-btn" onclick="calculateTotal()">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Total Section -->
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


                        <!-- Admin Note -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <textarea name="admin_note" class="form-control" rows="3" placeholder="{{ __('Admin Note') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
                        @if(isset($packing))
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
    <!-- Bill To Modal -->
    <div class="modal fade" id="billToModal" tabindex="-1" aria-labelledby="billToModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="billToModalLabel">{{ __('Billing Address') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="billingAddress">{{ __('Address') }}</label>
                        <textarea id="billingAddress" class="form-control" rows="3" placeholder="{{ __('Enter billing address') }}"></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <label for="billingCity">{{ __('City') }}</label>
                        <input id="billingCity" type="text" class="form-control" placeholder="{{ __('Enter city') }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="billingState">{{ __('State') }}</label>
                        <input id="billingState" type="text" class="form-control" placeholder="{{ __('Enter state') }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="billingZip">{{ __('ZIP Code') }}</label>
                        <input id="billingZip" type="text" class="form-control" placeholder="{{ __('Enter ZIP code') }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="billingCountry">{{ __('Country') }}</label>
                        <select id="billingCountry" name="billing_country" class="form-control">
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="applyBilling">{{ __('Apply') }}</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="shipToModal" tabindex="-1" aria-labelledby="shipToModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shipToModalLabel">{{ __('Shipping Address') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="shippingAddress">{{ __('Address') }}</label>
                        <textarea id="shippingAddress" class="form-control" rows="3" placeholder="{{ __('Enter shipping address') }}"></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <label for="shippingCity">{{ __('City') }}</label>
                        <input id="shippingCity" type="text" class="form-control" placeholder="{{ __('Enter city') }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="shippingState">{{ __('State') }}</label>
                        <input id="shippingState" type="text" class="form-control" placeholder="{{ __('Enter state') }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="shippingZip">{{ __('ZIP Code') }}</label>
                        <input id="shippingZip" type="text" class="form-control" placeholder="{{ __('Enter ZIP code') }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="shippingCountry">{{ __('Country') }}</label>
                        <select id="shippingCountry" class="form-control">
                            @foreach($countries as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="applyShipping">{{ __('Apply') }}</button>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Apply Billing Address
        document.getElementById('applyBilling').addEventListener('click', function () {
            const billingAddress = document.getElementById('billingAddress').value;
            const billingCity = document.getElementById('billingCity').value;
            const billingState = document.getElementById('billingState').value;
            const billingZip = document.getElementById('billingZip').value;
            const billingCountry = document.getElementById('billingCountry').selectedOptions[0].text;

            const formattedBilling = `${billingAddress}, ${billingCity}, ${billingState}, ${billingZip}, ${billingCountry}`;
            document.getElementById('billToHidden').value = formattedBilling;
            document.getElementById('billToDisplay').innerText = formattedBilling;

            const billingModal = bootstrap.Modal.getInstance(document.getElementById('billToModal'));
            billingModal.hide();
        });

        // Apply Shipping Address
        document.getElementById('applyShipping').addEventListener('click', function () {
            const shippingAddress = document.getElementById('shippingAddress').value;
            const shippingCity = document.getElementById('shippingCity').value;
            const shippingState = document.getElementById('shippingState').value;
            const shippingZip = document.getElementById('shippingZip').value;
            const shippingCountry = document.getElementById('shippingCountry').selectedOptions[0].text;

            const formattedShipping = `${shippingAddress}, ${shippingCity}, ${shippingState}, ${shippingZip}, ${shippingCountry}`;
            document.getElementById('shipToHidden').value = formattedShipping;
            document.getElementById('shipToDisplay').innerText = formattedShipping;

            const shippingModal = bootstrap.Modal.getInstance(document.getElementById('shipToModal'));
            shippingModal.hide();
        });
    });


</script>
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

