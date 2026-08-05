{{ Form::open(array('url' => 'deals')) }}
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
        {{-- <div class="col-6 form-group">
            {{ Form::label('name', __('Deal Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('phone', __('Phone'),['class'=>'form-label']) }}
            {{ Form::text('phone', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('price', __('Price'),['class'=>'form-label']) }}
            {{ Form::number('price', 0, array('class' => 'form-control','min'=>0)) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('clients', __('Clients'),['class'=>'form-label']) }}
            {{ Form::select('clients[]', $clients,null, array('class' => 'form-control select2','multiple'=>'','id'=>'choices-multiple1','required'=>'required')) }}
            @if(count($clients) <= 0 && Auth::user()->type == 'Owner')
                <div class="text-muted text-xs">
                    {{__('Please create new clients')}} <a href="{{route('clients.index')}}">{{__('here')}}</a>.
                </div>
            @endif
        </div> --}}
            <!-- Lead Owner -->
            <div data-bs-toggle="tooltip" title="{{__('Create New Deal')}}">

            </div>
            <div class="col-6 form-group">
                {{ Form::label('lead_owner', __('Lead Owner'), ['class'=>'form-label']) }}
                {{ Form::select('lead_owner', $users, null, array('class' => 'form-control select','required'=>'required')) }}
            </div>

            <div class="col-6 form-group">
                <!-- Auto-generated part (readonly) -->
                {{ Form::label('name', __('Deal Name'), ['class'=>'form-label']) }}
                <div class="input-group">
                    <!-- Readonly auto-generated part -->
                    {{ Form::text('generated_deal_name', $generatedDealName, ['class' => 'form-control', 'readonly' => true, 'id' => 'generated_deal_name']) }}

                    <!-- Editable part for user input -->
                    {{ Form::text('deal_name_additional', null, ['class' => 'form-control', 'placeholder' => __('Add additional part')]) }}
                </div>
            </div>



<!-- Create Deal From (Radio Buttons: From Leads, From Customers) -->
<div class="col-6 form-group">
    {{ Form::label('create_from', __('Create Deal From'), ['class'=>'form-label']) }}
    <div>
        <!-- From Leads Radio Button -->
        {{ Form::radio('create_from', 'from_leads', false, ['id' => 'from_leads']) }}
        {{ Form::label('from_leads', __(' Leads')) }}

        <!-- From Customers Radio Button -->
        {{ Form::radio('create_from', 'from_customers', false, ['id' => 'from_customers']) }}
        {{ Form::label('from_customers', __(' Customers')) }}
    </div>
</div>

<!-- Lead Select (conditional dropdown based on radio button selection) -->
<div class="col-6 form-group conditional-lead" style="display: none;">
    {{ Form::label('lead_select', __('Lead Select'), ['class'=>'form-label']) }}
    {{ Form::select('lead_select', $leads, null, array('class' => 'form-control select')) }}
</div>

<!-- Customer Select (conditional dropdown based on radio button selection) -->
<div class="col-6 form-group conditional-customer" style="display: none;">
    {{ Form::label('customer_select', __('Customer Select'), ['class'=>'form-label']) }}
    {{ Form::select('customer_select', $clients, null, array('class' => 'form-control select')) }}
</div>

<script>
    $(document).ready(function() {
    // When the radio button is clicked, toggle the visibility of the select dropdowns
    $('input[type=radio][name=create_from]').change(function() {
        if (this.value === 'from_leads') {
            $('.conditional-lead').show();     // Show the Lead select dropdown
            $('.conditional-customer').hide(); // Hide the Customer select dropdown
        }
        else if (this.value === 'from_customers') {
            $('.conditional-lead').hide();     // Hide the Lead select dropdown
            $('.conditional-customer').show(); // Show the Customer select dropdown
        }
    });
});

</script>

            <!-- Primary Contact (conditional dropdown based on lead/customer selection) -->
            {{-- <div class="col-6 form-group">
                {{ Form::label('primary_contact', __('Primary Contact'), ['class'=>'form-label']) }}
                {{ Form::select('primary_contact', $contacts, null, array('class' => 'form-control select')) }}
            </div> --}}

            <!-- Additional Contacts (Conditional dropdown, multiple selections) -->
            {{-- <div class="col-6 form-group">
                {{ Form::label('additional_contacts', __('Additional Contacts'), ['class'=>'form-label']) }}
                {{ Form::select('additional_contacts[]', $additional_contacts, null, array('class' => 'form-control select2','multiple'=>'multiple')) }}
            </div> --}}

            <!-- Deal Source -->
            <div class="col-6 form-group">
                {{ Form::label('deal_source', __('Deal Source'), ['class'=>'form-label']) }}
                {{ Form::select('deal_source', $sources, null, array('class' => 'form-control select')) }}
            </div>

                               <!-- Pipeline Dropdown -->
<div class="col-6 form-group">
    {{ Form::label('pipeline', __('Pipeline'), ['class' => 'form-label']) }}
    <select id="pipeline" class="form-control select">
        <option value="">{{ __('Select Pipeline') }}</option>
        @foreach($pipelines as $pipeline_id => $pipeline)
            <option value="{{ $pipeline_id }}">{{ $pipeline['name'] }}</option>
        @endforeach
    </select>
</div>

<!-- Lead Stage Dropdown -->
<div class="col-6 form-group">
    {{ Form::label('lead_stage', __('Lead Stage'), ['class' => 'form-label']) }}
    <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('Please select Pipeline to see option') }}"></i>

    <select id="lead_stage" class="form-control select">
        <option value="">{{ __('Select Lead Stage') }}</option>
    </select>
</div>

<script>
    $(document).ready(function() {
        var pipelines = @json($pipelines); // Passing the pipelines data to JavaScript

        $('#pipeline').change(function() {
            var pipelineId = $(this).val();
            var leadStageDropdown = $('#lead_stage');

            // Clear the lead stages dropdown
            leadStageDropdown.empty();
            leadStageDropdown.append('<option value="">{{ __('Select Lead Stage') }}</option>');

            // Check if a valid pipeline is selected
            if (pipelineId && pipelines[pipelineId]) {
                // Loop through the stages and append them to the lead stage dropdown
                pipelines[pipelineId]['lead_stages'].forEach(function(stage) {
                    leadStageDropdown.append('<option value="' + stage.id + '">' + stage.name + '</option>');
                });
            }
        });
    });
</script>

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




    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
