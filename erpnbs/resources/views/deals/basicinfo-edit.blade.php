{{ Form::model($deal, array('route' => array('deals.basicinfo.update', $deal->id), 'method' => 'PUT','class' => 'modalForm')) }}
<div class="modal-body">
    {{-- start for ai module --}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['deal']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module --}}

    <fieldset>
        <legend>{{ __('Deal Basic Info') }}</legend>
        <div class="row">
            <!-- Lead Owner -->
            <div class="col-12 form-group">
                {{ Form::label('lead_owner', __('Deal Owner'), ['class' => 'form-label']) }}
                {{ Form::select('lead_owner', $users, null, array('class' => 'form-control select', 'required' => 'required')) }}
            </div>

            <!-- Company Website -->
            <div class="col-12 form-group">
                {{ Form::label('company_website', __('Company Website'), ['class' => 'form-label']) }}
                {{ Form::text('company_website', null, array('class' => 'form-control', 'placeholder' => __('www.domain.com'))) }}
            </div>

            <!-- Company/Entity Name -->
            <div class="col-12 form-group">
                {{ Form::label('company_entity_name', __('Company / Entity Name'), ['class' => 'form-label']) }}
                {{ Form::text('company_entity_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Company / Entity Name'))) }}
            </div>

            <!-- Company/Entity Logo with Preview -->
            <div class="col-12 form-group">
                <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('Recommended size 32x32') }}"></i>
                {{ Form::label('company_entity_logo', __('Company / Entity Logo'), ['class' => 'form-label']) }}

                @if($deal->company_entity_logo )
                    <div class="my-2">
                        <img src="{{ asset('storage/app/public/' . $deal->company_entity_logo) }}" alt="{{ __('Company Logo') }}" class="img-thumbnail" style="width: 32px; height: 32px;">
                    </div>
                @endif

                {{ Form::file('company_entity_logo', ['class' => 'form-control', 'placeholder' => __('Enter Company / Entity Logo')]) }}
            </div>

            <!-- Company Phone LL1 -->
            <div class="col-12 form-group">
                {{ Form::label('company_phone_ll1', __('Company Phone LL1'), ['class' => 'form-label']) }}
                {{ Form::text('company_phone_ll1', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'))) }}
            </div>

            <!-- Company Phone LL2 -->
            <div class="col-12 form-group">
                {{ Form::label('company_phone_ll2', __('Company Phone LL2'), ['class' => 'form-label']) }}
                {{ Form::text('company_phone_ll2', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'))) }}
            </div>

            <!-- Company Email -->
            <div class="col-12 form-group">
                {{ Form::label('company_email', __('Company Email'), ['class' => 'form-label']) }}
                {{ Form::email('company_email', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'))) }}
            </div>

            <!-- Address1 -->
            <div class="col-12 form-group">
                {{ Form::label('address1', __('Address1'), ['class' => 'form-label']) }}
                {{ Form::text('address1', null, array('class' => 'form-control', 'placeholder' => __('Enter Address1'))) }}
            </div>

            <!-- Address2 -->
            <div class="col-12 form-group">
                {{ Form::label('address2', __('Address2'), ['class' => 'form-label']) }}
                {{ Form::text('address2', null, array('class' => 'form-control', 'placeholder' => __('Enter Address2'))) }}
            </div>

            <!-- City -->
            <div class="col-12 form-group">
                {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                {{ Form::text('city', null, array('class' => 'form-control', 'placeholder' => __('Enter City'))) }}
            </div>

            <!-- Region/State -->
            <div class="col-12 form-group">
                {{ Form::label('region', __('Region/State'), ['class' => 'form-label']) }}
                {{ Form::text('region', null, array('class' => 'form-control', 'placeholder' => __('Enter Region/State'))) }}
            </div>

            <!-- Country -->
            <div class="col-12 form-group">
                {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                {{ Form::text('country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country'))) }}
            </div>

            <!-- Zip Code -->
            <div class="col-12 form-group">
                {{ Form::label('zip_code', __('Zip Code'), ['class' => 'form-label']) }}
                {{ Form::text('zip_code', null, array('class' => 'form-control', 'placeholder' => __('Enter Zip Code'))) }}
            </div>

            <!-- Company LinkedIn -->
            <div class="col-12 form-group">
                {{ Form::label('company_linkedin', __('Company LinkedIn'), ['class' => 'form-label']) }}
                {{ Form::url('company_linkedin', null, array('class' => 'form-control', 'placeholder' => __('Enter LinkedIn URL'))) }}
            </div>

            <!-- Company Location -->
            <div class="col-12 form-group">
                {{ Form::label('company_location', __('Company Location'), ['class' => 'form-label']) }}
                {{ Form::text('company_location', null, array('class' => 'form-control', 'placeholder' => __('Enter Location URL'))) }}
            </div>
        </div>
    </fieldset>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>

{{ Form::close() }}
