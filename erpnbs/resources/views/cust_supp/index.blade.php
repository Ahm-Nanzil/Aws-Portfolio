@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Cust & Supp Returns')}}
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
    <li class="breadcrumb-item">{{__('Inventory Cust & Supp Returns')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">


        <a href="{{ route('inventory.cust-supp.create') }}" data-size="lg"   title="{{__('add')}}" class="btn btn-sm btn-primary">
            Receipt return order
        </a>
        <a href="{{ route('inventory.de-return.create') }}" data-size="lg"   title="{{__('add')}}" class="btn btn-sm btn-primary">
            Delivery return purchasing goods

        </a>
        {{-- <a data-size="md" href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('inventory.cust-supp.create') }}" data-ajax-popup="true"  data-size="xl" data-bs-toggle="tooltip" title="{{__('Update Quantity')}}">
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
                                        <th>{{ __('Order Return Number') }}</th>
                                        <th>{{ __('Customer Name') }}</th>
                                        <th>{{ __('Total Amount') }}</th>
                                        <th>{{ __('Discount Total') }}</th>
                                        <th>{{ __('Date Created') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($custSuppLists as $list)
                                        <tr class="font-style">
                                            <td>{{ $list->order_return_number }}</td>
                                            <td>{{ $list->customer_id }}</td> <!-- Assuming customer relationship exists -->
                                            <td>{{ \Auth::user()->priceFormat($list->total_payment) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($list->total_discount) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($list->created_at)->format('d-m-Y H:i') }}</td>
                                            <td>{{ $list->related_data_id }}</td> <!-- Assuming relatedType relationship exists -->
                                            <td>{{ $list->status ? __('Active') : __('Inactive') }}</td>
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
                                                    <a href="{{ route('inventory.cust-supp.edit', $list->id) }}" class="text-white" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                </div>

                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.cust-supp.destroy', $list->id], 'id'=>'delete-form-'.$list->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" onclick="document.getElementById('delete-form-{{ $list->id }}').submit();">
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

