@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Receiving Voucher')}}
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
    <li class="breadcrumb-item">{{__('Inventory receiving voucher')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">


        <a href="{{ route('inventory.return-voucher.create') }}" data-size="lg"   title="{{__('Delivery Docket')}}" class="btn btn-sm btn-primary">
            Delivery Docket

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
                                        <th>{{__('Delivery Docket Code')}}</th>
                                        <th>{{__('Supplier Name')}}</th>
                                        <th>{{__('Buyer')}}</th>
                                        <th>{{__('Choose from a Purchase Order')}}</th>
                                        <th>{{__('Voucher Date')}}</th>
                                        <th>{{__('Total Tax Amount')}}</th>
                                        <th>{{__('Total Goods Value')}}</th>
                                        <th>{{__('Value of Inventory')}}</th>
                                        <th>{{__('Total Payment')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventoryVouchers as $voucher)
                                        <tr class="font-style">
                                            <td>{{ $voucher->delivery_docket_number }}</td>
                                            <td>{{ $voucher->supplier_name }}</td>
                                            <td>{{ $voucher->buyer }}</td>
                                            <td>{{ $voucher->purchase_order }}</td>
                                            <td>{{ $voucher->voucher_date }}</td>
                                            <td>{{ \Auth::user()->priceFormat($voucher->total_tax_amount) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($voucher->total_goods_value) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($voucher->value_of_inventory) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($voucher->total_payment) }}</td>
                                            <td>{{ $voucher->status ? __('Active') : __('Inactive') }}</td>
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
                                                        <a href="{{ route('inventory.return-voucher.edit', $voucher->id) }}">
                                                            <i class="ti ti-pencil text-white"></i>

                                                        </a>

                                                    </div>

                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.return-voucher.destroy', $voucher->id], 'id'=>'delete-form-'.$voucher->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}">
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

