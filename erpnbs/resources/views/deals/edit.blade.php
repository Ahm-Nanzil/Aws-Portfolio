{{ Form::model($deal, array('route' => array('deals.update', $deal->id), 'method' => 'PUT', 'class' => 'modalForm')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();

    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['deal']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif

    {{-- end for ai module--}}
    <div class="row">

            <!-- Lead Owner -->
            <div data-bs-toggle="tooltip" title="{{__('Create New Deal')}}">

            </div>
            <div class="col-6 form-group">
                {{ Form::label('deal_owner', __('Deal Owner'), ['class'=>'form-label']) }}
                {{ Form::select('deal_owner', $users, null, array('class' => 'form-control select','required'=>'required')) }}
            </div>

            <div class="col-6 form-group">
                <!-- Auto-generated part (readonly) -->
                {{ Form::label('name', __('Deal Name'), ['class'=>'form-label']) }}
                <div class="input-group">
                    <!-- Readonly auto-generated part -->
                    {{ Form::text('generated_deal_name', $generatedDealName, ['class' => 'form-control', 'readonly' => true, 'id' => 'generated_deal_name']) }}

                    <!-- Editable part for user input -->
                    {{ Form::text('deal_name_additional',$deal_name_additional, null, ['class' => 'form-control', 'placeholder' => __('Add additional part')]) }}
                </div>
            </div>





            <!-- Lead Select (conditional dropdown based on radio button selection) -->
            <div class="col-6 form-group conditional-lead" style="display: none;">
                {{ Form::label('lead_select', __('Lead Select'), ['class'=>'form-label']) }}
                {{ Form::select('lead_id', $leads, null, array('class' => 'form-control select')) }}
            </div>

            <!-- Customer Select (conditional dropdown based on radio button selection) -->
            <div class="col-6 form-group conditional-customer" style="display: none;">
                {{ Form::label('clients', __('Customer Select'), ['class'=>'form-label']) }}
                {{ Form::select('clients[]', $clients, null, array('class' => 'form-control select')) }}
            </div>






            <!-- Deal Source -->
            <div class="col-6 form-group">
                {{ Form::label('deal_source', __('Deal Source'), ['class'=>'form-label']) }}
                {{ Form::select('deal_source', $sources, null, array('class' => 'form-control select')) }}
            </div>

            <div class="col-6 form-group">
                {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control select','required'=>'required')) }}
            </div>
            <div class="col-6 form-group">
                {{ Form::label('stage_id', __('Deal Stage'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control select','required'=>'required')) }}
            </div>






            <!-- Deal Amount -->
            <div class="col-6 form-group">
                {{ Form::label('price', __('Deal Amount'),['class'=>'form-label']) }}
                {{ Form::number('price', 0, array('class' => 'form-control','min'=>0)) }}
            </div>

            <!-- Inquiry Details (textarea and multiple file attachment) -->
            <div class="col-6 form-group">
                {{ Form::label('inquiry_details', __('Inquiry Details'), ['class' => 'form-label']) }}
                {{ Form::textarea('inquiry_details', null, ['class' => 'form-control', 'rows' => 3]) }}

                <!-- File input field -->
                {{ Form::file('attachments[]', ['multiple' => true, 'class' => 'form-control-file', 'id' => 'attachments']) }}

                <!-- Placeholder to display selected file names -->
                <div id="file-list" class="mt-2"></div>
            </div>



            <!-- Note -->
            <div class="col-6 form-group">
                {{ Form::label('note', __('Note'), ['class'=>'form-label']) }}
                <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('If you want to add any others information') }}"></i>

                {{ Form::textarea('note', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>

        <div class="othersInfo" style="display: inline;">


            <fieldset>
                <legend>{{ __('Basic Info') }}</legend>

                <div class="row">


                    <!-- Company Website -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_website', __('Company Website'), ['class' => 'form-label']) }}
                        {{ Form::text('company_website', null, array('class' => 'form-control', 'placeholder' => __('www.domain.com'), 'required' => 'required'))}}
                    </div>

                    <!-- Company/Entity Name -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_entity_name', __('Company / Entity Name'), ['class' => 'form-label']) }}
                        {{ Form::text('company_entity_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Company / Entity Name'), 'required' => 'required')) }}
                    </div>

                    <div class="my-2">
                        <img src="{{ asset('storage/app/public/' . $deal->company_entity_logo) }}" alt="{{ __('Company Logo') }}" class="img-thumbnail" style="width: 50px; height: 50px;">
                    </div>
                    <!-- Company/Entity Logo -->
                    <div class="col-6 form-group">
                        <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('Recommended size 32x32') }}"></i>

                        {{ Form::label('company_entity_logo', __('Company / Entity Logo'), ['class' => 'form-label']) }}
                        {{ Form::file('company_entity_logo', ['class' => 'form-control', 'placeholder' => __('Enter Company / Entity Logo')]) }}
                    </div>


                    <!-- Company Phone LL1 -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_phone_ll1', __('Company Phone LL1'), ['class' => 'form-label']) }}
                        {{ Form::text('company_phone_ll1', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'),'required' => 'required')) }}
                    </div>

                    <!-- Company Phone LL2 -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_phone_ll2', __('Company Phone LL2'), ['class' => 'form-label']) }}
                        {{ Form::text('company_phone_ll2', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'),'required' => 'required')) }}
                    </div>

                    <!-- Company Email -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_email', __('Company Email'), ['class' => 'form-label']) }}
                        {{ Form::email('company_email', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'),'required' => 'required')) }}
                    </div>

                    <!-- Address1 -->
                    <div class="col-6 form-group">
                        {{ Form::label('address1', __('Address1'), ['class' => 'form-label']) }}
                        {{ Form::text('address1', null, array('class' => 'form-control', 'placeholder' => __('Enter Address1'),'required' => 'required')) }}
                    </div>

                    <!-- Address2 -->
                    <div class="col-6 form-group">
                        {{ Form::label('address2', __('Address2'), ['class' => 'form-label']) }}
                        {{ Form::text('address2', null, array('class' => 'form-control', 'placeholder' => __('Enter Address2'),'required' => 'required')) }}
                    </div>

                    <!-- City -->
                    <div class="col-6 form-group">
                        {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                        {{ Form::text('city', null, array('class' => 'form-control', 'placeholder' => __('Enter City'),'required' => 'required')) }}
                    </div>

                    <!-- Region/State -->
                    <div class="col-6 form-group">
                        {{ Form::label('region', __('Region/State'), ['class' => 'form-label']) }}
                        {{ Form::text('region', null, array('class' => 'form-control', 'placeholder' => __('Enter Region/State'),'required' => 'required')) }}
                    </div>

                    <!-- Country -->
                    <div class="col-6 form-group">
                        {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                        {{ Form::text('country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country'),'required' => 'required')) }}
                    </div>

                    <!-- Zip Code -->
                    <div class="col-6 form-group">
                        {{ Form::label('zip_code', __('Zip Code'), ['class' => 'form-label']) }}
                        {{ Form::text('zip_code', null, array('class' => 'form-control', 'placeholder' => __('Enter Zip Code'),'required' => 'required')) }}
                    </div>

                    <!-- Company LinkedIn -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_linkedin', __('Company LinkedIn'), ['class' => 'form-label']) }}
                        {{ Form::url('company_linkedin', null, array('class' => 'form-control', 'placeholder' => __('Enter LinkedIn URL'),'required' => 'required')) }}
                    </div>

                    <!-- Company Location -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_location', __('Company Location'), ['class' => 'form-label']) }}
                        {{ Form::text('company_location', null, array('class' => 'form-control', 'placeholder' => __('Enter Location URL'),'required' => 'required')) }}
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>{{ __('Primary Contact Info') }}</legend>

                <div class="row">
                    <!-- Salutation -->
                    <div class="col-6 form-group">
                        {{ Form::label('salutation', __('Salutation'), ['class' => 'form-label']) }}
                        {{ Form::select('salutation', ['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms'], null, array('class' => 'form-control select', 'required' => 'required')) }}
                    </div>

                    <!-- First Name -->
                    <div class="col-6 form-group">
                        {{ Form::label('first_name', __('First Name'), ['class' => 'form-label']) }}
                        {{ Form::text('first_name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter First Name'),'required' => 'required')) }}
                    </div>

                    <!-- Last Name -->
                    <div class="col-6 form-group">
                        {{ Form::label('last_name', __('Last Name'), ['class' => 'form-label']) }}
                        {{ Form::text('last_name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Last Name'),'required' => 'required')) }}
                    </div>

                    <!-- Mobile Primary -->
                    <div class="col-6 form-group">
                        {{ Form::label('mobile_primary', __('Mobile Primary'), ['class' => 'form-label']) }}
                        {{ Form::text('mobile_primary', null, array('class' => 'form-control', 'placeholder' => __('+966(0)541145867'),'required' => 'required')) }}
                    </div>

                    <!-- Mobile Secondary -->
                    <div class="col-6 form-group">
                        {{ Form::label('mobile_secondary', __('Mobile Secondary'), ['class' => 'form-label']) }}
                        {{ Form::text('mobile_secondary', null, array('class' => 'form-control', 'placeholder' => __('+966(0)541145867'),'required' => 'required')) }}
                    </div>

                    <!-- Email Work -->
                    <div class="col-6 form-group">
                        {{ Form::label('email_work', __('Email Work'), ['class' => 'form-label']) }}
                        {{ Form::email('email_work', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'),'required' => 'required')) }}
                    </div>

                    <!-- Email Personal -->
                    <div class="col-6 form-group">
                        {{ Form::label('email_personal', __('Email Personal'), ['class' => 'form-label']) }}
                        {{ Form::email('email_personal', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'),'required' => 'required')) }}
                    </div>

                    <!-- Phone LL -->
                    <div class="col-6 form-group">
                        {{ Form::label('phone_ll', __('Phone LL'), ['class' => 'form-label']) }}
                        {{ Form::text('phone_ll', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'),'required' => 'required')) }}
                    </div>

                    <div class="col-6 form-group">
                        {{ Form::label('company_phone_ll', __('Company Pone LL'), ['class' => 'form-label']) }}
                        {{ Form::text('company_phone_ll', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'),'required' => 'required')) }}
                    </div>

                    <!-- Extension # -->
                    <div class="col-6 form-group">
                        {{ Form::label('extension', __('Extension #'), ['class' => 'form-label']) }}
                        {{ Form::number('extension', null, array('class' => 'form-control', 'placeholder' => __('Enter Extension Number'),'required' => 'required')) }}
                    </div>

                    <!-- LinkedIn Profile -->
                    <div class="col-6 form-group">
                        {{ Form::label('linkedin_profile', __('LinkedIn Profile'), ['class' => 'form-label']) }}
                        {{ Form::url('linkedin_profile', null, array('class' => 'form-control', 'placeholder' => __('Enter LinkedIn URL'),'required' => 'required')) }}
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>{{ __('Additional Info:') }}</legend>

                <div class="row">






                    <!-- Currency -->
                    <div class="col-6 form-group">
                        {{ Form::label('currency', __('Currency'), ['class' => 'form-label']) }}
                        {{ Form::text('currency', null, array('class' => 'form-control', 'placeholder' => __('Enter Currency Symbol'))) }}
                    </div>

                    <!-- Industry -->
                    <div class="col-6 form-group">
                        {{ Form::label('industry', __('Industry'), ['class' => 'form-label']) }}
                        {{ Form::text('industry', null, array('class' => 'form-control', 'placeholder' => __('Enter Industry'))) }}
                    </div>


                </div>
            </fieldset>


        </div>


    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
<script>
    var stage_id = '{{$deal->stage_id}}';

    $(document).ready(function () {
        var pipeline_id = $('[name=pipeline_id]').val();
        getStages(pipeline_id);
    });

    $(document).on("change", "#commonModal select[name=pipeline_id]", function () {
        var currVal = $(this).val();
        console.log('current val ', currVal);
        getStages(currVal);
    });

    function getStages(id) {
        $.ajax({
            url: '{{route('deals.json')}}',
            data: {pipeline_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var stage_cnt = Object.keys(data).length;
                $("#stage_id").empty();
                if (stage_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';
                        if (key == '{{ $deal->stage_id }}') {
                            select = 'selected';
                        }
                        $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    }
</script>
