{{ Form::model($warehouse, array('route' => array('warehouse.update', $warehouse->id), 'method' => 'PUT')) }}

<div class="modal-body">
    {{-- Start for AI module --}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['warehouse']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- End for AI module --}}

    <div class="row">
        <!-- Name Field -->
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
            @error('name')
            <small class="invalid-name" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

        <!-- Address Field -->
        <div class="form-group col-md-12">
            {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('address', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>

        <!-- City Field -->
        <div class="form-group col-md-6">
            {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
            {{ Form::text('city', null, ['class' => 'form-control']) }}
        </div>

        <!-- Zip Code Field -->
        <div class="form-group col-md-6">
            {{ Form::label('city_zip', __('Zip Code'), ['class' => 'form-label']) }}
            {{ Form::text('city_zip', null, ['class' => 'form-control']) }}
        </div>

        <!-- Warehouse Code Field -->
        <div class="form-group col-md-6">
            {{ Form::label('code', __('Warehouse Code'), ['class' => 'form-label']) }}
            {{ Form::text('code', null, ['class' => 'form-control']) }}
        </div>

        <!-- State Field -->
        <div class="form-group col-md-6">
            {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
            {{ Form::text('state', null, ['class' => 'form-control']) }}
        </div>

        <!-- Postal Code Field -->
        <div class="form-group col-md-6">
            {{ Form::label('postal_code', __('Postal Code'), ['class' => 'form-label']) }}
            {{ Form::text('postal_code', null, ['class' => 'form-control']) }}
        </div>

        <!-- Staffs Field (Multi-select) -->
        <div class="form-group col-md-6">
            {{ Form::label('staffs', __('Assign to Staffs'), ['class' => 'form-label']) }}
            {{-- {{ Form::select('staffs[]', $staffs, json_decode($warehouse->staffs, true), ['class' => 'form-control', 'multiple' => true]) }} --}}
            {{ Form::select('staffs[]', $staffs, json_decode($warehouse->staffs, true), ['class' => 'form-control select2', 'multiple' => '', 'id' => 'assigned-users-select', 'required' => 'required']) }}

        </div>

        <!-- Display Field (Checkbox) -->
        <div class="form-group col-md-6">
            {{ Form::label('display', __('Display'), ['class' => 'form-label']) }}
            {{ Form::checkbox('display', 1, $warehouse->display, ['class' => 'form-check-input']) }}
        </div>

        <!-- Hide When Out of Stock Field (Checkbox) -->
        <div class="form-group col-md-6">
            {{ Form::label('hide_when_out_of_stock', __('Hide When Out of Stock'), ['class' => 'form-label']) }}
            {{ Form::checkbox('hide_when_out_of_stock', 1, $warehouse->hide_when_out_of_stock, ['class' => 'form-check-input']) }}
        </div>

        <!-- Note Field -->
        <div class="form-group col-md-12">
            {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}
            {{ Form::textarea('note', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Edit') }}" class="btn btn-primary">
</div>

{{ Form::close() }}
