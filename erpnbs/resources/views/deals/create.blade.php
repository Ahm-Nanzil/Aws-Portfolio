{{ Form::open(array('url' => 'deals', 'enctype' => 'multipart/form-data', 'class' => 'modalForm')) }}
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
                {{ Form::select('deal_owner', $users, null, array('class' => 'form-control select')) }}
            </div>

            <div class="col-6 form-group">
                {{ Form::label('name', __('Deal Name'), ['class'=>'form-label']) }}
                <div class="input-group">
                    {{ Form::text('generated_deal_name', $generatedDealName, ['class' => 'form-control', 'readonly' => true, 'id' => 'generated_deal_name']) }}

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

<div class="conditional-lead" style="display: none;">

<!-- Lead Select (conditional dropdown based on radio button selection) -->
<div class="col-6 form-group">
    {{ Form::label('lead_select', __('Lead Select'), ['class'=>'form-label']) }}
    {{ Form::select('lead_id', $leads, null, array('class' => 'form-control select')) }}
</div>

<!-- Customer Pipeline Dropdown -->
<div class="col-6 form-group">
    {{ Form::label('customer_pipeline', __('Customer Pipeline'), ['class' => 'form-label']) }}
    <select id="customer_pipeline" name="customer_pipeline_id" class="form-control select" >
        <option value="">{{ __('Select Customer Pipeline') }}</option>
        @foreach($pipelines as $pipeline)
            <option value="{{ $pipeline['id'] }}">{{ $pipeline['name'] }}</option>
        @endforeach
    </select>
</div>

<!-- Customer Stage Dropdown -->
<div class="col-6 form-group">
    {{ Form::label('customer_stage', __('Customer Stage'), ['class' => 'form-label']) }}
    <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('Please select Customer Pipeline to see options') }}"></i>
    <select id="customer_stage" name="customer_stage_id" class="form-control select" >
        <option value="">{{ __('Select Customer Stage') }}</option>
    </select>
</div>
</div>

<script>
    $(document).ready(function () {
        // Get all customer stages from the Blade view and parse to JavaScript object
        var customerStages = @json($customerStages);

        function populateStages(pipelineDropdownId, stageDropdownId) {
            var pipelineDropdown = $(pipelineDropdownId); // Pipeline dropdown selector
            var stageDropdown = $(stageDropdownId); // Stage dropdown selector

            pipelineDropdown.change(function () {
                var pipelineId = $(this).val(); // Get the selected pipeline ID

                // Debugging: Log the selected pipeline ID
                console.log("Selected Pipeline ID for " + pipelineDropdownId + ":", pipelineId);

                // Clear the stage dropdown
                stageDropdown.empty();
                stageDropdown.append('<option value="">{{ __('Select Stage') }}</option>');

                // Check if a valid pipeline is selected
                if (pipelineId) {
                    // Filter customer stages by the selected pipeline ID
                    var filteredStages = customerStages.filter(function (stage) {
                        return stage.pipeline_id == pipelineId;
                    });

                    // Debugging: Log the filtered customer stages
                    console.log("Filtered Stages for " + stageDropdownId + ":", filteredStages);

                    // Populate the stage dropdown with filtered stages
                    filteredStages.forEach(function (stage) {
                        stageDropdown.append('<option value="' + stage.id + '">' + stage.name + '</option>');
                    });
                }
            });
        }

        // Initialize functionality for both pipeline-stage pairs
        populateStages('#pipeline', '#deal_stage');
        populateStages('#customer_pipeline', '#customer_stage');
    });
</script>



<!-- Customer Select (conditional dropdown based on radio button selection) -->
<div class="col-6 form-group conditional-customer" style="display: none;">
    {{ Form::label('clients', __('Customer Select'), ['class'=>'form-label']) }}
    {{ Form::select('clients[]', $clients, null, array('class' => 'form-control select')) }}
</div>

<script>
    $(document).ready(function () {
        $('input[type=radio][name=create_from]').change(function () {
            if (this.value === 'from_leads') {
                $('.conditional-lead').show();
                $('.conditional-customer').hide();
                $('.othersInfo').hide();

                // Remove 'required' attribute from fields in othersInfo
                $('.othersInfo :input').each(function () {
                    $(this).removeAttr('required');
                });

                // Add 'required' attribute to Customer Pipeline and Stage
                $('#customer_pipeline').attr('required', 'required');
                $('#customer_stage').attr('required', 'required');
            } else if (this.value === 'from_customers') {
                $('.conditional-lead').hide();
                $('.conditional-customer').show();
                $('.othersInfo').show();

                // Add 'required' attribute to fields in othersInfo
                $('.othersInfo :input').each(function () {
                    // $(this).attr('required', 'required');
                });

                // Remove 'required' attribute from Customer Pipeline and Stage
                $('#customer_pipeline').removeAttr('required');
                $('#customer_stage').removeAttr('required');
            }
        });

        // Optional: Validate Customer Stage dropdown based on Customer Pipeline selection
        $('#customer_pipeline').change(function () {
            let pipelineValue = $(this).val();
            if (pipelineValue) {
                $('#customer_stage').attr('required', 'required');
            } else {
                $('#customer_stage').removeAttr('required');
            }
        });
    });
</script>





            <!-- Deal Source -->
            <div class="col-6 form-group">
                {{ Form::label('deal_source', __('Deal Source'), ['class'=>'form-label']) }}
                {{ Form::select('deal_source', $sources, null, array('class' => 'form-control select')) }}
            </div>

                               <!-- Pipeline Dropdown -->
                            <div class="col-6 form-group">
                                {{ Form::label('pipeline', __('Deal Pipeline'), ['class' => 'form-label']) }}
                                <select id="pipeline" name="pipeline_id" class="form-control select" required>
                                    <option value="">{{ __('Select Pipeline') }}</option>
                                    @foreach($pipelines as $pipeline)
                                        <option value="{{ $pipeline['id'] }}">{{ $pipeline['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <!-- Deal Stage Dropdown -->
                            <div class="col-6 form-group">
                                {{ Form::label('deal_stage', __('Deal Stage'), ['class' => 'form-label']) }}
                                <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('Please select Pipeline to see options') }}"></i>

                                <select id="deal_stage" name="stage_id" class="form-control select" required>
                                    <option value="">{{ __('Select Deal Stage') }}</option>
                                </select>
                            </div>

                            <script>
                                $(document).ready(function () {
                                    // Get all stages from the Blade view and parse to JavaScript object
                                    var stages = @json($stages);

                                    $('#pipeline').change(function () {
                                        var pipelineId = $(this).val(); // Get the selected pipeline ID
                                        var dealStageDropdown = $('#deal_stage'); // Reference to deal stage dropdown

                                        // Debugging: Log the selected pipeline ID
                                        console.log("Selected Pipeline ID:", pipelineId);

                                        // Clear the deal stage dropdown
                                        dealStageDropdown.empty();
                                        dealStageDropdown.append('<option value="">{{ __('Select Deal Stage') }}</option>');

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

        <div class="othersInfo" style="display: none;">






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
