@extends('layouts.admin')
@section('page-title')
            {{__('Manage Activity')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Activity')}}</li>
@endsection
{{-- @section('content')
<div class="row">
    <div class="col-sm-12">
        @php
            $sections = [
                'Leads' => [
                    'data' => $leadTask,
                    'routes' => 'leads',
                    'title' => __('Lead'),
                ],
                'Deals' => [
                    'data' => $dealTask,
                    'routes' => 'deals',
                    'title' => __('Deal'),
                ],
                'Customers' => [
                    'data' => $customerTask,
                    'routes' => 'customer',
                    'title' => __('Customer'),
                ],
            ];
        @endphp
        <div class="row kanban-wrapper horizontal-scroll-cards">
            @foreach($sections as $sectionName => $section)
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <div class="float-end">
                                <a href="#" data-size="lg" data-url="{{ route($section['routes'] . '.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create ' . $section['title']) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a>
                                <span class="btn btn-sm btn-primary btn-icon count">
                                    {{ count($section['data']) }}
                                </span>
                            </div>
                            <h4 class="mb-0">{{ $sectionName }}</h4>
                        </div>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="tab-{{ $sectionName }}" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="task-tab-{{ $sectionName }}" data-bs-toggle="tab" href="#task-{{ $sectionName }}" role="tab" aria-controls="task-{{ $sectionName }}" aria-selected="true">{{ __('Task') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="email-tab-{{ $sectionName }}" data-bs-toggle="tab" href="#email-{{ $sectionName }}" role="tab" aria-controls="email-{{ $sectionName }}" aria-selected="false">{{ __('Email') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="call-tab-{{ $sectionName }}" data-bs-toggle="tab" href="#call-{{ $sectionName }}" role="tab" aria-controls="call-{{ $sectionName }}" aria-selected="false">{{ __('Call') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="visit-tab-{{ $sectionName }}" data-bs-toggle="tab" href="#visit-{{ $sectionName }}" role="tab" aria-controls="visit-{{ $sectionName }}" aria-selected="false">{{ __('Visit') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="meeting-tab-{{ $sectionName }}" data-bs-toggle="tab" href="#meeting-{{ $sectionName }}" role="tab" aria-controls="meeting-{{ $sectionName }}" aria-selected="false">{{ __('Meeting') }}</a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="tabContent-{{ $sectionName }}">
                            <div class="tab-pane fade show active" id="task-{{ $sectionName }}" role="tabpanel" aria-labelledby="task-tab-{{ $sectionName }}">
                                @foreach($section['data'] as $item)
                                    <div class="card" data-id="{{ $item->id }}">
                                        <div class="card-header border-0 pb-0">
                                            <h5>
                                                <a href="">
                                                    {{ $item->name }}
                                                </a>
                                            </h5>
                                            <div class="card-header-right">
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="" class="dropdown-item">
                                                            <i class="ti ti-eye"></i>
                                                            <span>{{ __('View') }}</span>
                                                        </a>
                                                        <a href="#" data-url="{{ route($section['routes'] . '.edit', $item->id) }}" data-ajax-popup="true" class="dropdown-item" data-title="{{ __('Edit ' . $section['title']) }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>
                                                        {!! Form::open(['method' => 'DELETE', 'route' => [$section['routes'] . '.destroy', $item->id], 'id' => 'delete-form-' . $item->id]) !!}
                                                        <a href="#" class="dropdown-item bs-pass-para">
                                                            <i class="ti ti-trash"></i>
                                                            <span>{{ __('Delete') }}</span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Similar structure for other tabs like Email, Call, Visit, and Meeting -->
                            <div class="tab-pane fade" id="email-{{ $sectionName }}" role="tabpanel" aria-labelledby="email-tab-{{ $sectionName }}">
                                <!-- Display email data -->
                            </div>
                            <div class="tab-pane fade" id="call-{{ $sectionName }}" role="tabpanel" aria-labelledby="call-tab-{{ $sectionName }}">
                                <!-- Display call data -->
                            </div>
                            <div class="tab-pane fade" id="visit-{{ $sectionName }}" role="tabpanel" aria-labelledby="visit-tab-{{ $sectionName }}">
                                <!-- Display visit data -->
                            </div>
                            <div class="tab-pane fade" id="meeting-{{ $sectionName }}" role="tabpanel" aria-labelledby="meeting-tab-{{ $sectionName }}">
                                <!-- Display meeting data -->
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection --}}


@section('content')
<div class="row">
    <div class="col-sm-12">
        @php
            $sectionsTask = [
                'Tasks of Leads' => [
                    'data' => $leadTask,
                    'routes' => 'leads',
                    'title' => __('Tasks of Leads'),
                ],
                'Tasks of Deals' => [
                    'data' => $dealTask,
                    'routes' => 'deals',
                    'title' => __('Task of Deals'),
                ],
                'Tasks of Customers' => [
                    'data' => $customerTask,
                    'routes' => 'customer',
                    'title' => __('Tasks of Customers'),
                ],
            ];
            $sectionsEmail = [
                'Emails of Leads' => [
                    'data' => $leadEmail,
                    'routes' => 'leads',
                    'title' => __('Emails of Leads'),
                ],
                'Emails of Deals' => [
                    'data' => $dealEmail,
                    'routes' => 'deals',
                    'title' => __('Task of Deals'),
                ],
                'Emails of Customers' => [
                    'data' => $customerEmail,
                    'routes' => 'customer',
                    'title' => __('Emails of Customers'),
                ],
            ];

            $sectionsCall = [
                'Calls of Leads' => [
                    'data' => $leadCall,
                    'routes' => 'leads',
                    'title' => __('Calls of Leads'),
                ],
                'Calls of Deals' => [
                    'data' => $dealCall,
                    'routes' => 'deals',
                    'title' => __('Call of Deals'),
                ],
                'Calls of Customers' => [
                    'data' => $customerCall,
                    'routes' => 'customer',
                    'title' => __('Calls of Customers'),
                ],
            ];
            $sectionsVisit = [
                'Visits of Leads' => [
                    'data' => $leadVisit,
                    'routes' => 'leads',
                    'title' => __('Visits of Leads'),
                ],
                'Visits of Deals' => [
                    'data' => $dealVisit,
                    'routes' => 'deals',
                    'title' => __('Visit of Deals'),
                ],
                'Visits of Customers' => [
                    'data' => $customerVisit,
                    'routes' => 'customer',
                    'title' => __('Visits of Customers'),
                ],
            ];
            $sectionsMeeting = [
                'Meetings of Leads' => [
                    'data' => $leadMeeting,
                    'routes' => 'leads',
                    'title' => __('Meetings of Leads'),
                ],
                'Meetings of Deals' => [
                    'data' => $dealMeeting,
                    'routes' => 'deals',
                    'title' => __('Meeting of Deals'),
                ],
                'Meetings of Customers' => [
                    'data' => $customerMeeting,
                    'routes' => 'customer',
                    'title' => __('Meetings of Customers'),
                ],
            ];

            $sectionLeadDealCustomer = [
                'Leads' => [
                    'data' => $leads,
                    'routes' => 'leads',
                    'title' => __('Lead'),
                ],
                'Deals' => [
                    'data' => $deals,
                    'routes' => 'deals',
                    'title' => __('Deal'),
                ],
                'Customers' => [
                    'data' => $customers,
                    'routes' => 'customer',
                    'title' => __('Customer'),
                ],
            ];

        @endphp

        <!-- Tabs for Task, Email, Call, Visit, Meeting -->
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="task-tab" data-bs-toggle="tab" href="#task" role="tab" aria-controls="task" aria-selected="true">{{ __('Task') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="email-tab" data-bs-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">{{ __('Email') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="call-tab" data-bs-toggle="tab" href="#call" role="tab" aria-controls="call" aria-selected="false">{{ __('Call') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="visit-tab" data-bs-toggle="tab" href="#visit" role="tab" aria-controls="visit" aria-selected="false">{{ __('Visit') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="meeting-tab" data-bs-toggle="tab" href="#meeting" role="tab" aria-controls="meeting" aria-selected="false">{{ __('Meeting') }}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="leaddealcustomer-tab" data-bs-toggle="tab" href="#leaddealcustomer" role="tab" aria-controls="leaddealcustomer" aria-selected="false">{{ __('Recent Lead/Deal/Customers') }}</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="tabContent">
            <!-- Task Tab: Show Leads, Deals, and Customers -->
            <div class="tab-pane fade show active" id="task" role="tabpanel" aria-labelledby="task-tab">
                <div class="row kanban-wrapper horizontal-scroll-cards">
                    @foreach($sectionsTask as $sectionName => $section)
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        {{-- <a href="#" data-size="lg" data-url="{{ route($section['routes'] . '.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create ' . $section['title']) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a> --}}
                                        <span class="btn btn-sm btn-primary btn-icon count">
                                            {{ count($section['data']) }}
                                        </span>
                                    </div>
                                    <h4 class="mb-0">{{ $sectionName }}</h4>
                                </div>
                                <div class="card-body kanban-box">
                                    @foreach($section['data'] as $item)
                                        <div class="card" data-id="{{ $item->id }}">
                                            <div class="card-header border-0 pb-0">
                                                <h5>
                                                    <a href="">
                                                        {{ $item->name }}
                                                    </a>
                                                </h5>
                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="" class="dropdown-item">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>
                                                            <a href="#"
                                                                data-url="{{
                                                                    route(
                                                                        $section['routes'] . '.tasks.edit',
                                                                        [
                                                                            'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                            'tid' => $item->id
                                                                        ]
                                                                    )
                                                                }}"
                                                                data-ajax-popup="true"
                                                                class="dropdown-item"
                                                                data-title="{{ __('Edit ' . $section['title']) }}">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{ __('Edit') }}</span>
                                                                </a>

                                                            {!! Form::open(['method' => 'DELETE',

                                                            'route' => [
                                                                $section['routes'] . '.tasks.destroy',
                                                                    [
                                                                        'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                        'tid' => $item->id
                                                                    ]

                                                            ],


                                                            'id' => 'delete-form-' . $item->id]) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Email Tab: Show Email Data -->
            <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                <!-- Display Email Data Here -->

                <div class="row kanban-wrapper horizontal-scroll-cards">
                    @foreach($sectionsEmail as $sectionName => $section)
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        {{-- <a href="#" data-size="lg" data-url="{{ route($section['routes'] . '.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create ' . $section['title']) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a> --}}
                                        <span class="btn btn-sm btn-primary btn-icon count">
                                            {{ count($section['data']) }}
                                        </span>
                                    </div>
                                    <h4 class="mb-0">{{ $sectionName }}</h4>
                                </div>
                                <div class="card-body kanban-box">
                                    @foreach($section['data'] as $item)
                                        <div class="card" data-id="{{ $item->id }}">
                                            <div class="card-header border-0 pb-0">
                                                <h5>
                                                    <a href="">
                                                        {{ $item->subject }}
                                                    </a>
                                                </h5>
                                                <div class="card-header-right">
                                                    {{-- <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="" class="dropdown-item">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>
                                                            <a href="#" data-url="{{ route($section['routes'] . '.edit', $item->id) }}" data-ajax-popup="true" class="dropdown-item" data-title="{{ __('Edit ' . $section['title']) }}">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{ __('Edit') }}</span>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' => [$section['routes'] . '.destroy', $item->id], 'id' => 'delete-form-' . $item->id]) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="card-body">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Call Tab: Show Call Data -->
            <div class="tab-pane fade" id="call" role="tabpanel" aria-labelledby="call-tab">
                <!-- Display Call Data Here -->

                <div class="row kanban-wrapper horizontal-scroll-cards">
                    @foreach($sectionsCall as $sectionName => $section)
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        {{-- <a href="#" data-size="lg" data-url="{{ route($section['routes'] . '.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create ' . $section['title']) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a> --}}
                                        <span class="btn btn-sm btn-primary btn-icon count">
                                            {{ count($section['data']) }}
                                        </span>
                                    </div>
                                    <h4 class="mb-0">{{ $sectionName }}</h4>
                                </div>
                                <div class="card-body kanban-box">
                                    @foreach($section['data'] as $item)
                                        <div class="card" data-id="{{ $item->id }}">
                                            <div class="card-header border-0 pb-0">
                                                <h5>
                                                    <a href="">
                                                        {{ $item->subject }}
                                                    </a>
                                                </h5>
                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="" class="dropdown-item">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>
                                                            <a href="#"
                                                            data-url="{{
                                                                route(
                                                                    $section['routes'] . '.calls.edit',
                                                                    [
                                                                        'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                        'cid' => $item->id
                                                                    ]
                                                                )
                                                            }}"
                                                            data-ajax-popup="true"
                                                            class="dropdown-item"
                                                            data-title="{{ __('Edit ' . $section['title']) }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                            </a>

                                                            {!! Form::open(['method' => 'DELETE',
                                                            'route' => [
                                                                $section['routes'] . '.calls.destroy',
                                                                    [
                                                                        'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                        'cid' => $item->id
                                                                    ]
                                                            ],

                                                            'id' => 'delete-form-' . $item->id]) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Visit Tab: Show Visit Data -->
            <div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab">
                <!-- Display Visit Data Here -->

                <div class="row kanban-wrapper horizontal-scroll-cards">
                    @foreach($sectionsVisit as $sectionName => $section)
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        {{-- <a href="#" data-size="lg" data-url="{{ route($section['routes'] . '.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create ' . $section['title']) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a> --}}
                                        <span class="btn btn-sm btn-primary btn-icon count">
                                            {{ count($section['data']) }}
                                        </span>
                                    </div>
                                    <h4 class="mb-0">{{ $sectionName }}</h4>
                                </div>
                                <div class="card-body kanban-box">
                                    @foreach($section['data'] as $item)
                                        <div class="card" data-id="{{ $item->id }}">
                                            <div class="card-header border-0 pb-0">
                                                <h5>
                                                    <a href="">
                                                        {{ $item->title }}
                                                    </a>
                                                </h5>
                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="" class="dropdown-item">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>
                                                            <a href="#"
                                                            data-url="{{
                                                                route(
                                                                    $section['routes'] . '.visit.edit',
                                                                    [
                                                                        'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                        'vid' => $item->id
                                                                    ]
                                                                )
                                                            }}"
                                                            data-ajax-popup="true"
                                                            class="dropdown-item"
                                                            data-title="{{ __('Edit ' . $section['title']) }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                            </a>

                                                            {!! Form::open(['method' => 'DELETE',
                                                            'route' => [
                                                                $section['routes'] . '.visit.destroy',
                                                                    [
                                                                        'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                        'vid' => $item->id
                                                                    ]
                                                            ],

                                                            'id' => 'delete-form-' . $item->id]) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Meeting Tab: Show Meeting Data -->
            <div class="tab-pane fade" id="meeting" role="tabpanel" aria-labelledby="meeting-tab">
                <!-- Display Meeting Data Here -->

                <div class="row kanban-wrapper horizontal-scroll-cards">
                    @foreach($sectionsMeeting as $sectionName => $section)
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        {{-- <a href="#" data-size="lg" data-url="{{ route($section['routes'] . '.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create ' . $section['title']) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a> --}}
                                        <span class="btn btn-sm btn-primary btn-icon count">
                                            {{ count($section['data']) }}
                                        </span>
                                    </div>
                                    <h4 class="mb-0">{{ $sectionName }}</h4>
                                </div>
                                <div class="card-body kanban-box">
                                    @foreach($section['data'] as $item)
                                        <div class="card" data-id="{{ $item->id }}">
                                            <div class="card-header border-0 pb-0">
                                                <h5>
                                                    <a href="">
                                                        {{ $item->title }}
                                                    </a>
                                                </h5>
                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="" class="dropdown-item">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>
                                                            <a href="#"
                                                            data-url="{{
                                                                route(
                                                                    $section['routes'] . '.meeting.edit',
                                                                    [
                                                                        'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                        'mid' => $item->id
                                                                    ]
                                                                )
                                                            }}"
                                                            data-ajax-popup="true"
                                                            class="dropdown-item"
                                                            data-title="{{ __('Edit ' . $section['title']) }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                            </a>

                                                            {!! Form::open(['method' => 'DELETE',
                                                            'route' => [
                                                                $section['routes'] . '.meeting.destroy',
                                                                    [
                                                                        'id' => $item->{$section['routes'] === 'leads' ? 'lead_id' : ($section['routes'] === 'deals' ? 'deal_id' : 'customer_id')},
                                                                        'mid' => $item->id
                                                                    ]
                                                            ],

                                                            'id' => 'delete-form-' . $item->id]) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="tab-pane fade" id="leaddealcustomer" role="tabpanel" aria-labelledby="leaddealcustomer-tab">

                <div class="row kanban-wrapper horizontal-scroll-cards">
                    @foreach($sectionLeadDealCustomer as $sectionName => $section)
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        <a href="#" data-size="lg" data-url="{{ route($section['routes'] . '.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create ' . $section['title']) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                        <span class="btn btn-sm btn-primary btn-icon count">
                                            {{ count($section['data']) }}
                                        </span>
                                    </div>
                                    <h4 class="mb-0">{{ $sectionName }}</h4>
                                </div>
                                <div class="card-body kanban-box">
                                    @foreach($section['data'] as $item)
                                        <div class="card" data-id="{{ $item->id }}">
                                            <div class="card-header border-0 pb-0">
                                                <h5>
                                                    <a href="">
                                                        {{ $item->name }}
                                                    </a>
                                                </h5>
                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="{{ route($section['routes'] . '.show', $section['routes'] === 'customer' ? \Crypt::encrypt($item->id) : $item->id) }}" class="dropdown-item">
                                                                <i class="ti ti-eye"></i>
                                                                <span>{{ __('View') }}</span>
                                                            </a>

                                                            <a href="#" data-url="{{ route($section['routes'] . '.edit', $item->id) }}" data-ajax-popup="true" class="dropdown-item" data-title="{{ __('Edit ' . $section['title']) }}">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{ __('Edit') }}</span>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' => [$section['routes'] . '.destroy', $item->id], 'id' => 'delete-form-' . $item->id]) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para">
                                                                <i class="ti ti-trash"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p><strong>{{ __('Email:') }}</strong> {{ $item->emails()->count() }}</p>
                                                <p><strong>{{ __('Task:') }}</strong> {{ $item->tasks()->count() }}</p>
                                                <p><strong>{{ __('Call:') }}</strong> {{ $item->calls()->count() }}</p>
                                                <p><strong>{{ __('Meeting:') }}</strong> {{ $item->meetings()->count() }}</p>
                                                <p><strong>{{ __('Visit:') }}</strong> {{ $item->visits()->count() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

