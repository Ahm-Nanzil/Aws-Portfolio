@if(isset($visit))
    {{ Form::model($visit, array('route' => array('deals.visit.update', $deal->id, $visit->id), 'method' => 'PUT','class' => 'modalForm')) }}
@else
    {{ Form::open(array('route' => ['deals.visit.store',$deal->id], 'class' => 'modalForm')) }}
@endif
<div class="modal-body">
    <div class="row">
        <input type="hidden" name="file_ids[]" id="file-ids-input">
        <div class="col-12 form-group">
            {{ Form::label('title', __('Title'),['class'=>'form-label']) }}
            {{ Form::text('title', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', null, array('class' => 'summernote-simple','id'=>'summernote')) }}

        </div>

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('assigned_users', __('Assigned Users'), ['class'=>'form-label']) }}
                {{ Form::select(
                    'assigned_users[]',
                    $assignedUsers,  // Ensure this array is populated
                    isset($visit) ? (is_array($visit->assigned_users) ? $visit->assigned_users : json_decode($visit->assigned_users, true)) : null,
                    ['class' => 'form-control select2', 'multiple' => '', 'id' => 'assigned-users-select', 'required' => 'required']
                ) }}
            </div>
        </div>

        <div class="col-6 form-group">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}
            {{ Form::date('date', null, array('class' => 'form-control','required'=>'required','min' => \Carbon\Carbon::today()->toDateString())) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('time', __('Time'),['class'=>'form-label']) }}
            {{ Form::time('time', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('location', __('Location'),['class'=>'form-label']) }}
            {{ Form::text('location', null, array('class' => 'form-control','required'=>'required')) }}
        </div>

        <div class="col-6 form-group">
            {{ Form::label('status', __('Status'),['class'=>'form-label']) }}
            <select class="form-control select2" name="status" id="choices-multiple2" required>
                @foreach($status as $key => $st)
                    <option value="{{$key}}" @if(isset($visit) && $visit->status == $key) selected @endif>{{__($st)}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 form-group">
            <label for="recurrence-toggle" class="form-label">
                <input type="checkbox" id="recurrence-toggle"> {{ __('Enable Recurrence') }}
            </label>
        </div>
        <input type="hidden" id="recurrence-status-hidden" name="recurrence_status" value="0">

        <div id="recurrence-fields" style="display: none;">

            <div class="col-6 form-group">
                {{ Form::label('recurrence', __('Recurrence'), ['class'=>'form-label']) }}
                <select class="form-control select2" name="recurrence" id="recurrence-dropdown" required>
                    @foreach($recurrances as $key => $recurrance)
                        <option value="{{ $key }}" @if(isset($view) && $view->recurrance == $key) selected @endif>
                            {{ __($recurrance) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 form-group">
                {{ Form::label('repeat_interval', __('Repeat Interval'), ['class'=>'form-label']) }}
                {{ Form::number('repeat_interval', null, ['class' => 'form-control', 'min' => 1]) }}
            </div>

            <div class="col-6 form-group">
                {{ Form::label('end_recurrence', __('End Recurrence'), ['class'=>'form-label']) }}
                {{ Form::date('end_recurrence', null, ['class' => 'form-control','min' => \Carbon\Carbon::today()->toDateString()]) }}
            </div>

            <div class="col-6 form-group">
                {{ Form::label('reminder', __('Reminder'),['class'=>'form-label']) }}
                {{ Form::time('reminder', null, array('class' => 'form-control')) }}
            </div>
        </div>
        <script>
            $(document).on('change', '#recurrence-toggle', function () {
                const recurrenceFields = $('#recurrence-fields');
                const hiddenInput = $('#recurrence-status-hidden');

                if ($(this).is(':checked')) {
                    recurrenceFields.show();
                    hiddenInput.val('1');
                } else {
                    recurrenceFields.hide();
                    hiddenInput.val('0');
                }
            });


        </script>


        <div class="col-6 form-group">
            <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="{{ __('Recommended size 32x32') }}"></i>

            {{ Form::label('files', __('Files'), ['class' => 'form-label']) }}
            {{ Form::file('files[]', ['class' => 'form-control', 'placeholder' => __('Files')]) }}
        </div>


    </div>

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    @if(isset($visit))
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    @else
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    @endif

</div>
{{Form::close()}}


<script>
    $('#date').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
        singleDatePicker: true,
    });
    $("#time").timepicker({
        icons: {
            up: 'ti ti-chevron-up',
            down: 'ti ti-chevron-down'
        }
    });
</script>

{{-- <script>
    var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: '#lead-sidenav',
        offset: 300
    })
    Dropzone.autoDiscover = false;
    Dropzone.autoDiscover = false;
    myDropzone = new Dropzone("#dropzonewidget", {
        maxFiles: 20,
        // maxFilesize: 20,
        parallelUploads: 1,
        filename: false,
        // acceptedFiles: ".jpeg,.jpg,.png,.pdf,.doc,.txt",
        url: "{{route('deals.file.upload',$deal->id)}}",
        success: function (file, response) {
            if (response.is_success) {
                addFileIdToHiddenField(response.file_id); // Assume response.file_id contains the uploaded file's ID

                dropzoneBtn(file, response);
            } else {
                myDropzone.removeFile(file);
                show_toastr('error', response.error, 'error');
            }
        },
        error: function (file, response) {
            myDropzone.removeFile(file);
            if (response.error) {
                show_toastr('error', response.error, 'error');
            } else {
                show_toastr('error', response, 'error');
            }
        }
    });
    myDropzone.on("sending", function (file, xhr, formData) {
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append("lead_id", {{$deal->id}});
    });

    function dropzoneBtn(file, response) {
        var download = document.createElement('a');
        download.setAttribute('href', response.download);
        download.setAttribute('class', "badge bg-info mx-1");
        download.setAttribute('data-toggle', "tooltip");
        download.setAttribute('data-original-title', "{{__('Download')}}");
        download.innerHTML = "<i class='ti ti-download'></i>";

        var del = document.createElement('a');
        del.setAttribute('href', response.delete);
        del.setAttribute('class', "badge bg-danger mx-1");
        del.setAttribute('data-toggle', "tooltip");
        del.setAttribute('data-original-title', "{{__('Delete')}}");
        del.innerHTML = "<i class='ti ti-trash'></i>";

        del.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (confirm("Are you sure ?")) {
                var btn = $(this);
                $.ajax({
                    url: btn.attr('href'),
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    type: 'DELETE',
                    success: function (response) {
                        if (response.is_success) {
                            btn.closest('.dz-image-preview').remove();
                        } else {
                            show_toastr('error', response.error, 'error');
                        }
                    },
                    error: function (response) {
                        response = response.responseJSON;
                        if (response.is_success) {
                            show_toastr('error', response.error, 'error');
                        } else {
                            show_toastr('error', response, 'error');
                        }
                    }
                })
            }
        });

        var html = document.createElement('div');
        html.appendChild(download);
        @if(Auth::user()->type != 'client')
        @can('edit lead')
        html.appendChild(del);
        @endcan
        @endif

        file.previewTemplate.appendChild(html);
    }
    function addFileIdToHiddenField(fileId) {
    // Get the hidden input field
    var fileIdsInput = document.getElementById('file-ids-input');
    // Add the new file ID to the value (assuming IDs are comma-separated)
    if (fileIdsInput.value) {
        fileIdsInput.value += ',' + fileId;
    } else {
        fileIdsInput.value = fileId;
    }
}


