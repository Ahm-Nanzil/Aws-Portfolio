{{ Form::open(['route' => ['customer.additional-contact.store', $customer->id], 'class' => 'modalForm']) }}
    <div class="modal-body">
        <div class="row">
            <!-- Contact Name -->
            <div class="col-md-12 form-group">
                {{ Form::label('name', __('Contact Name'), ['class' => 'form-label']) }}
                {{ Form::text('name', null, [
                    'class' => 'form-control',
                    'placeholder' => __('Enter Contact Name'),
                    'required' => 'required',
                    'oninvalid' => "this.setCustomValidity('Please enter the contact name')",
                    'oninput' => "this.setCustomValidity('')"
                ]) }}
            </div>

            <!-- Email -->
            <div class="col-md-12 form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                {{ Form::email('email', null, [
                    'class' => 'form-control',
                    'placeholder' => __('Enter Email'),
                    'required' => 'required',
                    'oninvalid' => "this.setCustomValidity('Please enter a valid email address')",
                    'oninput' => "this.setCustomValidity('')"
                ]) }}
            </div>

            <!-- Title -->
            <div class="col-md-12 form-group">
                {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
                {{ Form::text('title', null, [
                    'class' => 'form-control',
                    'placeholder' => __('Enter Title'),
                    'required' => 'required',
                    'oninvalid' => "this.setCustomValidity('Please enter the title')",
                    'oninput' => "this.setCustomValidity('')"
                ]) }}
            </div>

            <!-- Department -->
            <div class="col-md-12 form-group">
                {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                {{ Form::text('department', null, [
                    'class' => 'form-control',
                    'placeholder' => __('Enter Department'),
                    'required' => 'required',
                    'oninvalid' => "this.setCustomValidity('Please enter the department')",
                    'oninput' => "this.setCustomValidity('')"
                ]) }}
            </div>

            <!-- Phone Mobile -->
            <div class="col-md-12 form-group">
                {{ Form::label('phone_mobile', __('Phone Mobile'), ['class' => 'form-label']) }}
                {{ Form::text('phone_mobile', null, [
                    'class' => 'form-control',
                    'placeholder' => __('Enter Phone Mobile'),
                    'required' => 'required'

                ]) }}
                {{-- <small class="form-text text-muted">Format: 10 digits, numbers only.</small> --}}
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Save Contact') }}" class="btn btn-primary">
    </div>
{{ Form::close() }}

