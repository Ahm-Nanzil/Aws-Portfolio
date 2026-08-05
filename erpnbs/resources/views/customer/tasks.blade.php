@if(isset($task))
    {{ Form::model($task, array('route' => array('customer.tasks.update', $customer->id, $task->id), 'method' => 'PUT','class' => 'modalForm')) }}
@else
    {{ Form::open(array('route' => ['customer.tasks.store',$customer->id], 'class' => 'modalForm')) }}
@endif
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
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
            {{ Form::label('priority', __('Priority'),['class'=>'form-label']) }}
            <select class="form-control select2" name="priority" required id="choices-multiple1">
                @foreach($priorities as $key => $priority)
                    <option value="{{$key}}" @if(isset($task) && $task->priority == $key) selected @endif>{{__($priority)}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('status', __('Status'),['class'=>'form-label']) }}
            <select class="form-control select2" name="status" id="choices-multiple2" required>
                @foreach($status as $key => $st)
                    <option value="{{$key}}" @if(isset($task) && $task->status == $key) selected @endif>{{__($st)}}</option>
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

            <div class="col-12 form-group">
                {{ Form::label('recurrence', __('Recurrence'), ['class'=>'form-label']) }}
                <select class="form-control select2" name="recurrence" id="recurrence-dropdown" required>
                    @foreach($recurrances as $key => $recurrance)
                        <option value="{{ $key }}" @if(isset($view) && $view->recurrance == $key) selected @endif>
                            {{ __($recurrance) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 form-group">
                {{ Form::label('repeat_interval', __('Repeat Interval'), ['class'=>'form-label']) }}
                {{ Form::number('repeat_interval', null, ['class' => 'form-control', 'min' => 1]) }}
            </div>

            <div class="col-12 form-group">
                {{ Form::label('end_recurrence', __('End Recurrence'), ['class'=>'form-label']) }}
                {{ Form::date('end_recurrence', null, ['class' => 'form-control','min' => \Carbon\Carbon::today()->toDateString()]) }}
            </div>

            <div class="col-12 form-group">
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

        <div class="col-12 form-group">
            {{ Form::label('assigned_users', __('Assigned Users'),['class'=>'form-label']) }}
            {{ Form::select('assigned_users[]', $assignedUsers, null, ['class' => 'form-control select2', 'multiple' => '', 'id' => 'assigned-users-select', 'required' => 'required']) }}

        </div>


    </div>

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    @if(isset($task))
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





