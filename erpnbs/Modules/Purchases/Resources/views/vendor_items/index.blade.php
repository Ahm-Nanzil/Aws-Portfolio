@extends('layouts.admin')
@section('page-title')
    {{__('Manage Vendor Products')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Vendor Products')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{-- <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('productservice.file.import') }}" data-ajax-popup="true" data-title="{{__('Import product CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('productservice.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a> --}}

        <a href="#" data-size="lg" data-url="{{ route('vendor_items.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Vendor Product')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>

    </div>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('Vendor') }}</th>
                                <th class="text-center">{{ __('Group') }}</th>
                                <th class="text-center">{{ __('Products') }}</th>
                                <th class="text-center">{{ __('Created By') }}</th>
                                <th class="text-center">{{ __('Date Created') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($vendorProducts as $vendorProduct)
                                <tr class="font-style">
                                    <td class="text-center">{{ $vendorProduct->getVendorName->name }}</td>
                                    <td class="text-center">{{ $vendorProduct->categories ? $vendorProduct->getCategoryName->name : 'N/A' }}</td>
                                    <td class="text-center">
                                        @if(!empty($vendorProduct->getProductNames()))
                                            @foreach($vendorProduct->getProductNames() as $productName)
                                                {{ $productName }} <br>
                                            @endforeach
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ !empty($vendorProduct->created_by) ? \App\Models\User::find($vendorProduct->created_by)->name : 'N/A' }}</td>
                                    <td class="text-center">{{ $vendorProduct->datecreate }}</td>
                                    <td class="text-center">
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
                                        {{-- @can('edit vendor-product') --}}
                                            <div class="action-btn bg-info ms-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('vendor_items.edit', $vendorProduct->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Vendor Product')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                        {{-- @endcan --}}
                                        {{-- @can('delete vendor-product') --}}
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['vendor_items.destroy', $vendorProduct->id], 'id'=>'delete-form-'.$vendorProduct->id]) !!}
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}">
                                                    <i class="ti ti-trash text-white"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                        {{-- @endcan --}}
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

@endsection

