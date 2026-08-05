{{ Form::model($productService, array('route' => array('productservice.update', $productService->id), 'method' => 'PUT','enctype' => "multipart/form-data")) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['productservice']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                {{ Form::text('name',null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sku', __('SKU'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                {{ Form::text('sku', null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sale_price', __('Sale Price'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                {{ Form::number('sale_price', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('sale_chartaccount_id', __('Income Account'),['class'=>'form-label']) }}
            <select name="sale_chartaccount_id" class="form-control" required="required">
                @foreach ($incomeChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount" {{ ($productService->sale_chartaccount_id == $key) ? 'selected' : ''}}>{{ $chartAccount }}</option>
                    @foreach ($incomeSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5" {{ ($productService->sale_chartaccount_id == $subAccount['id']) ? 'selected' : ''}}> &nbsp; &nbsp;&nbsp; {{ $subAccount['code_name'] }}</option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purchase_price', __('Purchase Price'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                {{ Form::number('purchase_price', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('expense_chartaccount_id', __('Expense Account'),['class'=>'form-label']) }}
            <select name="expense_chartaccount_id" class="form-control" required="required">
                @foreach ($expenseChartAccounts as $key => $chartAccount)
                    <option value="{{ $key }}" class="subAccount" {{ ($productService->expense_chartaccount_id == $key) ? 'selected' : ''}}>{{ $chartAccount }}</option>
                    @foreach ($expenseSubAccounts as $subAccount)
                        @if ($key == $subAccount['account'])
                            <option value="{{ $subAccount['id'] }}" class="ms-5" {{ ($productService->expense_chartaccount_id == $subAccount['id']) ? 'selected' : ''}}> &nbsp; &nbsp;&nbsp; {{ $subAccount['code_name'] }}</option>
                        @endif
                    @endforeach
                @endforeach
            </select>
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('tax_id', __('Tax'),['class'=>'form-label']) }}
            {{ Form::select('tax_id[]', $tax,null, array('class' => 'form-control select2','id'=>'choices-multiple1','multiple'=>'')) }}
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('category_id', $category,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('unit_id', __('Unit'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('unit_id', $unit,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>

        <div class="col-md-6 form-group">
            {{Form::label('pro_image',__('Product Image'),['class'=>'form-label'])}}
            <div class="choose-file ">
                <label for="pro_image" class="form-label">
                    <input type="file" class="form-control" name="pro_image" id="pro_image" data-filename="pro_image_create">
                    <img id="image"  class="mt-3" width="100" src="@if($productService->pro_image){{asset(Storage::url('uploads/pro_image/'.$productService->pro_image))}}@else{{asset(Storage::url('uploads/pro_image/user-2_1654779769.jpg'))}}@endif" />
                </label>
            </div>
        </div>



        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block form-label">{{__('Type')}}</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input type" id="customRadio5" name="type" value="product" @if($productService->type=='product') checked @endif >
                            <label class="custom-control-label form-label" for="customRadio5">{{__('Product')}}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input type" id="customRadio6" name="type" value="service" @if($productService->type=='service') checked @endif >
                            <label class="custom-control-label form-label" for="customRadio6">{{__('Service')}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group col-md-6 quantity {{$productService->type=='service' ? 'd-none':''}}">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('quantity',null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!}
        </div>

        <!-- Additional Fields -->
<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('images', __('Images'),['class'=>'form-label']) }}
        {{ Form::file('images[]', ['class' => 'form-control', 'multiple' => 'multiple']) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('product_type_code', __('Product Type Code'),['class'=>'form-label']) }}
        {{ Form::text('product_type_code', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('product_type_name', __('Product Type Name'),['class'=>'form-label']) }}
        {{ Form::text('product_type_name', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('group_name', __('Group Name'),['class'=>'form-label']) }}
        {{ Form::text('group_name', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('warehouse_name', __('Warehouse Name'),['class'=>'form-label']) }}
        {{ Form::text('warehouse_name', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('tags', __('Tags'),['class'=>'form-label']) }}
        {{ Form::text('tags', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('inventory', __('Inventory'),['class'=>'form-label']) }}
        {{ Form::number('inventory', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('main_category', __('Main Category'),['class'=>'form-label']) }}
        {{ Form::text('main_category', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('sub_category', __('Sub Category'),['class'=>'form-label']) }}
        {{ Form::text('sub_category', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('child_category', __('Child Category'),['class'=>'form-label']) }}
        {{ Form::text('child_category', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('tax_2', __('Tax 2'),['class'=>'form-label']) }}
        {{ Form::text('tax_2', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('status', __('Status'),['class'=>'form-label']) }}
        {{ Form::text('status', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('minimum_stock', __('Minimum Stock'),['class'=>'form-label']) }}
        {{ Form::number('minimum_stock', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('maximum_stock', __('Maximum Stock'),['class'=>'form-label']) }}
        {{ Form::number('maximum_stock', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('price_after_tax', __('Price After Tax'),['class'=>'form-label']) }}
        {{ Form::number('price_after_tax', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('mother', __('Mother'),['class'=>'form-label']) }}
        {{ Form::text('mother', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('date', __('Date'),['class'=>'form-label']) }}
        {{ Form::date('date', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('inventory_vendor', __('Inventory Vendor'),['class'=>'form-label']) }}
        {{ Form::text('inventory_vendor', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('expiration_date', __('Expiration Date'),['class'=>'form-label']) }}
        {{ Form::date('expiration_date', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('uom', __('UOM'),['class'=>'form-label']) }}
        {{ Form::text('uom', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('temperature_inwards_delivery', __('Temperature Inwards Delivery'),['class'=>'form-label']) }}
        {{ Form::text('temperature_inwards_delivery', null, array('class' => 'form-control')) }}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {{ Form::label('target_penjualan', __('Target Penjualan'),['class'=>'form-label']) }}
        {{ Form::text('target_penjualan', null, array('class' => 'form-control')) }}
    </div>
</div>
<!-- End Additional Fields -->



    </div>
    @if(!$customFields->isEmpty())
        <div class="col-md-6">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('customFields.formBuilder')
            </div>
        </div>
    @endif
</div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
<script>
    document.getElementById('pro_image').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }

    //hide & show quantity

    $(document).on('click', '.type', function ()
    {
        var type = $(this).val();
        if (type == 'product') {
            $('.quantity').removeClass('d-none')
            $('.quantity').addClass('d-block');
        } else {
            $('.quantity').addClass('d-none')
            $('.quantity').removeClass('d-block');
        }
    });
</script>

