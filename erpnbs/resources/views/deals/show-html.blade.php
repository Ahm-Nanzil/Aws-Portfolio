<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="lead-sidenav">
                        @if(Auth::user()->type != 'client')
                            <a href="#general" class="list-group-item list-group-item-action border-0">{{__('General')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif

                        @if(Auth::user()->type != 'client')
                            <a href="#basicInfoHeader" class="list-group-item list-group-item-action border-0">{{__('Basic Info')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif

                        @if(Auth::user()->type != 'client')
                            <a href="#sources" class="list-group-item list-group-item-action border-0">{{__('Sources')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if(Auth::user()->type != 'client')
                            <a href="#primaryContactInfo" class="list-group-item list-group-item-action border-0">{{__('Primary Contact')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if(Auth::user()->type != 'client')
                            <a href="#additionalcontacts" class="list-group-item list-group-item-action border-0">{{__('Additional Contact')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if(Auth::user()->type != 'client')
                            <a href="#users" class="list-group-item list-group-item-action border-0">{{__('Users')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif

                        @if(Auth::user()->type != 'client')
                            <a href="#emails" class="list-group-item list-group-item-action border-0">{{__('Emails')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if(Auth::user()->type != 'client')
                            <a href="#openActivities" class="list-group-item list-group-item-action border-0">{{__('Open Activities')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif

                        @if(Auth::user()->type != 'client')
                            <a href="#products" class="list-group-item list-group-item-action border-0">{{__('Products')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif

                        @if(Auth::user()->type != 'client')
                            <a href="#discussion_note" class="list-group-item list-group-item-action border-0">{{__('Discussion').' | '.__('Notes')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if(Auth::user()->type != 'client')
                            <a href="#files" class="list-group-item list-group-item-action border-0">{{__('Files')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif

                        @if(Auth::user()->type != 'client')
                            <a href="#timeline" class="list-group-item list-group-item-action border-0">{{__('Timeline')}}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <?php
                $tasks = $deal->tasks;
                $products = $deal->products();
                $sources = $deal->sources();
                $calls = $deal->calls;
                $emails = $deal->emails;
                ?>
                <div id="general" class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="d-flex align-items-start">
                                    <div class="theme-avtar bg-primary">
                                        <i class="ti ti-test-pipe"></i>
                                    </div>
                                    <div class="ms-2">
                                        <p class="text-muted text-sm mb-0">{{__('Pipeline')}}</p>
                                        <h5 class="mb-0 text-success">{{$deal->pipeline->name}}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 my-3 my-sm-0">
                                <div class="d-flex align-items-start">
                                    <div class="theme-avtar bg-primary">
                                        <i class="ti ti-server"></i>
                                    </div>
                                    <div class="ms-2">
                                        <p class="text-muted text-sm mb-0">{{__('Stage')}}</p>
                                        <h5 class="mb-0 text-primary">{{$deal->stage->name}}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 my-sm-0 my-3 col-sm-6">
                                <div class="d-flex align-items-start">
                                    <div class="theme-avtar bg-warning">
                                        <i class="ti ti-calendar"></i>
                                    </div>
                                    <div class="ms-2">
                                        <p class="text-muted text-sm mb-0">{{__('Created')}}</p>
                                        <h5 class="mb-0 text-warning">{{\Auth::user()->dateFormat($deal->created_at)}}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="d-flex align-items-start">
                                    <div class="theme-avtar bg-info">
                                        <i class="ti ti-report-money"></i>
                                    </div>
                                    <div class="ms-2">
                                        <p class="text-muted text-sm mb-0">{{__('Price')}}</p>
                                        <h5 class="mb-0 text-info">{{\Auth::user()->priceFormat($deal->price)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto mb-3 mb-sm-0">
                                        <small class="text-muted">{{__('Task')}}</small>
                                        <h3 class="m-0">{{count($tasks)}}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <div class="theme-avtar bg-danger">
                                            <i class="ti ti-subtask"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto mb-3 mb-sm-0">
                                        <small class="text-muted">{{__('Product')}}</small>
                                        <h3 class="m-0">{{count($products)}}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-shopping-cart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto mb-3 mb-sm-0">
                                        <small class="text-muted">{{__('Source')}}</small>
                                        <h3 class="m-0">{{count($sources)}}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <div class="theme-avtar bg-primary">
                                            <i class="ti ti-social"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto mb-3 mb-sm-0">
                                        <small class="text-muted">{{__('Files')}}</small>
                                        <h3 class="m-0">{{count($deal->files)}}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <div class="theme-avtar bg-warning">
                                            <i class="ti ti-file"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card">
                    <div class="card-header" id="basicInfoHeader">
                        <h5 class="mb-0 d-flex justify-content-between align-items-center">
                            {{ __('Deal Basic Info') }}
                            <div class="float-end">
                            <a data-size="md" data-url="{{ route('deals.basicinfo.edit',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit Basic Info')}}" data-title="{{__('Edit Basic Info')}}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit text-white"></i>
                            </a>

                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#basicInfoContent" aria-expanded="false" aria-controls="basicInfoContent" onclick="toggleIcon(this)">
                                <i class="fas fa-chevron-right"></i> <!-- Starts with the right arrow -->
                            </button>
                            <script>
                                function toggleIcon(button) {
                                    const icon = button.querySelector('i');
                                    icon.classList.toggle('fa-chevron-down');
                                    icon.classList.toggle('fa-chevron-right');
                                }
                            </script>
                            </div>

                        </h5>
                    </div>
                    <div id="basicInfoContent" class="collapse" aria-labelledby="basicInfoHeader">
                        <div class="card-body">
                            <div class="row">
                                <!-- Lead Owner -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Lead Owner') }}</p>
                                    <h5>{{ $deal->lead_owner ?? '' }}</h5>
                                </div>

                                <!-- Company Website -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company Website') }}</p>
                                    <h5><a href="{{ $deal->company_website }}" target="_blank">{{ $deal->company_website ?? '' }}</a></h5>
                                </div>

                                <!-- Company/Entity Name -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company / Entity Name') }}</p>
                                    <h5>{{ $deal->company_entity_name ?? '' }}</h5>
                                </div>

                                <!-- Company/Entity Logo -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company / Entity Logo') }}</p>
                                    <img src="{{ asset('storage/' . $deal->company_entity_logo) }}" alt="Company Logo" style="max-width: 100px;">
                                </div>

                                <!-- Company Phone LL1 -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company Phone LL1') }}</p>
                                    <h5>{{ $deal->company_phone_ll1 ?? '' }}</h5>
                                </div>

                                <!-- Company Phone LL2 -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company Phone LL2') }}</p>
                                    <h5>{{ $deal->company_phone_ll2 ?? '' }}</h5>
                                </div>

                                <!-- Company Email -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company Email') }}</p>
                                    <h5>{{ $deal->company_email ?? '' }}</h5>
                                </div>

                                <!-- Address1 -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Address1') }}</p>
                                    <h5>{{ $deal->address1 ?? '' }}</h5>
                                </div>

                                <!-- Address2 -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Address2') }}</p>
                                    <h5>{{ $deal->address2 ?? '' }}</h5>
                                </div>

                                <!-- City -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('City') }}</p>
                                    <h5>{{ $deal->city ?? '' }}</h5>
                                </div>

                                <!-- Region/State -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Region/State') }}</p>
                                    <h5>{{ $deal->region ?? '' }}</h5>
                                </div>

                                <!-- Country -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Country') }}</p>
                                    <h5>{{ $deal->country ?? '' }}</h5>
                                </div>

                                <!-- Zip Code -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Zip Code') }}</p>
                                    <h5>{{ $deal->zip_code ?? '' }}</h5>
                                </div>

                                <!-- Company LinkedIn -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company LinkedIn') }}</p>
                                    <h5><a href="{{ $deal->company_linkedin }}" target="_blank">{{ $deal->company_linkedin ?? '' }}</a></h5>
                                </div>

                                <!-- Company Location -->
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted mb-0">{{ __('Company Location') }}</p>
                                    <h5><a href="{{ $deal->company_location }}" target="_blank">{{ $deal->company_location ?? '' }}</a></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="sources">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>{{__('Sources')}}</h5>
                                        <div class="float-end">
                                            <a data-size="md" data-url="{{ route('deals.sources.edit',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Source')}}" data-title="{{__('Add Source')}}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-plus text-white"></i>
                                            </a>
                                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#leadSources" aria-expanded="false" aria-controls="leadSources" onclick="toggleleadSourcesIcon(this)">
                                                <i class="fas fa-chevron-right"></i> <!-- Starts with the right arrow -->
                                            </button>
                                            <script>
                                                function toggleleadSourcesIcon(button) {
                                                    const icon = button.querySelector('i');
                                                    icon.classList.toggle('fa-chevron-down');
                                                    icon.classList.toggle('fa-chevron-right');
                                                }
                                            </script>
                                        </div>
                                    </div>

                                </div>
                                <div id="leadSources" class="collapse" aria-labelledby="">

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($sources as $source)
                                                    <tr>
                                                        <td>{{$source->name}} </td>
                                                        @can('edit deal')
                                                            <td>
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['deals.sources.destroy', $deal->id,$source->id], 'class' => 'modalForm','id'=>'delete-form-'.$deal->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </td>
                                                        @endcan
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
                </div>

                <div id="primaryContactInfo" class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title d-inline">{{ __('Primary Contact Info') }}</h5>
                        <div class="float-end">
                        <a data-size="md" data-url="{{ route('deals.primarycontact.edit',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit Primary Contact Info')}}" data-title="{{__('Edit Primary Contact Info')}}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        <button class="btn btn-link float-end" type="button" data-bs-toggle="collapse" data-bs-target="#primaryContactContent" aria-expanded="false" aria-controls="primaryContactContent" onclick="togglePrimaryContactIcon(this)">
                            <i class="fas fa-chevron-right"></i> <!-- Start with right arrow -->
                        </button>
                        </div>
                        <script>
                            function togglePrimaryContactIcon(button) {
                                const icon = button.querySelector('i');
                                icon.classList.toggle('fa-chevron-down');
                                icon.classList.toggle('fa-chevron-right');
                            }
                        </script>

                    </div>

                    <div id="primaryContactContent" class="collapse">
                        <div class="card-body">
                            <div class="row">
                                {{-- <div class="col-md-6 mt-3">
                                    <p class="text-muted text-sm mb-0">{{__('Salutation')}}</p>
                                    <h5 class="mb-0">{{ $deal->salutation ?? '' }}</h5>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <p class="text-muted text-sm mb-0">{{__('First Name')}}</p>
                                    <h5 class="mb-0">{{ $deal->first_name ?? '' }}</h5>
                                </div>
                                <!-- Add remaining fields in similar two-column format here -->

                                <div class="col-md-6 mt-3">
                                    <p class="text-muted text-sm mb-0">{{__('LinkedIn Profile')}}</p>
                                    <a href="{{ $deal->linkedin_profile }}" target="_blank" class="text-primary">{{ $deal->linkedin_profile ?? '' }}</a>
                                </div> --}}

                                <div class="row">
                                    <!-- Salutation -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Salutation') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->salutation) ? $deal->salutation : '' }}</h5>
                                    </div>

                                    <!-- First Name -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('First Name') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->first_name) ? $deal->first_name : '' }}</h5>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Last Name') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->last_name) ? $deal->last_name : '' }}</h5>
                                    </div>

                                    <!-- Mobile Primary -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Mobile Primary') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->mobile_primary) ? $deal->mobile_primary : '' }}</h5>
                                    </div>

                                    <!-- Mobile Secondary -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Mobile Secondary') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->mobile_secondary) ? $deal->mobile_secondary : '' }}</h5>
                                    </div>

                                    <!-- Email Work -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Email Work') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->email_work) ? $deal->email_work : '' }}</h5>
                                    </div>

                                    <!-- Email Personal -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Email Personal') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->email_personal) ? $deal->email_personal : '' }}</h5>
                                    </div>

                                    <!-- Phone LL -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Phone LL') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->phone_ll) ? $deal->phone_ll : '' }}</h5>
                                    </div>

                                    <!-- Company Phone LL -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Company Phone LL') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->company_phone_ll) ? $deal->company_phone_ll : '' }}</h5>
                                    </div>

                                    <!-- Extension # -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('Extension #') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->extension) ? $deal->extension : '' }}</h5>
                                    </div>

                                    <!-- LinkedIn Profile -->
                                    <div class="col-md-6 col-sm-6 mt-3">
                                        <p class="text-muted text-sm mb-0">{{ __('LinkedIn Profile') }}</p>
                                        <h5 class="mb-0">{{ !empty($deal->linkedin_profile) ? $deal->linkedin_profile : '' }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="additionalcontacts" class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5>{{ __('Additional Contacts') }}</h5>
                            {{-- @can('create lead email') --}}
                                <div class="float-end">
                                    <a data-size="md" data-bs-target="#contactModal" data-url="{{ route('deals.additional-contact.create',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create Additional Contact')}}" data-title="{{__('Edit Additional Contact ')}}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-plus text-white"></i>
                                    </a>
                                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#additional-contact" aria-expanded="false" aria-controls="basicInfoContent" onclick="additionalcontactIcon(this)">
                                        <i class="fas fa-chevron-right"></i> <!-- Starts with the right arrow -->
                                    </button>
                                </div>

                                <script>
                                    function additionalcontactIcon(button) {
                                        const icon = button.querySelector('i');
                                        icon.classList.toggle('fa-chevron-down');
                                        icon.classList.toggle('fa-chevron-right');
                                    }
                                </script>
                            {{-- @endcan --}}
                        </div>

                    </div>
                    <div id="additional-contact" class="collapse" aria-labelledby="">

                        <div class="card-body">
                            <div class="row" id="additional-contacts-list">
                                @foreach(json_decode($deal->additional_contacts, true) ?? [] as $index => $contact)
                                    <div class="col-md-12 mt-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h5 class="mb-0 text-primary">{{ $contact['name'] ?? '' }}
                                                    <button class="btn btn-link p-0 toggle-details" data-target="#contact-details-{{ $index }}">
                                                        <i class="ti ti-plus"></i>
                                                    </button>
                                                </h5>
                                            </div>
                                            <div class="float-end">
                                                @can('edit deal')
                                                    <div class="col-md-12 mt-2">
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['deals.additional_contacts.destroy', $deal->id, $index], 'class' => 'modalForm', 'id' => 'delete-form-' . $index]) !!}
                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                @endcan
                                            </div>
                                        </div>
                                        <div id="contact-details-{{ $index }}" class="contact-details mt-2" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-3 mt-2">
                                                    <div class="d-flex align-items-start">
                                                        <div class="theme-avtar bg-warning">
                                                            <i class="ti ti-mail"></i>
                                                        </div>
                                                        <div class="ms-2">
                                                            <p class="text-muted text-sm mb-0">{{ __('Email') }}</p>
                                                            <h5 class="mb-0 text-warning">{{ $contact['email'] ?? '' }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <div class="d-flex align-items-start">
                                                        <div class="theme-avtar bg-info">
                                                            <i class="ti ti-briefcase"></i>
                                                        </div>
                                                        <div class="ms-2">
                                                            <p class="text-muted text-sm mb-0">{{ __('Title') }}</p>
                                                            <h5 class="mb-0 text-info">{{ $contact['title'] ?? '' }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <div class="d-flex align-items-start">
                                                        <div class="theme-avtar bg-primary">
                                                            <i class="ti ti-sitemap"></i>
                                                        </div>
                                                        <div class="ms-2">
                                                            <p class="text-muted text-sm mb-0">{{ __('Department') }}</p>
                                                            <h5 class="mb-0 text-primary">{{ $contact['department'] ?? '' }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <div class="d-flex align-items-start">
                                                        <div class="theme-avtar bg-warning">
                                                            <i class="ti ti-phone"></i>
                                                        </div>
                                                        <div class="ms-2">
                                                            <p class="text-muted text-sm mb-0">{{ __('Phone Mobile') }}</p>
                                                            <h5 class="mb-0 text-warning">{{ $contact['phone_mobile'] ?? '' }}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <script>
                    document.querySelectorAll('.toggle-details').forEach(button => {
                        button.addEventListener('click', function () {
                            const target = document.querySelector(this.dataset.target);
                            if (target.style.display === 'none') {
                                target.style.display = 'block';
                                this.innerHTML = '<i class="ti ti-minus"></i>';
                            } else {
                                target.style.display = 'none';
                                this.innerHTML = '<i class="ti ti-plus"></i>';
                            }
                        });
                    });
                </script>



                <div id="users">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>{{__('Users')}}</h5>
                                        <div class="float-end">
                                            <a  data-size="md" data-url="{{ route('deals.users.edit',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add User')}}" data-title="{{__('Add User')}}" class="btn btn-sm btn-primary ">
                                                <i class="ti ti-plus text-white"></i>
                                            </a>
                                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#leadUser" aria-expanded="false" aria-controls="leadUser" onclick="toggleleadUserIcon(this)">
                                                <i class="fas fa-chevron-right"></i> <!-- Starts with the right arrow -->
                                            </button>
                                            <script>
                                                function toggleleadUserIcon(button) {
                                                    const icon = button.querySelector('i');
                                                    icon.classList.toggle('fa-chevron-down');
                                                    icon.classList.toggle('fa-chevron-right');
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                                <div id="leadUser" class="collapse" aria-labelledby="">

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($deal->users as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div>
                                                                    <img @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="wid-30 rounded-circle me-3" alt="avatar image">
                                                                </div>
                                                                <p class="mb-0">{{$user->name}}</p>
                                                            </div>
                                                        </td>
                                                        @can('edit deal')
                                                            <td>
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['deals.users.destroy', $deal->id,$user->id],'class' => 'modalForm','id'=>'delete-form-'.$deal->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </td>
                                                        @endcan
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
                </div>

                <div id="emails">
                    <div class="row">

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>{{__('Emails')}}</h5>
                                        @can('create lead email')
                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('deals.emails.create',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create Email')}}" data-title="{{__('Create Email')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#leadEmail" aria-expanded="false" aria-controls="leadEmail" onclick="toggleLeadEmailIcon(this)">
                                                    <i class="fas fa-chevron-right"></i> <!-- Starts with the right arrow -->
                                                </button>
                                                <script>
                                                    function toggleLeadEmailIcon(button) {
                                                        const icon = button.querySelector('i');
                                                        icon.classList.toggle('fa-chevron-down');
                                                        icon.classList.toggle('fa-chevron-right');
                                                    }
                                                </script>
                                            </div>
                                        @endcan
                                    </div>

                                </div>
                                <div id="leadEmail" class="collapse" aria-labelledby="">

                                    <div class="card-body">
                                        <div class="list-group list-group-flush mt-2">
                                            @if(!$emails->isEmpty())
                                                @foreach($emails as $email)
                                                    <li class="list-group-item px-0">
                                                        <div class="d-block d-sm-flex align-items-start">
                                                            <img src="{{asset('/storage/uploads/avatar/avatar.png')}}"
                                                                class="img-fluid wid-40 me-3 mb-2 mb-sm-0" alt="image">
                                                            <div class="w-100">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="mb-3 mb-sm-0">
                                                                        <h6 class="mb-0">{{$email->subject}}</h6>
                                                                        <span class="text-muted text-sm">{{$email->to}}</span>
                                                                    </div>
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        {{$email->created_at->diffForHumans()}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="text-center">
                                                    {{__(' No Emails Available.!')}}
                                                </li>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="openActivities" class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5>{{ __('Open Activities') }}</h5>

                            @php
                                $tasks = $deal->tasks;

                                // dd($tasks);
                            @endphp
                            <div class="float-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="activityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-plus text-white"></i> {{ __('Add Activity') }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="activityDropdown">
                                        <li>

                                            <a class="dropdown-item" data-size="md" data-url="{{ route('deals.calls.create', $deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Call') }}" data-title="{{__('Add Call')}}" >
                                                {{ __('Add Call') }}
                                            </a>

                                        </li>
                                        <li>
                                            <a class="dropdown-item" data-size="md" data-url="{{ route('deals.tasks.create', $deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Task') }}" data-title="{{__('Add Task')}}" >
                                                {{ __('Add Task') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" data-size="md" data-url="{{ route('deals.meeting.create', $deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Meeting') }}" data-title="{{__('Add Meeting')}}" >
                                                {{ __('Add Meeting') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" data-size="md" data-url="{{ route('deals.visit.create', $deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Visit') }}" data-title="{{__('Add Visit')}}" >
                                                {{ __('Add Visit') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>



                        </div>
                        <!-- Tab Navigation -->
                        <div>
                            <button onclick="showTab('calls')" class="btn btn-link" id="tab-calls">Calls</button>
                            <button onclick="showTab('tasks')" class="btn btn-link" id="tab-tasks">Tasks</button>
                            <button onclick="showTab('meeting')" class="btn btn-link" id="tab-meeting">Meeting</button>
                            <button onclick="showTab('files')" class="btn btn-link" id="tab-files">Visits</button>

                        </div>
                    </div>

                    <!-- Tab Content -->

                    <div class="card-body">
                        <!-- Calls Section -->
                        <div id="content-calls" class="tab-content">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Subject') }}</th>
                                            <th>{{ __('Call Type') }}</th>
                                            <th>{{ __('Duration') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($calls as $call)
                                            <tr>
                                                <td>{{ $call->subject }}</td>
                                                <td>{{ ucfirst($call->call_type) }}</td>
                                                <td>{{ $call->duration }}</td>
                                                <td>{{ isset($call->getLeadCallUser) ? $call->getLeadCallUser->name : '-' }}</td>
                                                <td>
                                                    @can('edit lead call')
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ URL::to('leads/' . $deal->id . '/call/' . $call->id . '/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Call') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('delete lead call')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['leads.calls.destroy', $deal->id, $call->id], 'class' => 'modalForm', 'id' => 'delete-form-' . $deal->id]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i class="ti ti-trash text-white"></i></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tasks Section -->
                        <div id="content-tasks" class="tab-content" style="display: none;">
                            @if(!$tasks->isEmpty())
                                <ul class="list-group list-group-flush mt-2">
                                    @foreach($tasks as $task)
                                    @php
                                    // dd($task->id);
                                    @endphp
                                        <li class="list-group-item px-0">
                                            <div class="d-block d-sm-flex align-items-start">
                                                {{-- <div class="form-check form-switch form-switch-right img-fluid me-3 mb-2 mb-sm-0">
                                                    <input class="form-check-input task-checkbox" type="checkbox" role="switch" id="task_{{$task->id}}" @if($task->status) checked="checked" @endcan value="{{$task->status}}" data-url="{{route('deals.tasks.update_status',[$deal->id,$task->id])}}">
                                                    <label class="form-check-label pe-5" for="task_{{$task->id}}"></label>
                                                </div> --}}
                                                <div class="w-100">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h5 class="mb-0">
                                                                {{$task->name}}
                                                                @if($task->status)
                                                                    <div class="badge bg-primary mb-1">{{__(\App\Models\DealTask::$status[$task->status])}}</div>
                                                                @else
                                                                    <div class="badge bg-warning mb-1">{{__(\App\Models\DealTask::$status[$task->status])}}</div>
                                                                @endif
                                                            </h5>
                                                            <small class="text-sm">{{__(\App\Models\DealTask::$priorities[$task->priority])}} - {{Auth::user()->dateFormat($task->date)}} {{Auth::user()->timeFormat($task->time)}}</small>
                                                            <span class="text-muted text-sm">
                                                                @if($task->status)
                                                                    <div class="badge badge-pill badge-success mb-1">{{__(\App\Models\DealTask::$status[$task->status])}}</div>
                                                                @else
                                                                    <div class="badge badge-pill badge-warning mb-1">{{__(\App\Models\DealTask::$status[$task->status])}}</div>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <span>
                                                        @can('edit task')
                                                                <div class="action-btn bg-info ms-2">
                                                                <a href="#" class="" data-title="{{__('Edit Task')}}" data-url="{{route('deals.tasks.edit',[$deal->id,$task->id])}}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Task')}}"><i class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                            @endcan
                                                            @can('delete task')
                                                                <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['deals.tasks.destroy',$deal->id,$task->id], 'class' => 'modalForm']) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                                                                {!! Form::close() !!}
                                                                </div>
                                                            @endcan
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center">
                                    No Tasks Available.!
                                </div>
                            @endif
                        </div>

                        <!-- Meeting Section -->
                        <div id="content-meeting" class="tab-content" style="display: none;">
                            @if(!$meetings->isEmpty())
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table datatable">
                                                    <thead>
                                                    <tr>
                                                        <th>{{__('Meeting title')}}</th>
                                                        <th>{{__('Meeting Date')}}</th>
                                                        <th>{{__('Meeting Time')}}</th>
                                                        @if(Gate::check('edit meeting') || Gate::check('delete meeting'))
                                                            <th width="200px">{{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                    </thead>
                                                    <tbody class="font-style">
                                                    @foreach ($meetings as $meeting)
                                                        <tr>
                                                            <td>{{ $meeting->title }}</td>
                                                            <td>{{  \Auth::user()->dateFormat($meeting->date) }}</td>
                                                            <td>{{  \Auth::user()->timeFormat($meeting->time) }}</td>
                                                            @if(Gate::check('edit meeting') || Gate::check('delete meeting'))
                                                                <td>
                                                                    @can('edit meeting')
                                                                        <div class="action-btn bg-primary ms-2">
                                                                            <a href="#" data-url="{{route('deals.meeting.edit',[$deal->id,$meeting->id])}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Meeting')}}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                                        </div>
                                                                    @endcan
                                                                    @can('delete meeting')
                                                                    <div class="action-btn bg-danger ms-2">
                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['deals.meeting.destroy',$deal->id,$meeting->id], 'class' => 'modalForm']) !!}
                                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
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
                            @else
                            <div class="text-center">
                                No Meetings Available.!
                            </div>
                            @endif
                        </div>
                        <div id="content-files" class="tab-content" style="display: none;">
                            <div class="lead-view-section">
                                @foreach ($visits as $view)
                                    <div class="lead-view-item" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border: 1px solid #ddd; padding: 15px;">
                                        <div style="flex: 1; padding-right: 15px;">
                                            <h4>{{ $view->title }}</h4>
                                            <p><strong>Description:</strong> {!! $view->description !!}</p>
                                            <p><strong>Assigned Users:</strong>
                                                @php
                                                    // Decode the assigned_users JSON
                                                    $assignedUsers = json_decode($view->assigned_users, true);
                                                    $userNames = \App\Models\User::whereIn('id', $assignedUsers)->pluck('name')->toArray();
                                                @endphp
                                                {{ implode(', ', $userNames) }}
                                            </p>
                                            <p><strong>Date:</strong> {{ $view->date }}</p>
                                            <p><strong>Time:</strong> {{ $view->time }}</p>
                                            <p><strong>Location:</strong> {{ $view->location }}</p>
                                            <p><strong>Status:</strong> {{ $view->status }}</p>
                                            <p><strong>Recurrence:</strong> {{ \App\Models\LeadView::$recurrances[$view->recurrence] ?? 'None' }}</p>
                                            <p><strong>Repeat Interval:</strong> {{ $view->repeat_interval }}</p>
                                            <p><strong>End Recurrence:</strong> {{ $view->end_recurrence }}</p>
                                            <p><strong>Reminder:</strong> {{ $view->reminder }}</p>

                                            <!-- File Display Section -->
                                            {{-- <div class="file-list">
                                                <h5>Attached Files:</h5>
                                                @php
                                                    // Decode the file_ids stored in the view
                                                    $fileIds = json_decode($view->file_ids, true);
                                                    $fileIdsArray = explode(',', $fileIds[0]); // Converts "30,31,32" into [30, 31, 32]
                                                    $files = \App\Models\LeadFile::whereIn('id', $fileIdsArray)->get();
                                                @endphp

                                                @if($files->isNotEmpty())
                                                    <ul class="file-horizontal-list">
                                                        @foreach($files as $file)
                                                            <li>
                                                                @if(in_array(strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg']))
                                                                    <div class="file-image">
                                                                        <img src="{{ asset('storage/lead_files/' . $file->file_path) }}" alt="{{ $file->file_name }}" width="200" height="auto">
                                                                    </div>
                                                                @else
                                                                    <a href="{{ asset('storage/lead_files/' . $file->file_path) }}" target="_blank">
                                                                        Download {{ $file->file_name }}
                                                                    </a>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p>No files attached for this task.</p>
                                                @endif
                                            </div> --}}

                                            <style>
                                                .file-horizontal-list {
                                                    display: flex;
                                                    list-style-type: none;
                                                    padding: 0;
                                                }

                                                .file-horizontal-list li {
                                                    margin-right: 15px;
                                                    display: flex;
                                                    align-items: center;
                                                }

                                                .file-image img {
                                                    max-width: 150px;
                                                    height: auto;
                                                    border: 1px solid #ddd;
                                                    border-radius: 4px;
                                                    padding: 5px;
                                                }
                                            </style>

                                        </div>

                                        <span>
                                            {{-- @can('edit task') --}}
                                                    <div class="action-btn bg-info ms-2">
                                                    <a href="#" class="" data-title="{{__('Edit Visit')}}" data-url="{{route('deals.visit.edit',[$deal->id,$view->id])}}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                {{-- @endcan --}}
                                                {{-- @can('delete task') --}}
                                                    <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['deals.visit.destroy',$deal->id,$view->id], 'class' => 'modalForm']) !!}
                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                    </div>
                                                {{-- @endcan --}}
                                            </span>
                                    </div>
                                    <hr>
                                @endforeach
                            </div>



                        </div>

                    </div>
                </div>

                 <!-- JavaScript for Toggling Tabs -->
                 <script>
                    function showTab(tabName) {
                        // Hide all tab content
                        document.querySelectorAll('.tab-content').forEach(function(content) {
                            content.style.display = 'none';
                        });

                        // Remove active class from all buttons
                        document.querySelectorAll('.btn-link').forEach(function(button) {
                            button.classList.remove('active');
                        });

                        // Show the selected tab and set the button as active
                        document.getElementById('content-' + tabName).style.display = 'block';
                        document.getElementById('tab-' + tabName).classList.add('active');
                    }

                    // Set default tab
                    document.addEventListener("DOMContentLoaded", function() {
                        showTab('calls'); // Default tab
                    });
                </script>

                <!-- CSS for active tab button -->
                <style>
                    .btn-link.active {
                        font-weight: bold;
                        text-decoration: underline;
                    }
                </style>


                <div id="products">
                    <div class="row">

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>{{__('Products')}}</h5>
                                        <div class="float-end">
                                            <a data-size="md" data-url="{{ route('deals.products.edit',$deal->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Product')}}" data-title="{{__('Add Product')}}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-plus text-white"></i>
                                            </a>
                                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#leadProducts" aria-expanded="false" aria-controls="leadProducts" onclick="toggleleadProductsIcon(this)">
                                                <i class="fas fa-chevron-right"></i> <!-- Starts with the right arrow -->
                                            </button>
                                            <script>
                                                function toggleleadProductsIcon(button) {
                                                    const icon = button.querySelector('i');
                                                    icon.classList.toggle('fa-chevron-down');
                                                    icon.classList.toggle('fa-chevron-right');
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                                <div id="leadProducts" class="collapse" aria-labelledby="">

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th>{{__('Price')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($deal->products() as $product)
                                                    <tr>
                                                        <td>
                                                            {{$product->name}}
                                                        </td>
                                                        <td>
                                                            {{\Auth::user()->priceFormat($product->sale_price)}}
                                                        </td>
                                                        @can('edit deal')
                                                            <td>
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['deals.products.destroy', $deal->id,$product->id], 'class' => 'modalForm']) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </td>
                                                        @endcan
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
                </div>

                <div id="discussion_note">
                    <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>{{__('Discussion')}}</h5>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush mt-2">
                                        @if(!$deal->discussions->isEmpty())
                                            @foreach($deal->discussions as $discussion)
                                                <li class="list-group-item px-0">
                                                    <div class="d-block d-sm-flex align-items-start">
                                                        <img src="@if($discussion->user->avatar) {{asset('/storage/uploads/avatar/'.$discussion->user->avatar)}} @else {{asset('/storage/uploads/avatar/avatar.png')}} @endif"
                                                            class="img-fluid wid-40 me-3 mb-2 mb-sm-0" alt="image">
                                                        <div class="w-100">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="mb-3 mb-sm-0">
                                                                    <h6 class="mb-0"> {{$discussion->comment}}</h6>
                                                                </div>
                                                                <div class="form-check form-switch form-switch-right mb-2">
                                                                    {{$discussion->created_at->diffForHumans()}} by
                                                                    <span class="text-muted text-sm">{{$discussion->user->name}}</span>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="text-center">
                                                {{__(' No Data Available.!')}}
                                            </li>
                                        @endif
                                    </ul>
                                    {{ Form::model($deal, array('route' => array('deals.discussion.store', $deal->id), 'method' => 'POST', 'class' => 'modalForm')) }}
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12 form-group">
                                                {{ Form::label('comment', __('Message'),['class'=>'form-label']) }}
                                                {{ Form::textarea('comment', null, array('class' => 'form-control')) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
                                        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
                                    </div>
                                    {{Form::close()}}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>{{__('Notes')}}</h5>
                                        @php
                                            $settings = \App\Models\Utility::settings();
                                        @endphp
                                        @if($settings['ai_chatgpt_enable'] == 'on')
                                            <div class="float-end">
                                                <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm" data-ajax-popup-over="true" id="grammarCheck" data-url="{{ route('grammar',['grammar']) }}"
                                                data-bs-placement="top" data-title="{{ __('Grammar check with AI') }}">
                                                    <i class="ti ti-rotate"></i> <span>{{__('Grammar check with AI')}}</span>
                                                </a>
                                                <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['lead']) }}"
                                                data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                                                    <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body">
                                    <textarea class="summernote-simple" name="note">{!! $deal->notes !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="salesAndFinance" class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5>{{ __('Sales and Finance') }}</h5>
                            <div class="float-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="activityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-plus text-white"></i> {{ __('Add Activity') }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="activityDropdown">
                                        <li><a class="dropdown-item">{{ __('Add Sales') }}</a></li>
                                        <li><a class="dropdown-item">{{ __('Add Finance') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Navigation -->
                        <div>
                            <button onclick="showTab('sales-finance')" class="btn btn-link" id="tab-sales-finance">Sales & Finance</button>
                            <button onclick="showTab('estimates')" class="btn btn-link" id="tab-estimates">Estimates</button>
                            <button onclick="showTab('quotation')" class="btn btn-link" id="tab-quotation">Quotation</button>
                            <button onclick="showTab('invoices')" class="btn btn-link" id="tab-invoices">Invoices</button>
                            <button onclick="showTab('sales-orders')" class="btn btn-link" id="tab-sales-orders">Sales Orders</button>
                            <button onclick="showTab('performa-invoice')" class="btn btn-link" id="tab-performa-invoice">Performa Invoice</button>
                            <button onclick="showTab('proposal')" class="btn btn-link" id="tab-proposal">Proposal</button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="card-body">
                        <!-- Sales & Finance -->
                        <div id="content-sales-finance" class="tab-content">
                            <p>Static content for Sales & Finance.</p>
                        </div>

                        <!-- Estimates -->
                        <div id="content-estimates" class="tab-content" style="display: none;">
                            <p>Static content for Estimates.</p>
                        </div>

                        <!-- Quotation -->
                        <div id="content-quotation" class="tab-content" style="display: none;">
                            <p>Static content for Quotation.</p>
                        </div>

                        <!-- Invoices -->
                        <div id="content-invoices" class="tab-content" style="display: none;">
                            <p>Static content for Invoices.</p>
                        </div>

                        <!-- Sales Orders -->
                        <div id="content-sales-orders" class="tab-content" style="display: none;">
                            <p>Static content for Sales Orders.</p>
                        </div>

                        <!-- Performa Invoice -->
                        <div id="content-performa-invoice" class="tab-content" style="display: none;">
                            <p>Static content for Performa Invoice.</p>
                        </div>

                        <!-- Proposal -->
                        <div id="content-proposal" class="tab-content" style="display: none;">
                            <p>Static content for Proposal.</p>
                        </div>
                    </div>
                </div>

                <!-- JavaScript for Toggling Tabs -->
                <script>
                    function showTab(tabName) {
                        // Hide all tab content
                        document.querySelectorAll('.tab-content').forEach(function(content) {
                            content.style.display = 'none';
                        });

                        // Remove active class from all buttons
                        document.querySelectorAll('.btn-link').forEach(function(button) {
                            button.classList.remove('active');
                        });

                        // Show the selected tab and set the button as active
                        document.getElementById('content-' + tabName).style.display = 'block';
                        document.getElementById('tab-' + tabName).classList.add('active');
                    }

                    // Set default tab
                    document.addEventListener("DOMContentLoaded", function() {
                        showTab('sales-finance'); // Default tab
                    });
                </script>

                <!-- CSS for active tab button -->
                <style>
                    .btn-link.active {
                        font-weight: bold;
                        text-decoration: underline;
                    }
                </style>

                <div id="files" class="card">
                    <div class="card-header ">
                        <h5>{{__('Files')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12 dropzone top-5-scroll browse-file" id="dropzonewidget"></div>
                    </div>
                </div>

                <div id="timeline" class="card">
                    <div class="card-header">
                        <h5>{{__('Timeline')}}</h5>
                    </div>
                    <div class="card-body ">
                        <div class="row leads-scroll" >
                            <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                @if(!$deal->activities->isEmpty())
                                    @foreach($deal->activities as $activity)
                                        <li class="list-group-item card mb-3">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col-auto mb-3 mb-sm-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="theme-avtar bg-primary">
                                                            <i class="ti ti-{{ $activity->logIcon() }}"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <span class="text-dark text-sm">{{ __($activity->log_type) }}</span>
                                                            <h6 class="m-0">{!! $activity->getRemark() !!}</h6>
                                                            <small class="text-muted">{{$activity->created_at->diffForHumans()}}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">

                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @else
                                    No activity found yet.
                                @endif
                            </ul>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
