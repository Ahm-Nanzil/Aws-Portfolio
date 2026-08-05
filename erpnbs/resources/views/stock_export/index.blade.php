@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Cust Delivery Notes')}}
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
    <li class="breadcrumb-item">{{__('Cust Delivery Notes')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">


        <a href="{{ route('inventory.stock-export.create') }}" data-size="lg"   title="{{__('Loss and adjustment')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus text-white"></i>

        </a>
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
                                        <th>{{ __('Inventory Delivery Voucher Code') }}</th>
                                        <th>{{ __('Customer Name') }}</th>
                                        <th>{{ __('Voucher Date') }}</th>
                                        <th>{{ __('Invoices') }}</th>
                                        <th>{{ __('To') }}</th>
                                        <th>{{ __('Address') }}</th>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Delivery Status') }}</th>
                                        <th>{{ __('Options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deliveryVouchers as $voucher)
                                        <tr class="font-style">
                                            <td>{{ $voucher->voucher_code }}</td>
                                            <td>{{ $voucher->customer_id }}</td> <!-- Assuming relationship with Customer -->
                                            <td>{{ $voucher->voucher_date->format('Y-m-d') }}</td>
                                            <td>{{ $voucher->invoice_id }}</td> <!-- Assuming relationship with Invoice -->
                                            <td>{{ $voucher->receiver }}</td>
                                            <td>{{ $voucher->address }}</td>
                                            <td>{{ $voucher->id }}</td>
                                            <td>{{ $voucher->status ? __('Active') : __('Inactive') }}</td>
                                            <td>{{ $voucher->delivery_status ? __('Delivered') : __('Pending') }}</td>
                                            <td class="Action">
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        @can('show customer')
                                                            <a href="" class="dropdown-item">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>
                                                        @endcan
                                                        @can('edit customer')
                                                            <a href="#" data-url="" data-ajax-popup="true" class="dropdown-item">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{ __('Edit') }}</span>
                                                            </a>
                                                        @endcan
                                                        @can('delete customer')
                                                            {!! Form::open(['method' => 'DELETE', 'id' => 'delete-form-']) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        @endcan
                                                    </div>
                                                </div>
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Warehouse Details')}}" data-title="{{__('Warehouse Details')}}">
                                                        <i class="ti ti-eye text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="{{ route('inventory.stock-export.edit', $voucher->id) }}" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.stock-export.destroy', $voucher->id], 'id' => 'delete-form-' . $voucher->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
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

