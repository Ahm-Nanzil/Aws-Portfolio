@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Product Categories')}}</li>
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
            <div class="tabs-container">
                <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="main-categories-tab" data-bs-toggle="tab" href="#main-categories" role="tab" aria-controls="main-categories" aria-selected="true">Main Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sub-categories-tab" data-bs-toggle="tab" href="#sub-categories" role="tab" aria-controls="sub-categories" aria-selected="false">Sub Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="child-categories-tab" data-bs-toggle="tab" href="#child-categories" role="tab" aria-controls="child-categories" aria-selected="false">Child Categories</a>
                    </li>
                </ul>
                <div class="tab-content" id="categoryTabsContent">
                    <div class="tab-pane fade show active" id="main-categories" role="tabpanel" aria-labelledby="main-categories-tab">

                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>
                                    Main List
                                </h5>
                                <div class="float-end">
                                    <a href="#" data-url="{{ route('inventory.product-categories.main.create') }}" data-ajax-popup="true" data-title="{{__('Create New Main Category')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
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
                                                    <th>{{ __('Main Category Code') }}</th>
                                                    <th>{{ __('Main Category Name') }}</th>
                                                    <th>{{ __('Order') }}</th>
                                                    <th>{{ __('Display') }}</th>
                                                    <th>{{ __('Note') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($mainCategories as $key => $mainCategory)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $mainCategory->code }}</td>
                                                        <td>{{ $mainCategory->name }}</td>
                                                        <td>{{ $mainCategory->order }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $mainCategory->display ? 'success' : 'danger' }}">
                                                                {{ $mainCategory->display ? __('Enabled') : __('Disabled') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $mainCategory->note }}</td>
                                                        <td>
                                                            <!-- Edit Button -->
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="#" class=""  data-url="{{ route('inventory.product-categories.main.edit', $mainCategory->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Main Category')}}"><i class="ti ti-pencil text-white"></i></a>
                                                            </div>

                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.product-categories.main.destroy',$mainCategory->id], 'class' => 'modalForm','id'=>'delete-form-'.$mainCategory->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">{{ __('No Main Categorys found.') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sub-categories" role="tabpanel" aria-labelledby="sub-categories-tab">

                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>
                                    Sub List
                                </h5>
                                <div class="float-end">
                                    <a href="#" data-url="{{ route('inventory.product-categories.sub.create') }}" data-ajax-popup="true" data-title="{{__('Create New Sub Category')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
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
                                                    <th>{{ __('Sub Category Code') }}</th>
                                                    <th>{{ __('Sub Category Name') }}</th>
                                                    <th>{{ __('Main Category Name') }}</th>
                                                    <th>{{ __('Order') }}</th>
                                                    <th>{{ __('Display') }}</th>
                                                    <th>{{ __('Note') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($subCategories as $key => $subCategory)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $subCategory->code }}</td>
                                                        <td>{{ $subCategory->name }}</td>
                                                        <td>{{ $subCategory->mainCategory->name ?? 'N/A' }}</td>
                                                        <td>{{ $subCategory->order }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $subCategory->display ? 'success' : 'danger' }}">
                                                                {{ $subCategory->display ? __('Enabled') : __('Disabled') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $subCategory->note }}</td>
                                                        <td>
                                                            <!-- Edit Button -->
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="#" class=""  data-url="{{ route('inventory.product-categories.sub.edit', $subCategory->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Sub Category')}}"><i class="ti ti-pencil text-white"></i></a>
                                                            </div>

                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.product-categories.sub.destroy',$subCategory->id], 'class' => 'modalForm','id'=>'delete-form-'.$subCategory->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">{{ __('No Sub Categorys found.') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="child-categories" role="tabpanel" aria-labelledby="child-categories-tab">

                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>
                                    Child List
                                </h5>
                                <div class="float-end">
                                    <a href="#" data-url="{{ route('inventory.product-categories.child.create') }}" data-ajax-popup="true" data-title="{{__('Create New Child Category')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
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
                                                    <th>{{ __('Child Category Code') }}</th>
                                                    <th>{{ __('Child Category Name') }}</th>
                                                    <th>{{ __('Main Category Name') }}</th>
                                                    <th>{{ __('Sub Category Name') }}</th>
                                                    <th>{{ __('Order') }}</th>
                                                    <th>{{ __('Display') }}</th>
                                                    <th>{{ __('Note') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($childCategories as $key => $childCategory)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $childCategory->code }}</td>
                                                        <td>{{ $childCategory->name }}</td>
                                                        <td>{{ $childCategory->mainCategory->name ?? 'N/A' }}</td>
                                                        <td>{{ $childCategory->subCategory->name ?? 'N/A' }}</td>
                                                        <td>{{ $childCategory->order }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $childCategory->display ? 'success' : 'danger' }}">
                                                                {{ $childCategory->display ? __('Enabled') : __('Disabled') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $childCategory->note }}</td>
                                                        <td>
                                                            <!-- Edit Button -->
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="#" class=""  data-url="{{ route('inventory.product-categories.child.edit', $childCategory->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Child Category')}}"><i class="ti ti-pencil text-white"></i></a>
                                                            </div>

                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.product-categories.child.destroy',$childCategory->id], 'class' => 'modalForm','id'=>'delete-form-'.$childCategory->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                {!! Form::close() !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">{{ __('No Child Categorys found.') }}</td>
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
            </div>




        </div>
    </div>
@endsection


