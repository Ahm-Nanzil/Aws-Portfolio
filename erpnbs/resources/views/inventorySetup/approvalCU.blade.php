@if(isset($approval))
{{ Form::model($approval, array('route' => array('inventory.approval.create', $approval->id), 'method' => 'PUT', 'class' => 'modalForm')) }}
@else
{{ Form::open(array('route' => ['inventory.approval.create'], 'class' => 'modalForm')) }}
@endif
<div class="modal-body">
<div class="row">
    <!-- Subject -->
    <div class="col-12 form-group">
        {{ Form::label('subject', __('Subject'), ['class' => 'form-label']) }}
        {{ Form::text('subject', null, ['class' => 'form-control', 'required' => true]) }}
    </div>

    <!-- Related -->
    <div class="col-12 form-group">
        {{ Form::label('related', __('Related'), ['class' => 'form-label']) }}
        {{ Form::select('related', $relatedOptions, null, ['class' => 'form-control', 'placeholder' => __('Select Related'), 'required' => true]) }}
    </div>

    <!-- Staff and Action -->
    <div class="col-12 form-group">
        {{ Form::label('staff_action', __('Staff and Actions'), ['class' => 'form-label']) }}
        <div id="staff-action-container">
            <div class="row mb-2 align-items-end">
                <div class="col-md-6">
                    {{ Form::label('staff[]', __('Staff'), ['class' => 'form-label']) }}
                    {{ Form::select('staff[]', $staffOptions, null, ['class' => 'form-control', 'placeholder' => __('Select Staff'), 'required' => true]) }}
                </div>
                <div class="col-md-5">
                    {{ Form::label('action[]', __('Action'), ['class' => 'form-label']) }}
                    {{ Form::select('action[]', $actionOptions, null, ['class' => 'form-control', 'placeholder' => __('Select Action'), 'required' => true]) }}
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-success add-row">+</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal-footer">
<input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
<input type="submit" value="{{ __('Save') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
