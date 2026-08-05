{{ Form::open(array('route' => ['customer.emails.store', $customer->id], 'class' => 'modalForm')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('template', __('Select Template'), ['class'=>'form-label']) }}
            {{ Form::select('template', $emailTemplates->pluck('subject', 'id'), null, ['class' => 'form-control', 'placeholder' => 'Choose a template...']) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('to', __('Mail To'), ['class'=>'form-label']) }}
            {{ Form::email('to', null, array('class' => 'form-control', 'required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('subject', __('Subject'), ['class'=>'form-label']) }}
            {{ Form::text('subject', null, array('class' => 'form-control', 'required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'), ['class'=>'form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'summernote-simple', 'id' => 'description')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('scheduled_time', __('Schedule Send Time'), ['class'=>'form-label']) }}
            {{ Form::datetimeLocal('scheduled_time', null, [
                'class' => 'form-control',
                'min' => now()->format('Y-m-d\TH:i')
            ]) }}
        </div>
        <div class="col-12 form-group" id="save-template-container">
            {{ Form::checkbox('save_as_template', '1', false, ['id' => 'save_as_template']) }}
            {{ Form::label('save_as_template', __('Save as Template')) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
    function initializeTemplateSelection() {
        const emailTemplates = @json($emailTemplates->keyBy('id'));
        const saveAsTemplateContainer = document.getElementById('save-template-container');

        // Hide save-as-template checkbox initially if template is selected
        document.querySelector('select[name="template"]').addEventListener('change', function() {
            const selectedTemplate = emailTemplates[this.value];
            if (selectedTemplate) {
                document.querySelector('input[name="subject"]').value = selectedTemplate.subject;
                $('#description').summernote('code', selectedTemplate.description); // Set Summernote content
                saveAsTemplateContainer.style.display = 'none'; // Hide checkbox
            } else {
                document.querySelector('input[name="subject"]').value = '';
                $('#description').summernote('code', ''); // Clear Summernote content
                saveAsTemplateContainer.style.display = 'block'; // Show checkbox
            }
        });

        // Show save-as-template checkbox if subject or description is modified
        document.querySelector('input[name="subject"]').addEventListener('input', function() {
            saveAsTemplateContainer.style.display = 'block';
        });

        $('#description').on('summernote.change', function() {
            saveAsTemplateContainer.style.display = 'block';
        });
    }

    // Initialize Summernote and template selection once modal is fully shown
    $(document).on('shown.bs.modal', function () {
        $('#description').summernote({
            height: 150, // Set desired height
            toolbar: [ // Set toolbar options if needed
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
        initializeTemplateSelection();
    });
</script>
