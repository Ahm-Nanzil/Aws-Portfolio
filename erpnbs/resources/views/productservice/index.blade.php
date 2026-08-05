@extends('layouts.admin')
@section('page-title')
    {{__('Manage Product & Services')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Product & Services')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('productservice.file.import') }}" data-ajax-popup="true" data-title="{{__('Import product CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('productservice.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>

        <a href="#" data-size="lg" data-url="{{ route('productservice.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Product')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 {{isset($_GET['category'])?'show':''}}" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['productservice.index'], 'method' => 'GET', 'id' => 'product_service']) }}
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6">
                                <div class="btn-box">
                                    {{ Form::label('category', __('Category'),['class'=>'form-label']) }}
                                    {{ Form::select('category', $category, null, ['class' => 'form-control select','id'=>'choices-multiple', 'required' => 'required']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary"
                                   onclick="document.getElementById('product_service').submit(); return false;"
                                   data-bs-toggle="tooltip" title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('productservice.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                   title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off "></i></span>
                                </a>
                            </div>

                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Sku')}}</th>
                                    <th>{{__('Sale Price')}}</th>
                                    <th>{{__('Purchase Price')}}</th>
                                    <th>{{__('Tax')}}</th>
                                    <th>{{__('Category')}}</th>
                                    <th>{{__('Unit')}}</th>
                                    <th>{{__('Quantity')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Images')}}</th>
                                    <th>{{__('Product Type Code')}}</th>
                                    <th>{{__('Product Type Name')}}</th>
                                    <th>{{__('Group Name')}}</th>
                                    <th>{{__('Warehouse Name')}}</th>
                                    <th>{{__('Tags')}}</th>
                                    <th>{{__('Inventory')}}</th>
                                    <th>{{__('Main Category')}}</th>
                                    <th>{{__('Sub Category')}}</th>
                                    <th>{{__('Child Category')}}</th>
                                    <th>{{__('Tax 2')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Minimum Stock')}}</th>
                                    <th>{{__('Maximum Stock')}}</th>
                                    <th>{{__('Price After Tax')}}</th>
                                    <th>{{__('Mother')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Inventory Vendor')}}</th>
                                    <th>{{__('Expiration Date')}}</th>
                                    <th>{{__('UOM')}}</th>
                                    <th>{{__('Temperature Inwards Delivery')}}</th>
                                    <th>{{__('Target Penjualan')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productServices as $productService)
                                    <tr class="font-style">
                                        <td>{{ $productService->name }}</td>
                                        <td>{{ $productService->sku }}</td>
                                        <td>{{ \Auth::user()->priceFormat($productService->sale_price) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($productService->purchase_price) }}</td>
                                        <td>
                                            @if (!empty($productService->tax_id))
                                                @php
                                                    $itemTaxes = [];
                                                    $getTaxData = Utility::getTaxData();

                                                    foreach (explode(',', $productService->tax_id) as $tax) {
                                                        $itemTax['name'] = $getTaxData[$tax]['name'];
                                                        $itemTax['rate'] = $getTaxData[$tax]['rate'] . '%';

                                                        $itemTaxes[] = $itemTax;
                                                    }
                                                    $productService->itemTax = $itemTaxes;
                                                @endphp
                                                @foreach ($productService->itemTax as $tax)
                                                    <span>{{ $tax['name'] .' ('.$tax['rate'].')' }}</span><br>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ !empty($productService->category) ? $productService->category->name : '' }}</td>
                                        <td>{{ !empty($productService->unit) ? $productService->unit->name : '' }}</td>
                                        @if($productService->type == 'product')
                                            <td>{{ $productService->quantity }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ ucwords($productService->type) }}</td>
                                        <td>{{ $productService->images }}</td>
                                        <td>{{ $productService->product_type_code }}</td>
                                        <td>{{ $productService->product_type_name }}</td>
                                        <td>{{ $productService->group_name }}</td>
                                        <td>{{ $productService->warehouse_name }}</td>
                                        <td>{{ $productService->tags }}</td>
                                        <td>{{ $productService->inventory }}</td>
                                        <td>{{ $productService->main_category }}</td>
                                        <td>{{ $productService->sub_category }}</td>
                                        <td>{{ $productService->child_category }}</td>
                                        <td>{{ $productService->tax_2 }}</td>
                                        <td>{{ $productService->status ? __('Active') : __('Inactive') }}</td>
                                        <td>{{ $productService->minimum_stock }}</td>
                                        <td>{{ $productService->maximum_stock }}</td>
                                        <td>{{ \Auth::user()->priceFormat($productService->price_after_tax) }}</td>
                                        <td>{{ $productService->mother }}</td>
                                        <td>{{ $productService->date }}</td>
                                        <td>{{ $productService->inventory_vendor }}</td>
                                        <td>{{ $productService->expiration_date }}</td>
                                        <td>{{ $productService->uom }}</td>
                                        <td>{{ $productService->temperature_inwards_delivery }}</td>
                                        <td>{{ \Auth::user()->priceFormat($productService->target_penjualan) }}</td>
                                        @if(Gate::check('edit product & service') || Gate::check('delete product & service'))
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
                                                <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('productservice.detail',$productService->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Warehouse Details')}}" data-title="{{__('Warehouse Details')}}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>

                                            @can('edit product & service')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('productservice.edit',$productService->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Product')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete product & service')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['productservice.destroy', $productService->id], 'id'=>'delete-form-'.$productService->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </td>

                                        @endif
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

