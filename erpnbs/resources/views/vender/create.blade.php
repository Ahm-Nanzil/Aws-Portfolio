{{ Form::open(['url' => 'vender', 'method' => 'post']) }}
<div class="modal-body">
    <ul class="nav nav-tabs" id="vendorTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="basic-info-tab" data-bs-toggle="tab" href="#basic-info" role="tab">Basic Info</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="billing-shipping-tab" data-bs-toggle="tab" href="#billing-shipping" role="tab">Billing & Shipping</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="return-policy-tab" data-bs-toggle="tab" href="#return-policy" role="tab">Return Policy</a>
        </li>
    </ul>

    <div class="tab-content" id="vendorTabsContent">
        <!-- Basic Info Tab -->
        <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
            {{-- <h6 class="sub-title">{{ __('Basic Info') }}</h6> --}}
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('name', __('Name')) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('contact', __('Contact')) }}
                        {{ Form::number('contact', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Contact')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('email', __('Email')) }}
                        {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Email')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('vendor_detail', __('Vendor Detail')) }}
                        {{ Form::text('vendor_detail', null, ['class' => 'form-control', 'placeholder' => __('Enter Vendor Detail')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('custom_fields', __('Custom Fields')) }}
                        @include('customFields.formBuilder')
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('vendor_code', __('Vendor Code')) }}
                        {{ Form::text('vendor_code', null, ['class' => 'form-control', 'placeholder' => __('Enter Vendor Code')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('company', __('Company')) }}
                        {{ Form::text('company', null, ['class' => 'form-control', 'placeholder' => __('Enter Company')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('balance', __('Balance')) }}
                        {{ Form::text('balance', null, ['class' => 'form-control', 'placeholder' => __('Enter Balance')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('vat_number', __('VAT Number')) }}
                        {{ Form::text('vat_number', null, ['class' => 'form-control', 'placeholder' => __('Enter VAT Number')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('phone', __('Phone')) }}
                        {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => __('Enter Phone')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('website', __('Website')) }}
                        {{ Form::text('website', null, ['class' => 'form-control', 'placeholder' => __('Enter Website')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('currency', __('Currency')) }}
                        {{ Form::text('currency', null, ['class' => 'form-control', 'placeholder' => __('Enter Currency')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('default_language', __('Default Language')) }}
                        {{ Form::text('default_language', null, ['class' => 'form-control', 'placeholder' => __('Enter Default Language')]) }}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{ Form::label('address', __('Address')) }}
                        {{ Form::textarea('address', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Address')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('city', __('City')) }}
                        {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('Enter City')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('state', __('State')) }}
                        {{ Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('Enter State')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('zip_code', __('Zip Code')) }}
                        {{ Form::text('zip_code', null, ['class' => 'form-control', 'placeholder' => __('Enter Zip Code')]) }}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('country', __('Country')) }}
                        {{ Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('Enter Country')]) }}
                    </div>
                </div>
            </div>
        </div>


        <!-- Billing & Shipping Tab -->
        <div class="tab-pane fade" id="billing-shipping" role="tabpanel">
            {{-- <h6 class="sub-title">{{ __('Billing & Shipping Address') }}</h6> --}}
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{Form::label('billing_name',__('Name'),array('class'=>'form-label')) }}
                        {{Form::text('billing_name',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Name')))}}

                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{Form::label('billing_phone',__('Phone'),array('class'=>'form-label')) }}
                        {{Form::text('billing_phone',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Phone')))}}

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{Form::label('billing_address',__('Address'),array('class'=>'form-label')) }}
                        {{Form::textarea('billing_address',null,array('class'=>'form-control','rows'=>3 , 'placeholder'=>__('Enter Address')))}}
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{Form::label('billing_city',__('City'),array('class'=>'form-label')) }}
                        {{Form::text('billing_city',null,array('class'=>'form-control' , 'placeholder'=>__('Enter City')))}}
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{Form::label('billing_state',__('State'),array('class'=>'form-label')) }}
                        {{Form::text('billing_state',null,array('class'=>'form-control' , 'placeholder'=>_('Enter State')))}}
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{Form::label('billing_country',__('Country'),array('class'=>'form-label')) }}
                        {{Form::text('billing_country',null,array('class'=>'form-control' , 'placeholder'=> __('Enter Country')))}}

                    </div>
                </div>


                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{Form::label('billing_zip',__('Zip Code'),array('class'=>'form-label')) }}
                        {{Form::text('billing_zip',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Zip Code')))}}

                    </div>
                </div>

            </div>
            @if(App\Models\Utility::getValByName('shipping_display')=='on')
        <div class="col-md-12 text-end">
            <input type="button" id="billing_data" value="{{__('Shipping Same As Billing')}}" class="btn btn-primary">
        </div>
        <h6 class="sub-title">{{__('Shipping Address')}}</h6>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_name',__('Name'),array('class'=>'form-label')) }}
                    {{Form::text('shipping_name',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Name')))}}

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_phone',__('Phone'),array('class'=>'form-label')) }}
                    {{Form::text('shipping_phone',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Phone')))}}

                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('shipping_address',__('Address'),array('class'=>'form-label')) }}
                    {{Form::textarea('shipping_address',null,array('class'=>'form-control','rows'=>3 , 'placeholder'=>__('Enter Address')))}}
                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_city',__('City'),array('class'=>'form-label')) }}
                    {{Form::text('shipping_city',null,array('class'=>'form-control' , 'placeholder'=>__('Enter City')))}}

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_state',__('State'),array('class'=>'form-label')) }}
                    {{Form::text('shipping_state',null,array('class'=>'form-control' , 'placeholder'=>__('Enter State')))}}

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_country',__('Country'),array('class'=>'form-label')) }}
                    {{Form::text('shipping_country',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Country')))}}

                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_zip',__('Zip Code'),array('class'=>'form-label' )) }}
                    {{Form::text('shipping_zip',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Zip Code')))}}
                </div>
            </div>

        </div>
    @endif
        </div>

        <!-- Return Policy Tab -->
        <div class="tab-pane fade" id="return-policy" role="tabpanel">
            {{-- <h6 class="sub-title">{{ __('Return Policy') }}</h6> --}}
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('return_days', __('Return Request Must Be Placed Within X Days')) }}
                        {{ Form::number('return_days', null, ['class' => 'form-control', 'placeholder' => __('Enter Number of Days')]) }}
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        {{ Form::label('return_fee', __('Fee for Return Order')) }}
                        {{ Form::text('return_fee', null, ['class' => 'form-control', 'placeholder' => __('Enter Return Fee')]) }}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{ Form::label('return_policy_info', __('Return Policies Information')) }}
                        {{ Form::textarea('return_policy_info', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Return Policies Information')]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
</div>
{{ Form::close() }}
