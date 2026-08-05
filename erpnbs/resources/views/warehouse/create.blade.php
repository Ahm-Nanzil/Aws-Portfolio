{{ Form::open(array('url' => 'warehouse')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['warehouse']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('code', __('Warehouse Code'),['class'=>'form-label']) }}
            {{ Form::text('code', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Warehouse Name'),['class'=>'form-label']) }}
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('address', __('Warehouse Address'),['class'=>'form-label']) }}
            {{ Form::textarea('address', null, array('class' => 'form-control', 'rows' => 3)) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('city', __('City'),['class'=>'form-label']) }}
            {{ Form::text('city', null, array('class' => 'form-control')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('state', __('State'),['class'=>'form-label']) }}
            {{ Form::text('state', null, array('class' => 'form-control')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('postal_code', __('Postal Code'),['class'=>'form-label']) }}
            {{ Form::text('postal_code', null, array('class' => 'form-control')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('country', __('Country'),['class'=>'form-label']) }}
            {{ Form::select('country', \Webpatser\Countries\Countries::getCountries(), null, ['class' => 'form-control', 'placeholder' => __('Select Country')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('note', __('Note'),['class'=>'form-label']) }}
            {{ Form::textarea('note', null, array('class' => 'form-control', 'rows' => 3)) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('staffs', __('Assign to Staffs'),['class'=>'form-label']) }}
            {{-- {{ Form::select('staffs[]', , null, ['class' => 'form-control', 'multiple' => 'multiple']) }} --}}
            {{ Form::select('staffs[]', \App\Models\User::pluck('name', 'id')->toArray(), null, ['class' => 'form-control select2', 'multiple' => '', 'id' => 'assigned-users-select', 'required' => 'required']) }}

        </div>
        <div class="col-3 custom-control custom-checkbox form-switch">
            {{ Form::checkbox('display', 1, true, ['class' => 'form-check-input', 'id' => 'display', 'checked' => 'checked']) }}
            {{ Form::label('display', __('Display'), ['class' => 'custom-control-label']) }}
        </div>

        <div class="col-3 custom-control custom-checkbox form-switch">
            {{ Form::checkbox('hide_when_out_of_stock', 1, false, ['class' => 'form-check-input', 'id' => 'hide_when_out_of_stock']) }}
            {{ Form::label('hide_when_out_of_stock', __('Hide Warehouse When Out of Stock'), ['class' => 'custom-control-label']) }}
        </div>


    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