@if(!empty($visitFiles) && $visitFiles->isNotEmpty())
    @foreach($visitFiles as $file)
        @if (file_exists(storage_path('lead_files/'.$file->file_path)))
            // Create the mock file:
            var mockFile = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('lead_files/'.$file->file_path))}}};
            // Call the default addedfile event handler
            myDropzone.emit("addedfile", mockFile);
            // And optionally show the thumbnail of the file:
            myDropzone.emit("thumbnail", mockFile, "{{ asset('storage/lead_files/' . $file->file_path) }}");
            myDropzone.emit("complete", mockFile);

            dropzoneBtn(mockFile, {
                download: "{{route('deals.file.download',[$deal->id,$file->id])}}",
                delete: "{{route('deals.file.delete',[$deal->id,$file->id])}}"
            });
        @endif
    @endforeach
@endif


    @can('edit lead')
    $('.summernote-simple').on('summernote.blur', function () {

        $.ajax({
            url: "{{route('deals.note.store',$deal->id)}}",
            data: {_token: $('meta[name="csrf-token"]').attr('content'), notes: $(this).val()},
            type: 'POST',
            success: function (response) {
                if (response.is_success) {
                    // show_toastr('Success', response.success,'success');
                } else {
                    show_toastr('error', response.error, 'error');
                }
            },
            error: function (response) {
                response = response.responseJSON;
                if (response.is_success) {
                    show_toastr('error', response.error, 'error');
                } else {
                    show_toastr('error', response, 'error');
                }
            }
        })
    });
    @else
    $('.summernote-simple').summernote('disable');
    @endcan

</script>


 --}}
