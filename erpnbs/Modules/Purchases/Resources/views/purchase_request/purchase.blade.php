@extends('layouts.admin')
@section('page-title')
    {{__('Purchase Request')}}
@endsection
@push('script-page')


@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @if(isset($purchaseRequest))
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
                    @if(isset($purchaseRequest))
                        {{ Form::model($purchaseRequest, array('route' => array('purchase_request.update', $purchaseRequest->id), 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'modalForm')) }}
                    @else
                        {{ Form::open(array('route' => ['purchase_request.store'], 'enctype' => 'multipart/form-data', 'class' => 'modalForm')) }}
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 form-group">
                                {{ Form::label('purchase_request_code', __('Purchase Request Code'), ['class' => 'form-label']) }}
                                {{ Form::text('purchase_request_code', $purchaseRequestCode, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                            </div>

                            <div class="col-12 form-group">
                                {{ Form::label('purchase_request_name', __('Purchase Request Name'), ['class' => 'form-label']) }}
                                {{ Form::text('purchase_request_name', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('project_id', __('Project'), ['class' => 'form-label']) }}
                                {{ Form::select('project_id', $projects, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('sale_estimate_id', __('Sale Estimate'), ['class' => 'form-label']) }}
                                {{ Form::select('sale_estimate_id', $saleEstimates, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('type_id', __('Type'), ['class' => 'form-label']) }}
                                {{ Form::select('type_id', $types, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('currency', __('Currency'), ['class' => 'form-label']) }}
                                {{ Form::select('currency', $currencies, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}
                                {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('sale_invoice_id', __('Sale Invoices'), ['class' => 'form-label']) }}
                                {{ Form::select('sale_invoice_id', $saleInvoices, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('requester_id', __('Requester'), ['class' => 'form-label']) }}
                                {{ Form::select('requester_id', $requesters, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-3 form-group">
                                {{ Form::label('vendor_id', __('Share to Vendors'), ['class' => 'form-label']) }}
                                {{ Form::select('vendor_id', $vendors, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                            </div>

                            <div class="col-12 form-group">
                                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
                            </div>
                        </div>
                    </div>

                    @if(!isset($purchaseRequest))

                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="form-group">
                                    {{ Form::label('item_select', __('Select Item')) }}
                                    <select class="form-control select2" id="itemDropdown">
                                        <option value="">{{ __('Select Item') }}</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->sale_price ?? '' }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('Unit Price (USD)') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Subtotal (USD)') }}</th>
                                            <th>{{ __('Tax') }}</th>
                                            <th>{{ __('Tax Value (USD)') }}</th>
                                            <th>{{ __('Total (USD)') }}</th>
                                            <th>{{ __('Settings') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="newItemRow">
                                            <td>{{ Form::text('item_name', null, ['class' => 'form-control', 'id' => 'itemName', 'readonly' => 'readonly']) }}</td>
                                            <td>{{ Form::text('unit_price', null, ['class' => 'form-control', 'id' => 'itemPrice']) }}</td>
                                            <td>{{ Form::text('quantity', null, ['class' => 'form-control', 'id' => 'itemQuantity']) }}</td>
                                            <td><input type="text" class="form-control" id="itemSubtotal" value="0" readonly></td>
                                            <td>{{ Form::select('tax', ['No Tax' => __('No Tax')], null, ['class' => 'form-control', 'id' => 'itemTax']) }}</td>
                                            <td>{{ Form::text('tax_value', null, ['class' => 'form-control', 'id' => 'itemTaxValue', 'readonly' => 'readonly']) }}</td>
                                            <td><input type="text" class="form-control" id="itemTotal" readonly></td>
                                            <td>
                                                <button type="button" class="btn btn-light settings-btn" id="addItemBtn">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>{{ __('Subtotal') }}</label>
                                    <input type="text" class="form-control" id="subtotalDisplay" value="0.00" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label>{{ __('Total') }}</label>
                                    <input type="text" class="form-control" id="totalDisplay" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif


                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                                let items = @json($items);

                                document.getElementById('itemDropdown').addEventListener('change', function() {
                                    let selectedItem = items.find(item => item.id == this.value) || {};
                                    document.getElementById('itemName').value = selectedItem.name || '';
                                    document.getElementById('itemPrice').value = selectedItem.sale_price || 0;
                                });

                                document.getElementById('addItemBtn').addEventListener('click', function() {
                                    let name = document.getElementById('itemName').value;
                                    let price = parseFloat(document.getElementById('itemPrice').value) || 0;
                                    let quantity = parseInt(document.getElementById('itemQuantity').value) || 1;
                                    let tax = document.getElementById('itemTax').value;
                                    let taxValue = tax === 'No Tax' ? 0 : (price * quantity * 0.1);
                                    let subtotal = price * quantity;
                                    let total = subtotal + taxValue;
                                    let itemId = document.getElementById('itemDropdown').value;

                                    if (!name || !itemId) {
                                        alert('Please select a valid item.');
                                        return;
                                    }

                                    let newRow = `<tr>
                                        <td>${name}</td>
                                        <td>${price.toFixed(2)}</td>
                                        <td>${quantity}</td>
                                        <td>${subtotal.toFixed(2)}</td>
                                        <td>${tax}</td>
                                        <td>${taxValue.toFixed(2)}</td>
                                        <td>${total.toFixed(2)}</td>
                                        <td><button type="button" class="btn btn-danger delete-btn"><i class="fas fa-trash"></i></button></td>
                                        <input type="hidden" name="items[${itemId}][id]" value="${itemId}">
                                        <input type="hidden" name="items[${itemId}][name]" value="${name}">
                                        <input type="hidden" name="items[${itemId}][price]" value="${price}">
                                        <input type="hidden" name="items[${itemId}][quantity]" value="${quantity}">
                                        <input type="hidden" name="items[${itemId}][subtotal]" value="${subtotal}">
                                        <input type="hidden" name="items[${itemId}][tax]" value="${tax}">
                                        <input type="hidden" name="items[${itemId}][tax_value]" value="${taxValue}">
                                        <input type="hidden" name="items[${itemId}][total]" value="${total}">
                                    </tr>`;

                                    document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', newRow);
                                    updateTotals();
                                });

                                document.querySelector('#itemsTable').addEventListener('click', function(event) {
                                    if (event.target.closest('.delete-btn')) {
                                        event.target.closest('tr').remove();
                                        updateTotals();
                                    }
                                });

                                function updateTotals() {
                                    let subtotal = 0;
                                    let total = 0;
                                    document.querySelectorAll('#itemsTable tbody tr:not(#newItemRow)').forEach(row => {
                                        let subtotalCell = parseFloat(row.cells[3].innerText) || 0;
                                        let totalCell = parseFloat(row.cells[6].innerText) || 0;
                                        subtotal += subtotalCell;
                                        total += totalCell;
                                    });
                                    document.getElementById('subtotalDisplay').value = subtotal.toFixed(2);
                                    document.getElementById('totalDisplay').value = total.toFixed(2);
                                }
                            });

                    </script>

                    <div class="modal-footer">
                        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
                        @if(isset($purchaseRequest))
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

