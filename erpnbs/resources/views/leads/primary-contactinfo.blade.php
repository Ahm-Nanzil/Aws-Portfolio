{{ Form::model($lead, array('route' => array('leads.primarycontact.update', $lead->id), 'method' => 'PUT','class' => 'modalForm')) }}
<div class="modal-body">
    <fieldset>
        <legend>{{ __('Primary Contact Info') }}</legend>

        <div class="row">
            <!-- Salutation -->
            <div class="col-6 form-group">
                {{ Form::label('salutation', __('Salutation'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('salutation', ['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms'], null, array('class' => 'form-control select', 'required' => 'required')) }}
            </div>

            <!-- First Name -->
            <div class="col-6 form-group">
                {{ Form::label('first_name', __('First Name'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::text('first_name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter First Name'))) }}
            </div>

            <!-- Last Name -->
            <div class="col-6 form-group">
                {{ Form::label('last_name', __('Last Name'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::text('last_name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Last Name'))) }}
            </div>

            <!-- Mobile Primary -->
            <div class="col-6 form-group">
                {{ Form::label('mobile_primary', __('Mobile Primary'), ['class' => 'form-label']) }}
                {{ Form::text('mobile_primary', null, array('class' => 'form-control', 'placeholder' => __('+966(0)541145867'))) }}
            </div>

            <!-- Mobile Secondary -->
            <div class="col-6 form-group">
                {{ Form::label('mobile_secondary', __('Mobile Secondary'), ['class' => 'form-label']) }}
                {{ Form::text('mobile_secondary', null, array('class' => 'form-control', 'placeholder' => __('+966(0)541145867'))) }}
            </div>

            <!-- Email Work -->
            <div class="col-6 form-group">
                {{ Form::label('email_work', __('Email Work'), ['class' => 'form-label']) }}
                {{ Form::email('email_work', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'))) }}
            </div>

            <!-- Email Personal -->
            <div class="col-6 form-group">
                {{ Form::label('email_personal', __('Email Personal'), ['class' => 'form-label']) }}
                {{ Form::email('email_personal', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'))) }}
            </div>

            <!-- Phone LL -->
            <div class="col-6 form-group">
                {{ Form::label('phone_ll', __('Phone LL'), ['class' => 'form-label']) }}
                {{ Form::text('phone_ll', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'))) }}
            </div>

            <!-- Company Phone LL -->
            <div class="col-6 form-group">
                {{ Form::label('company_phone_ll', __('Company Phone LL'), ['class' => 'form-label']) }}
                {{ Form::text('company_phone_ll', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'))) }}
            </div>

            <!-- Extension -->
            <div class="col-6 form-group">
                {{ Form::label('extension', __('Extension #'), ['class' => 'form-label']) }}
                {{ Form::number('extension', null, array('class' => 'form-control', 'placeholder' => __('Enter Extension Number'))) }}
            </div>

            <!-- LinkedIn Profile -->
            <div class="col-6 form-group">
                {{ Form::label('linkedin_profile', __('LinkedIn Profile'), ['class' => 'form-label']) }}
                {{ Form::url('linkedin_profile', null, array('class' => 'form-control', 'placeholder' => __('Enter LinkedIn URL'))) }}
            </div>
        </div>
    </fieldset>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>

{{ Form::close() }}
