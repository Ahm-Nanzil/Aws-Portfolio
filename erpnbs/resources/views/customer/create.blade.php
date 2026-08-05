{{Form::open(array('url'=>'customer','method'=>'post','class' => 'modalForm','enctype' => 'multipart/form-data'))}}
<div class="modal-body">



    <fieldset>
        <legend>{{ __(' Basic Info') }}</legend>

        <div class="row">
            {{-- <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="form-group">
                    {{Form::label('name',__('Name'),array('class'=>'form-label')) }}
                    {{Form::text('name',null,array('class'=>'form-control','required'=>'required' , 'placeholder'=>__('Enter Name')))}}
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="form-group">
                    {{Form::label('contact',__('Contact'),['class'=>'form-label'])}}
                    {{Form::number('contact',null,array('class'=>'form-control','required'=>'required' , 'placeholder'=>__('Enter Contact')))}}
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="form-group">
                    {{Form::label('email',__('Email'),['class'=>'form-label'])}}
                    {{Form::email('email',null,array('class'=>'form-control' , 'placeholder'=>__('Enter email'),'required'=>'required'))}}
                </div>
            </div> --}}


            @if(!$customFields->isEmpty())
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                        @include('customFields.formBuilder')
                    </div>
                </div>
            @endif
        </div>

        <div class="row">




        <div class="col-6 form-group">
            {{ Form::label('company_website', __('Company Website'), ['class' => 'form-label']) }}
            {{ Form::text('company_website', null, [
                'id' => 'company_website',
                'class' => 'form-control',
                'placeholder' => __('www.domain.com'),
                'required' => 'required'
            ]) }}
            <!-- Feedback placeholder -->
            <small id="website-feedback" class="text-danger" style="display: none;"></small>
        </div>


            <script>
                document.addEventListener('DOMContentLoaded', function () {
                const websiteField = document.querySelector('#company_website');
                const feedback = document.querySelector('#website-feedback');

                websiteField.addEventListener('blur', function () {
                    const website = websiteField.value.trim();

                    if (website) {
                        fetch('/check-duplicate-website', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({ website }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                feedback.textContent = 'This website is already registered.';
                                feedback.style.display = 'block'; // Show feedback message
                                websiteField.classList.add('is-invalid'); // Highlight the input field
                            } else {
                                feedback.textContent = '';
                                feedback.style.display = 'none'; // Hide feedback message
                                websiteField.classList.remove('is-invalid'); // Remove highlight
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    }
                });
            });


            </script>


            <!-- Company/Entity Name -->
            <div class="col-6 form-group">
                {{ Form::label('company_entity_name', __('Company / Entity Name'), ['class' => 'form-label']) }}
                {{ Form::text('company_entity_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Company / Entity Name'), 'required' => 'required')) }}
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
                {{ Form::email('company_email', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'))) }}
            </div>

            <!-- Address1 -->
            <div class="col-6 form-group">
                {{ Form::label('address1', __('Address1'), ['class' => 'form-label']) }}
                {{ Form::text('address1', null, array('class' => 'form-control', 'placeholder' => __('Enter Address1'),'required' => 'required')) }}
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
                {{ Form::text('country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country'),'required' => 'required')) }}
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


            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="form-group">
                    {{Form::label('tax_number',__('Tax Number'),['class'=>'form-label'])}}
                    {{Form::text('tax_number',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Tax Number')))}}
                </div>
            </div>
            <div class="col-6 form-group">
                {{ Form::label('pipeline', __('Pipeline'), ['class' => 'form-label']) }}
                <select id="pipeline" name="pipeline_id" class="form-control select" required>
                    <option value="">{{ __('Select Pipeline') }}</option>
                    @foreach($pipelines as $pipeline)
                        <option value="{{ $pipeline['id'] }}">{{ $pipeline['name'] }}</option>
                    @endforeach
                </select>
            </div>


            <div class="col-6 form-group">
                {{ Form::label('customer_stage', __('Customer Stage'), ['class' => 'form-label']) }}
                <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('Please select Pipeline to see options') }}"></i>

                <select id="stage_id" name="stage_id" class="form-control select" required>
                    <option value="" >{{ __('Select  Stage') }}</option>
                </select>
            </div>

            <div class="col-12 form-group">
                {{ Form::label('sources', __('Sources'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('sources[]', $sources,null, array('class' => 'form-control select2','id'=>'choices-multiple1','multiple'=>'','required'=>'required')) }}
            </div>

            <script>
                $(document).ready(function () {
                    // Get all stages from the Blade view and parse to JavaScript object
                    var stages = @json($stages);

                    $('#pipeline').change(function () {
                        var pipelineId = $(this).val(); // Get the selected pipeline ID
                        var dealStageDropdown = $('#stage_id'); // Reference to deal stage dropdown

                        // Debugging: Log the selected pipeline ID
                        console.log("Selected Pipeline ID:", pipelineId);

                        // Clear the deal stage dropdown
                        dealStageDropdown.empty();
                        dealStageDropdown.append('<option value="">{{ __('---') }}</option>');

                        // Check if a valid pipeline is selected
                        if (pipelineId) {
                            // Filter stages by the selected pipeline ID
                            var filteredStages = stages.filter(function (stage) {
                                return stage.pipeline_id == pipelineId;
                            });

                            // Debugging: Log the filtered stages
                            console.log("Filtered Stages:", filteredStages);

                            // Populate deal stage dropdown with filtered stages
                            filteredStages.forEach(function (stage) {
                                dealStageDropdown.append('<option value="' + stage.id + '">' + stage.name + '</option>');
                            });
                        }
                    });
                });
            </script>
        </div>
    </fieldset>

    <h6 class="sub-title">{{__('Billing Address')}}</h6>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_name',__('Name'),array('class'=>'','class'=>'form-label')) }}
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
                {{Form::text('billing_state',null,array('class'=>'form-control' , 'placeholder'=>__('Enter State')))}}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_country',__('Country'),array('class'=>'form-label')) }}
                {{Form::text('billing_country',null,array('class'=>'form-control' , 'placeholder'=>__('Enter Country')))}}
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
                    <label class="form-label" for="example2cols1Input"></label>
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
                    {{Form::label('shipping_zip',__('Zip Code'),array('class'=>'form-label')) }}
                    {{Form::text('shipping_zip',null,array('class'=>'form-control' ,'placeholder'=>__('Enter Zip Code')))}}

                </div>
            </div>

        </div>
    @endif




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
                {{ Form::email('email_work', null, array('class' => 'form-control', 'placeholder' => __('name@domain.com'),'required' => 'required')) }}
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
                {{ Form::text('company_phone_ll', null, array('class' => 'form-control', 'placeholder' => __('+966-11-1234567'),'required' => 'required')) }}
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

    <!-- Note -->
    <div class="col-12 form-group">
        {{ Form::label('notes', __('Note'), ['class' => 'form-label']) }}
        {{ Form::textarea('notes', null, array('class' => 'form-control', 'placeholder' => __('Enter Note'))) }}
    </div>

    <fieldset>
        <legend>{{ __('Additional Contacts:') }}
            <button type="button" class="btn btn-primary" onclick="addContact()">
                <i class="ti ti-plus"></i>
            </button>
        </legend>
        <div id="additional-contacts">
            <div class="contact-group">
                <div class="row">
                    <!-- Contact Name -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Contact Name') }}</label>
                            <input type="text" class="form-control" name="additional_contacts[0][name]" placeholder="{{ __('Enter Contact Name') }}" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Email') }}</label>
                            <input type="email" class="form-control" name="additional_contacts[0][email]" placeholder="{{ __('Enter Email') }}" required>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Title') }}</label>
                            <input type="text" class="form-control" name="additional_contacts[0][title]" placeholder="{{ __('Enter Title') }}" required>
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Department') }}</label>
                            <input type="text" class="form-control" name="additional_contacts[0][department]" placeholder="{{ __('Enter Department') }}" required>
                        </div>
                    </div>

                    <!-- Phone Mobile -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Phone Mobile') }}</label>
                            <input type="text" class="form-control" name="additional_contacts[0][phone_mobile]" placeholder="{{ __('Enter Phone Mobile') }}" required>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger" onclick="removeContact(this)">
                    <i class="ti ti-trash"></i> {{ __('Delete Contact') }}
                </button>
                <hr>
            </div>
        </div>
    </fieldset>

    <script>
    let contactIndex = 1; // Start with the first index for additional contacts

    function addContact() {
        const contactGroup = document.createElement('div');
        contactGroup.classList.add('contact-group');
        contactGroup.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Contact Name') }}</label>
                        <input type="text" class="form-control" name="additional_contacts[${contactIndex}][name]" placeholder="{{ __('Enter Contact Name') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Email') }}</label>
                        <input type="email" class="form-control" name="additional_contacts[${contactIndex}][email]" placeholder="{{ __('Enter Email') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Title') }}</label>
                        <input type="text" class="form-control" name="additional_contacts[${contactIndex}][title]" placeholder="{{ __('Enter Title') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Department') }}</label>
                        <input type="text" class="form-control" name="additional_contacts[${contactIndex}][department]" placeholder="{{ __('Enter Department') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Phone Mobile') }}</label>
                        <input type="text" class="form-control" name="additional_contacts[${contactIndex}][phone_mobile]" placeholder="{{ __('Enter Phone Mobile') }}" required>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-danger" onclick="removeContact(this)">
                <i class="ti ti-trash"></i> {{ __('Delete Contact') }}
            </button>
            <hr>
        `;
        document.getElementById('additional-contacts').appendChild(contactGroup);
        contactIndex++; // Increment the index for the next contact
    }

    function removeContact(button) {
        const contactGroup = button.closest('.contact-group');
        contactGroup.remove(); // Remove the selected contact group
    }
    </script>

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{Form::close()}}
