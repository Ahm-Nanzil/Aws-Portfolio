@if(isset($meeting))
    {{ Form::model($meeting, array('route' => ['customer.meeting.update', $customer->id, $meeting->id], 'method' => 'PUT', 'class' => 'modalForm')) }}
@else
    {{ Form::open(array('route' => ['customer.meeting.store', $id], 'class' => 'modalForm')) }}
@endif

<div class="modal-body">
    {{-- AI Module --}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate', ['meeting']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
            </a>
        </div>
    @endif
    {{-- End AI Module --}}

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('members', __('Members'), ['class'=>'form-label']) }}
                {{ Form::select(
                    'members[]',
                    $members,
                    isset($meeting) ? (is_array($meeting->members) ? $meeting->members : json_decode($meeting->members, true)) : null,
                    ['class' => 'form-control select2', 'multiple' => '', 'id' => 'members-select', 'required' => 'required']
                ) }}
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('title', __('Meeting Title'), ['class'=>'form-label']) }}
                {{ Form::text('title', null, ['class'=>'form-control', 'placeholder' => __('Enter Meeting Title'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Meeting Date'), ['class'=>'form-label']) }}
                {{ Form::date('date', null, ['class' => 'form-control', 'required' => 'required','min' => \Carbon\Carbon::today()->toDateString()]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('time', __('Meeting Time'), ['class'=>'form-label']) }}
                {{ Form::time('time', null, ['class' => 'form-control timepicker', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('note', __('Meeting Note'), ['class'=>'form-label']) }}
                {{ Form::textarea('note', null, ['class'=>'form-control', 'placeholder' => __('Enter Meeting Note')]) }}
            </div>
        </div>

        @if(isset($settings['google_calendar_enable']) && $settings['google_calendar_enable'] == 'on')
            <div class="form-group col-md-6">
                {{ Form::label('synchronize_type', __('Synchronize in Google Calendar?'), ['class'=>'form-label']) }}
                <div class="form-switch">
                    <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow" value="google_calendar" @if(isset($meeting) && $meeting->synchronize_type == 'google_calendar') checked @endif>
                    <label class="form-check-label" for="switch-shadow"></label>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    @if(isset($meeting))
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    @else
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    @endif
</div>

{{ Form::close() }}

<script>
    $('#date').daterangepicker({
        locale: { format: 'YYYY-MM-DD' },
        singleDatePicker: true,
    });
    $("#time").timepicker({
        icons: {
            up: 'ti ti-chevron-up',
            down: 'ti ti-chevron-down'
        }
    });
</script>
