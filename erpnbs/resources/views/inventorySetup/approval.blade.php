@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Approval')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.inventory_setup')
        </div>
        <div class="col-9">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Approval</h5>
                    <div class="float-end">
                        <a href="#" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                            <i class="ti ti-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Related') }}</th>
                                    <th>{{ __('Staff') }}</th>
                                    <th>{{ __('Action') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvals as $approval)
                                    @php
                                        $staffActions = json_decode($approval->staff_actions, true);
                                    @endphp
                                    @if(!empty($staffActions))
                                        @foreach($staffActions as $index => $staffAction)
                                            <tr>
                                                @if($index === 0)
                                                    <td rowspan="{{ count($staffActions) }}">{{ $approval->subject }}</td>
                                                    <td rowspan="{{ count($staffActions) }}">{{ $approval->related }}</td>
                                                @endif
                                                <td>{{ $staffOptions[$staffAction['staff']] ?? 'N/A' }}</td>
                                                <td>{{ $actionOptions[$staffAction['action']] ?? 'N/A' }}</td>
                                                @if($index === 0)
                                                    <td rowspan="{{ count($staffActions) }}">
                                                        <a href="#" class="btn btn-sm btn-primary" onclick="openEditModal(this)"
                                                           data-id="{{ $approval->id }}"
                                                           data-subject="{{ $approval->subject }}"
                                                           data-related="{{ $approval->related }}"
                                                           data-staff_actions="{{ $approval->staff_actions }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                        <form action="{{ route('inventory.approval.destroy', $approval->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>{{ $approval->subject }}</td>
                                            <td>{{ $approval->related }}</td>
                                            <td colspan="2" class="text-center">{{ __('No staff and actions assigned.') }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary" onclick="openEditModal(this)"
                                                   data-id="{{ $approval->id }}"
                                                   data-subject="{{ $approval->subject }}"
                                                   data-related="{{ $approval->related }}"
                                                   data-staff_actions="{{ $approval->staff_actions }}">
                                                   <i class="ti ti-pencil text-white"></i>
                                                </a>
                                                <form action="{{ route('inventory.approval.destroy', $approval->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('No approvals found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">{{ __('Create New Approval') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="modalForm" action="{{ route('inventory.approval.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Subject -->
                            <div class="col-12 form-group">
                                <label for="subject" class="form-label">{{ __('Subject') }}</label>
                                <input type="text" name="subject" id="subject" class="form-control" required>
                            </div>

                            <!-- Related -->
                            <div class="col-12 form-group">
                                <label for="related" class="form-label">{{ __('Related') }}</label>
                                <select name="related" id="related" class="form-control" required>
                                    <option value="">{{ __('Select Related') }}</option>
                                    @foreach($relatedOptions as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Staff and Action -->
                            <div class="col-12 form-group">
                                <div id="staff-action-container">
                                    <div class="row mb-2 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Staff') }}</label>
                                            <select name="staff[]" class="form-control" required>
                                                <option value="">{{ __('Select Staff') }}</option>
                                                @foreach($staffOptions as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">{{ __('Action') }}</label>
                                            <select name="action[]" class="form-control" required>
                                                <option value="">{{ __('Select Action') }}</option>
                                                @foreach($actionOptions as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-success add-row">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeStaffActionContainer();

    // Reset form when modal is hidden
    $('#createModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        // Remove all additional rows except the first one
        const container = document.getElementById('staff-action-container');
        const rows = container.querySelectorAll('.row');
        for (let i = 1; i < rows.length; i++) {
            rows[i].remove();
        }
        // Reset the form action to create
        document.querySelector('.modalForm').setAttribute('action', '{{ route("inventory.approval.store") }}');
        document.querySelector('.modalForm').setAttribute('method', 'POST');
        // Remove any existing method field
        const methodField = document.querySelector('input[name="_method"]');
        if (methodField) methodField.remove();
        // Reset modal title
        document.getElementById('createModalLabel').textContent = '{{ __("Create New Approval") }}';
    });
});

function initializeStaffActionContainer() {
    const container = document.getElementById('staff-action-container');

    if (!container) return;

    container.addEventListener('click', function(e) {
        // Add new row
        if (e.target.classList.contains('add-row')) {
            const row = document.createElement('div');
            row.className = 'row mb-2 align-items-end';
            row.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label">{{ __('Staff') }}</label>
                    <select name="staff[]" class="form-control" required>
                        <option value="">{{ __('Select Staff') }}</option>
                        @foreach($staffOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">{{ __('Action') }}</label>
                    <select name="action[]" class="form-control" required>
                        <option value="">{{ __('Select Action') }}</option>
                        @foreach($actionOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-row">-</button>
                </div>
            `;
            container.appendChild(row);
        }

        // Remove row
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.row').remove();
        }
    });
}

function openCreateModal() {
    // Reset the form
    document.querySelector('.modalForm').reset();
    // Set the form action to create
    document.querySelector('.modalForm').setAttribute('action', '{{ route("inventory.approval.store") }}');
    document.querySelector('.modalForm').setAttribute('method', 'POST');
    // Remove any existing method field
    const methodField = document.querySelector('input[name="_method"]');
    if (methodField) methodField.remove();
    // Set modal title
    document.getElementById('createModalLabel').textContent = '{{ __("Create New Approval") }}';
    // Show the modal
    new bootstrap.Modal(document.getElementById('createModal')).show();
}

function openEditModal(element) {
    // Get the data from the element
    const id = element.getAttribute('data-id');
    const subject = element.getAttribute('data-subject');
    const related = element.getAttribute('data-related');
    const staffActions = JSON.parse(element.getAttribute('data-staff_actions'));

    // Set the form values
    document.querySelector('input[name="subject"]').value = subject;
    document.querySelector('select[name="related"]').value = related;

    // Set the form action to update using the correct route
    document.querySelector('.modalForm').setAttribute('action', `{{ route('inventory.approval.update', ':id') }}`.replace(':id', id));
    document.querySelector('.modalForm').setAttribute('method', 'POST');

    // Add method_field for PUT request
    let methodField = document.querySelector('input[name="_method"]');
    if (!methodField) {
        methodField = document.createElement('input');
        methodField.setAttribute('type', 'hidden');
        methodField.setAttribute('name', '_method');
        document.querySelector('.modalForm').appendChild(methodField);
    }
    methodField.setAttribute('value', 'PUT');

    // Clear existing staff-action rows except the first one
    const container = document.getElementById('staff-action-container');
    const rows = container.querySelectorAll('.row');
    for (let i = 1; i < rows.length; i++) {
        rows[i].remove();
    }

    // Set the first row values
    if (staffActions && staffActions.length > 0) {
        const firstRow = container.querySelector('.row');
        firstRow.querySelector('select[name="staff[]"]').value = staffActions[0].staff;
        firstRow.querySelector('select[name="action[]"]').value = staffActions[0].action;

        // Add additional rows for remaining staff actions
        for (let i = 1; i < staffActions.length; i++) {
            const row = document.createElement('div');
            row.className = 'row mb-2 align-items-end';
            row.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label">{{ __('Staff') }}</label>
                    <select name="staff[]" class="form-control" required>
                        <option value="">{{ __('Select Staff') }}</option>
                        @foreach($staffOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">{{ __('Action') }}</label>
                    <select name="action[]" class="form-control" required>
                        <option value="">{{ __('Select Action') }}</option>
                        @foreach($actionOptions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-row">-</button>
                </div>
            `;
            container.appendChild(row);

            // Set the values for the new row
            const newRow = container.lastElementChild;
            newRow.querySelector('select[name="staff[]"]').value = staffActions[i].staff;
            newRow.querySelector('select[name="action[]"]').value = staffActions[i].action;
        }
    }

    // Set modal title
    document.getElementById('createModalLabel').textContent = '{{ __("Edit Approval") }}';

    // Show the modal
    new bootstrap.Modal(document.getElementById('createModal')).show();
}

</script>
@endpush
</antArtif
