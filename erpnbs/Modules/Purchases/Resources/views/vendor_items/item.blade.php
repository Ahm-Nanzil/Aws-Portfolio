@if(isset($vendorItem))
    {{ Form::model($vendorItem, array('route' => array('vendor_items.update', $vendorItem->id), 'method' => 'PUT','class' => 'modalForm')) }}
@else
    {{ Form::open(array('route' => ['vendor_items.store'], 'class' => 'modalForm')) }}
@endif

<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('vendor', __('Vendor'), ['class' => 'form-label']) }}
            {{ Form::select('vendor', $vendors, null, ['class' => 'form-control select2', 'required' => 'required', 'data-live-search' => 'true', 'data-none-selected-text' => __('Select Vendor')]) }}
        </div>

        <div class="col-12 form-group">
            {{ Form::label('categories', __('Group Items'), ['class' => 'form-label']) }}
            {{ Form::select('categories', $groups, null, ['class' => 'form-control select2', 'required' => 'required', 'data-live-search' => 'true', 'data-none-selected-text' => __('Select Group')]) }}
        </div>

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('products', __('Products'), ['class'=>'form-label']) }}
                {{ Form::select(
                    'products[]',
                    $products,
                    isset($vendorItem) ? (is_array($vendorItem->products) ? $vendorItem->products : json_decode($vendorItem->products, true)) : null,
                    ['class' => 'form-control select2', 'multiple' => '', 'id' => 'products-select', 'required' => 'required']
                ) }}
            </div>
        </div>



    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    @if(isset($vendorItem))
        <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
    @else
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
    @endif
</div>

{{ Form::close() }}
