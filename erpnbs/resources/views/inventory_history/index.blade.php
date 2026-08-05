@extends('layouts.admin')
@section('page-title')
    {{__('Inventory History')}}
@endsection
@push('script-page')
{{-- <script>
    $(document).on('click', '[data-ajax-popup="true"]', function () {
    var modal = $('#commonModal');

    // Add a larger size class dynamically
    modal.find('.modal-dialog').addClass('modal-xl'); // Add Bootstrap's extra-large size
    modal.modal('show');
});

</script> --}}
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('History')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">


        {{-- <a data-size="md" href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('inventory.return-voucher.create') }}" data-ajax-popup="true"  data-size="xl" data-bs-toggle="tooltip" title="{{__('Update Quantity')}}">
            <i class="ti ti-plus text-white"></i>
        </a> --}}

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Form Code') }}</th>
                                        <th>{{ __('Product Code') }}</th>
                                        <th>{{ __('Warehouse Code') }}</th>
                                        <th>{{ __('Warehouse Name') }}</th>
                                        <th>{{ __('Voucher Date') }}</th>
                                        <th>{{ __('Opening Stock') }}</th>
                                        <th>{{ __('Closing Stock') }}</th>
                                        <th>{{ __('Lot Number/Quantity Sold') }}</th>
                                        <th>{{ __('Expiry Date') }}</th>
                                        <th>{{ __('Serial Number') }}</th>
                                        <th>{{ __('Note') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventoryHistories as $history)
                                        <tr class="font-style">
                                            <td>{{ $history->form_code }}</td>
                                            <td>{{ $history->product_code }}</td>
                                            <td>{{ $history->warehouse_code }}</td>
                                            <td>{{ $history->warehouse_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($history->voucher_date)->format('d-m-Y') }}</td>
                                            <td>{{ $history->opening_stock }}</td>
                                            <td>{{ $history->closing_stock }}</td>
                                            <td>{{ $history->lot_number_quantity_sold }}</td>
                                            <td>{{ \Carbon\Carbon::parse($history->expiry_date)->format('d-m-Y') ?? '-' }}</td>
                                            <td>{{ $history->serial_number }}</td>
                                            <td>{{ $history->note }}</td>
                                            <td>{{ $history->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>



                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

