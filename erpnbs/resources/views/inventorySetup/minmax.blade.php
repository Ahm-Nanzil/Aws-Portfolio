@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Minmax')}}</li>
@endsection
@push('css-page')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css">
<script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>

@endpush
@push('script-page')




<script>
    document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('hot-table');

    // Convert Laravel array to JavaScript-compatible array
    const preloadedData = @json($tableData);

    // Ensure preloaded data includes the ID column (add null if ID is missing for new rows)
    const formattedData = preloadedData.map(row => {
        if (row.length === 5) {
            // Add a null ID for rows without one
            return [null, ...row];
        }
        return row; // Keep rows with ID as is
    });

    // Handsontable instance
    const hot = new Handsontable(container, {
        data: formattedData, // Preload saved data here
        colHeaders: [
            'ID', // ID column
            'Commodity Code',
            'Commodity Name',
            'SKU Code',
            'Minimum Inventory Value',
            'Maximum Inventory Qty'
        ],
        columns: [
            { data: 0, readOnly: true }, // ID (Read-Only Column)
            { data: 1, type: 'text' },   // Commodity Code
            { data: 2, type: 'text' },   // Commodity Name
            { data: 3, type: 'text' },   // SKU Code
            { data: 4, type: 'numeric' }, // Minimum Inventory Value
            { data: 5, type: 'numeric' }  // Maximum Inventory Qty
        ],
        hiddenColumns: {
            columns: [0], // Hide the ID column from the user
            indicators: false
        },
        rowHeaders: true,
        contextMenu: true,
        manualRowMove: true,
        manualColumnMove: true,
        minSpareRows: 1, // Always keep an empty row for new entries
        licenseKey: 'non-commercial-and-evaluation' // Free for personal use
    });

    // Save data to the form
    document.getElementById('saveBtn').addEventListener('click', function () {
        const tableData = hot.getData();

        // Ensure rows with empty fields are filtered out
        const filteredData = tableData.filter(row => {
            return row.some((cell, index) => {
                // Skip the ID column (index 0) when checking for empty rows
                return index > 0 && cell !== null && cell !== '';
            });
        });

        document.getElementById('table_data').value = JSON.stringify(filteredData);
        document.getElementById('inventory-form').submit();
    });
});

</script>


@endpush

@section('action-btn')

@endsection




@section('content')





<div class="row">
    <div class="col-3">
        @include('layouts.inventory_setup')
    </div>
    <div class="col-9">
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>
                            Set Minimum and Maximum Inventory Values
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <!-- Ensure the table container takes up full width -->
                            <div id="hot-table" class="mb-3" style="100%;"></div>

                            <form action="{{ route('inventory.minmax') }}" method="POST" id="inventory-form">
                                @csrf
                                <input type="hidden" name="table_data" id="table_data">
                                <button type="button" class="btn btn-success" id="saveBtn">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection


