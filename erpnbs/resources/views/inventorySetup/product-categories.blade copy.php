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

            <div class="card mb-3">
                <div class="card-header">
                    <h5>
                        Products Categories
                    </h5>
                </div>
                <div class="card-body">

                    <!-- Tabs for Categories -->
                    <ul class="nav nav-tabs d-flex" id="categoryTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="main-categories-tab" data-bs-toggle="tab" href="#main-categories" role="tab" aria-controls="main-categories" aria-selected="true" data-url="{{ route('inventory.product-categories.main.create') }}">
                                Main Categories
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="sub-categories-tab" data-bs-toggle="tab" href="#sub-categories" role="tab" aria-controls="sub-categories" aria-selected="false" data-url="{{ route('inventory.product-categories.main.create') }}">
                                Sub Categories
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="child-categories-tab" data-bs-toggle="tab" href="#child-categories" role="tab" aria-controls="child-categories" aria-selected="false" data-url="{{ route('inventory.product-categories.main.create') }}">
                                Child Categories
                            </a>
                        </li>
                        <!-- Create Link placed at the end of the row -->
                        <li class="nav-item ms-auto" role="presentation">
                            <a href="#" id="create-link" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
                                <i class="ti ti-plus"></i>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="categoryTabsContent">

                        <!-- Main Categories Tab -->
                        <div class="tab-pane fade show active" id="main-categories" role="tabpanel" aria-labelledby="main-categories-tab">
                            <!-- Content for Main Categories goes here -->
                        </div>

                        <!-- Sub Categories Tab -->
                        <div class="tab-pane fade" id="sub-categories" role="tabpanel" aria-labelledby="sub-categories-tab">
                            <!-- Content for Sub Categories goes here -->
                        </div>

                        <!-- Child Categories Tab -->
                        <div class="tab-pane fade" id="child-categories" role="tabpanel" aria-labelledby="child-categories-tab">
                            <!-- Content for Child Categories goes here -->
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        // When a tab is clicked, update the create link based on the active tab
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#categoryTabs .nav-link');
            const createLink = document.getElementById('create-link');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Get the URL from the clicked tab's data-url
                    const url = tab.getAttribute('data-url');
                    // Update the create button's URL
                    createLink.setAttribute('href', url);
                });
            });

            // Also update the create button link when the page is first loaded based on the active tab
            const activeTab = document.querySelector('#categoryTabs .nav-link.active');
            if (activeTab) {
                const initialUrl = activeTab.getAttribute('data-url');
                createLink.setAttribute('href', initialUrl);
            }
        });
    </script>

@endsection



<div class="card mb-3">
                <div class="card-header">
                    <h5>Products Categories</h5>
                </div>
                <div class="card-body">
                    <!-- Tabs for Categories -->
                    <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                        <li class="nav-item me-4" role="presentation">
                            <div class="d-inline-flex align-items-center">
                                <a class="nav-link active pe-1" id="main-categories-tab" data-bs-toggle="tab" href="#main-categories" role="tab" aria-controls="main-categories" aria-selected="true">
                                    Main Categories
                                </a>
                                {{-- <a href="#" data-url="{{ route('inventory.product-categories.main.create') }}" data-ajax-popup="true" data-title="{{__('Create New Main Category')}}" data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a> --}}
                            </div>
                        </li>
                        <li class="nav-item me-4" role="presentation">
                            <div class="d-inline-flex align-items-center">
                                <a class="nav-link pe-1" id="sub-categories-tab" data-bs-toggle="tab" href="#sub-categories" role="tab" aria-controls="sub-categories" aria-selected="false">
                                    Sub Categories
                                </a>
                                {{-- <a href="#" data-url="{{ route('inventory.product-types.create') }}" data-ajax-popup="true" data-title="{{__('Create New Sub Category')}}" data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a> --}}
                            </div>
                        </li>
                        <li class="nav-item" role="presentation">
                            <div class="d-inline-flex align-items-center">
                                <a class="nav-link pe-1" id="child-categories-tab" data-bs-toggle="tab" href="#child-categories" role="tab" aria-controls="child-categories" aria-selected="false">
                                    Child Categories
                                </a>
                                {{-- <a href="#" data-url="{{ route('inventory.product-types.create') }}" data-ajax-popup="true" data-title="{{__('Create New Child Category')}}" data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a> --}}
                            </div>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="categoryTabsContent">
                        <!-- Main Categories Tab -->
                        <div class="tab-pane fade show active" id="main-categories" role="tabpanel" aria-labelledby="main-categories-tab">


                        </div>

                        <!-- Sub Categories Tab -->
                        <div class="tab-pane fade" id="sub-categories" role="tabpanel" aria-labelledby="sub-categories-tab">
                            <!-- Content for Sub Categories goes here -->
                        </div>

                        <!-- Child Categories Tab -->
                        <div class="tab-pane fade" id="child-categories" role="tabpanel" aria-labelledby="child-categories-tab">
                            <!-- Content for Child Categories goes here -->
                        </div>
                    </div>
                </div>
            </div>
