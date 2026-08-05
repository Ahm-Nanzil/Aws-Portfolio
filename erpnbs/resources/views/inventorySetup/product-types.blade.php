@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Product Types')}}</li>
@endsection
@push('css-page')

@endpush
@push('script-page')
<script>
    $(document).on('click', '[data-ajax-popup="true"]', function () {
    var modal = $('#commonModal');

    // Add a larger size class dynamically
    modal.find('.modal-dialog').addClass('modal-xl'); // Add Bootstrap's extra-large size
    modal.modal('show');
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

            <div class="card mb-3">
                <div class="card-header">
                    <h5>
                        Products Types
                    </h5>
                    <div class="float-end">
                        <a href="#" data-url="{{ route('inventory.product-types.create') }}" data-ajax-popup="true" data-title="{{__('Create New Product Types')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                            <i class="ti ti-plus"></i>
                        </a>
                </div>
                </div>
                <div class="card-body">


                    <div class="mb-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Product Type Code') }}</th>
                                        <th>{{ __('Product Type Name') }}</th>
                                        <th>{{ __('Order') }}</th>
                                        <th>{{ __('Display') }}</th>
                                        <th>{{ __('Note') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productTypes as $key => $productType)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $productType->code }}</td>
                                            <td>{{ $productType->name }}</td>
                                            <td>{{ $productType->order }}</td>
                                            <td>
                                                <span class="badge bg-{{ $productType->display ? 'success' : 'danger' }}">
                                                    {{ $productType->display ? __('Enabled') : __('Disabled') }}
                                                </span>
                                            </td>
                                            <td>{{ $productType->note }}</td>
                                            <td>
                                                <!-- Edit Button -->
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="#" class=""  data-url="{{ route('inventory.product-types.edit', $productType->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Product Type')}}"><i class="ti ti-pencil text-white"></i></a>
                                                </div>

                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.product-types.destroy',$productType->id], 'class' => 'modalForm','id'=>'delete-form-'.$productType->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">{{ __('No product types found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
