
{{Form::model($user,array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                {{Form::label('name',__('Name'),['class'=>'form-label']) }}
                {{Form::text('name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter User Name')))}}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('email',__('Email'),['class'=>'form-label'])}}
                {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email')))}}
                @error('email')
                <small class="invalid-email" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        @if(\Auth::user()->type != 'super admin')
            <div class="form-group col-md-6">
                {{ Form::label('role', __('User Role'),['class'=>'form-label']) }}
                {!! Form::select('role', $roles, $user->roles,array('class' => 'form-control select','required'=>'required')) !!}
                @error('role')
                <small class="invalid-role" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        @endif
        @if(!$customFields->isEmpty())
            <div class="col-md-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('expiry_time', __('Expiry Time'), ['class' => 'form-label']) }}
                {{ Form::date('expiry_time', $user->expiry_time ? \Carbon\Carbon::parse($user->expiry_time)->format('Y-m-d') : null, ['class' => 'form-control', 'min' => \Carbon\Carbon::now()->format('Y-m-d'), 'placeholder' => __('Select Expiry Date')]) }}
                @error('expiry_time')
                    <small class="invalid-expiry_time" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                @enderror
                <div class="form-check">
                    <input type="checkbox" id="clearExpiry" class="form-check-input">
                    <label class="form-check-label" for="clearExpiry">{{ __('Clear Expiry Date') }}</label>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light"data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}
<script>
    document.getElementById('clearExpiry').addEventListener('change', function() {
        const expiryInput = document.querySelector('input[name="expiry_time"]');
        if (this.checked) {
            expiryInput.value = ''; // Clear the expiry date
        } else {
            expiryInput.value = '{{ $user->expiry_time ? \Carbon\Carbon::parse($user->expiry_time)->format('Y-m-d') : null }}'; // Restore the previous value if unchecked
        }
    });
</script>
