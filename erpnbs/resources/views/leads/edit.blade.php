{{ Form::model($lead, array('route' => array('leads.update', $lead->id), 'method' => 'PUT','class' => 'modalForm')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['lead']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">


            <!-- New fields start here -->

            <fieldset>
                <legend>{{ __('Lead Basic Info') }}</legend>

                <div class="row">
                    <!-- Lead Owner -->
                    <div class="col-6 form-group">
                        {{ Form::label('lead_owner', __('Lead Owner'), ['class' => 'form-label']) }}
                        {{ Form::select('lead_owner', $users, null, array('class' => 'form-control select')) }}
                    </div>

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
                        <img src="{{ asset('storage/app/public/' . $lead->company_entity_logo) }}" alt="{{ __('Company Logo') }}" class="img-thumbnail" style="width: 50px; height: 50px;">
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
                        {{ Form::text('company_phone_ll2', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'))) }}
                    </div>

                    <!-- Company Email -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_email', __('Company Email'), ['class' => 'form-label']) }}
                        {{ Form::email('company_email', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'),'required' => 'required')) }}
                    </div>

                    <!-- Address1 -->
                    <div class="col-6 form-group">
                        {{ Form::label('address1', __('Address1'), ['class' => 'form-label']) }}
                        {{ Form::text('address1', null, array('class' => 'form-control', 'placeholder' => __('Enter Address1'))) }}
                    </div>

                    <!-- Address2 -->
                    <div class="col-6 form-group">
                        {{ Form::label('address2', __('Address2'), ['class' => 'form-label']) }}
                        {{ Form::text('address2', null, array('class' => 'form-control', 'placeholder' => __('Enter Address2'))) }}
                    </div>

                    <!-- City -->
                    <div class="col-6 form-group">
                        {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                        {{ Form::text('city', null, array('class' => 'form-control', 'placeholder' => __('Enter City'))) }}
                    </div>

                    <!-- Region/State -->
                    <div class="col-6 form-group">
                        {{ Form::label('region', __('Region/State'), ['class' => 'form-label']) }}
                        {{ Form::text('region', null, array('class' => 'form-control', 'placeholder' => __('Enter Region/State'))) }}
                    </div>

                    <!-- Country -->
                    <div class="col-6 form-group">
                        {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                        {{ Form::text('country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country'))) }}
                    </div>

                    <!-- Zip Code -->
                    <div class="col-6 form-group">
                        {{ Form::label('zip_code', __('Zip Code'), ['class' => 'form-label']) }}
                        {{ Form::text('zip_code', null, array('class' => 'form-control', 'placeholder' => __('Enter Zip Code'))) }}
                    </div>

                    <!-- Company LinkedIn -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_linkedin', __('Company LinkedIn'), ['class' => 'form-label']) }}
                        {{ Form::url('company_linkedin', null, array('class' => 'form-control', 'placeholder' => __('Enter LinkedIn URL'))) }}
                    </div>

                    <!-- Company Location -->
                    <div class="col-6 form-group">
                        {{ Form::label('company_location', __('Company Location'), ['class' => 'form-label']) }}
                        {{ Form::text('company_location', null, array('class' => 'form-control', 'placeholder' => __('Enter Location URL'))) }}
                    </div>
                </div>
            </fieldset>


            <fieldset>
                <legend>{{ __('Primary Contact Info') }}</legend>

                <div class="row">
                    <!-- Salutation -->
                    <div class="col-6 form-group">
                        {{ Form::label('salutation', __('Salutation'), ['class' => 'form-label']) }}
                        {{ Form::select('salutation', ['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms'], null, array('class' => 'form-control select')) }}
                    </div>

                    <!-- First Name -->
                    <div class="col-6 form-group">
                        {{ Form::label('first_name', __('First Name'), ['class' => 'form-label']) }}
                        {{ Form::text('first_name', null, array('class' => 'form-control', 'placeholder' => __('Enter First Name'))) }}
                    </div>

                    <!-- Last Name -->
                    <div class="col-6 form-group">
                        {{ Form::label('last_name', __('Last Name'), ['class' => 'form-label']) }}
                        {{ Form::text('last_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Last Name'))) }}
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

                    <div class="col-6 form-group">
                        {{ Form::label('company_phone_ll', __('Company Pone LL'), ['class' => 'form-label']) }}
                        {{ Form::text('company_phone_ll', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'))) }}
                    </div>

                    <!-- Extension # -->
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

            <fieldset>
                <legend>{{ __('Additional Info:') }}</legend>

                <div class="row">

                    <!-- Pipeline Dropdown -->

                    <div class="col-6 form-group">
                        {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                        {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control select','required'=>'required')) }}
                    </div>
                    <div class="col-6 form-group">
                        {{ Form::label('stage_id', __('Stage'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                        {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control select','required'=>'required')) }}
                    </div>
                    <div class="col-12 form-group">
                        {{ Form::label('sources', __('Sources'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                        {{ Form::select('sources[]', $sources,null, array('class' => 'form-control select2','id'=>'choices-multiple1','multiple'=>'')) }}
                    </div>


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


                    <!-- Note -->
                    <div class="col-12 form-group">
                        {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}
                        {{ Form::textarea('note', null, array('class' => 'form-control', 'placeholder' => __('Enter Note'))) }}
                    </div>
                </div>
            </fieldset>
            @php
            // Assuming $lead is your Lead model instance with the additional_contacts JSON string
            $additionalContacts = json_decode($lead->additional_contacts, true) ?? []; // Decode JSON to array
            @endphp

            <fieldset>
                <legend>{{ __('Additional Contacts:') }}
                    <button type="button" class="btn btn-primary" onclick="addContact()">
                        <i class="ti ti-plus"></i>
                    </button>
                </legend>
                <div id="additional-contacts"></div>
            </fieldset>

            <script>
                let contactIndex = 0;
                const additionalContacts = <?php echo json_encode($additionalContacts); ?>;

                // Function to add a contact group to the DOM
                function addContact(contact = {}) {
                    const contactGroup = document.createElement('div');
                    contactGroup.classList.add('contact-group');
                    contactGroup.innerHTML = `
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Contact Name') }}</label>
                                    <input type="text" class="form-control" name="additional_contacts[${contactIndex}][name]" placeholder="{{ __('Enter Contact Name') }}" value="${contact.name || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Email') }}</label>
                                    <input type="email" class="form-control" name="additional_contacts[${contactIndex}][email]" placeholder="{{ __('Enter Email') }}" value="${contact.email || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Title') }}</label>
                                    <input type="text" class="form-control" name="additional_contacts[${contactIndex}][title]" placeholder="{{ __('Enter Title') }}" value="${contact.title || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Department') }}</label>
                                    <input type="text" class="form-control" name="additional_contacts[${contactIndex}][department]" placeholder="{{ __('Enter Department') }}" value="${contact.department || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Phone Mobile') }}</label>
                                    <input type="text" class="form-control" name="additional_contacts[${contactIndex}][phone_mobile]" placeholder="{{ __('Enter Phone Mobile') }}" value="${contact.phone_mobile || ''}" required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" onclick="removeContact(this)">
                            <i class="ti ti-trash"></i> {{ __('Delete Contact') }}
                        </button>
                        <hr>
                    `;
                    document.getElementById('additional-contacts').appendChild(contactGroup);
                    contactIndex++; // Increment index for the next contact
                }

                // Function to remove a contact group
                function removeContact(button) {
                    const contactGroup = button.closest('.contact-group');
                    contactGroup.remove();
                }

                // Load existing contacts on page load
                window.onload = function() {
                    additionalContacts.forEach(contact => addContact(contact));
                };
            </script>








    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}

<script>
    var stage_id = '{{$lead->stage_id}}';

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
            url: '{{route('leads.json')}}',
            data: {pipeline_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var stage_cnt = Object.keys(data).length;
                $("#stage_id").empty();
                if (stage_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';
                        if (key == '{{ $lead->stage_id }}') {
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
