<div class="row">
    <div class="col-md-4 col-lg-4 col-xl-4 mb-4">
        <div class="card customer-detail-box customer_card">
            <div class="card-body">
                <h5 class="card-title">{{__('Customer Info')}}</h5>
                <p class="card-text mb-0">{{$customer['name']}}</p>
                <p class="card-text mb-0">{{$customer['email']}}</p>
                <p class="card-text mb-0">{{$customer['contact']}}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-4 col-xl-4 mb-4">
        <div class="card customer-detail-box customer_card">
            <div class="card-body">
                <h5 class="card-title">{{__('Billing Info')}}</h5>
                <p class="card-text mb-0">{{$customer['billing_name']}}</p>
                <p class="card-text mb-0">{{$customer['billing_address']}}</p>
                <p class="card-text mb-0">{{$customer['billing_city'].', '. $customer['billing_state'] .', '.$customer['billing_zip']}}</p>
                <p class="card-text mb-0">{{$customer['billing_country']}}</p>
                <p class="card-text mb-0">{{$customer['billing_phone']}}</p>
            </div>
        </div>

    </div>
    <div class="col-md-4 col-lg-4 col-xl-4 mb-4">
        <div class="card customer-detail-box customer_card">
            <div class="card-body">
                <h5 class="card-title">{{__('Shipping Info')}}</h5>
                <p class="card-text mb-0">{{$customer['shipping_name']}}</p>
                <p class="card-text mb-0">{{$customer['shipping_address']}}</p>
                <p class="card-text mb-0">{{$customer['shipping_city'].', '. $customer['shipping_state'] .', '.$customer['shipping_zip']}}</p>
                <p class="card-text mb-0">{{$customer['shipping_country']}}</p>
                <p class="card-text mb-0">{{$customer['shipping_phone']}}</p>
            </div>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card pb-0">
            <div class="card-body">
                <h5 class="card-title">{{__('Company Info')}}</h5>

                <div class="row">
                    @php
                        $totalInvoiceSum=$customer->customerTotalInvoiceSum($customer['id']);
                        $totalInvoice=$customer->customerTotalInvoice($customer['id']);
                        $averageSale=($totalInvoiceSum!=0)?$totalInvoiceSum/$totalInvoice:0;
                    @endphp
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <p class="card-text mb-0">{{__('Customer Id')}}</p>
                            <h6 class="report-text mb-3">{{AUth::user()->customerNumberFormat($customer['customer_id'])}}</h6>
                            <p class="card-text mb-0">{{__('Total Sum of Invoices')}}</p>
                            <h6 class="report-text mb-0">{{\Auth::user()->priceFormat($totalInvoiceSum)}}</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <p class="card-text mb-0">{{__('Date of Creation')}}</p>
                            <h6 class="report-text mb-3">{{\Auth::user()->dateFormat($customer['created_at'])}}</h6>
                            <p class="card-text mb-0">{{__('Quantity of Invoice')}}</p>
                            <h6 class="report-text mb-0">{{$totalInvoice}}</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <p class="card-text mb-0">{{__('Balance')}}</p>
                            <h6 class="report-text mb-3">{{\Auth::user()->priceFormat($customer['balance'])}}</h6>
                            <p class="card-text mb-0">{{__('Average Sales')}}</p>
                            <h6 class="report-text mb-0">{{\Auth::user()->priceFormat($averageSale)}}</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <p class="card-text mb-0">{{__('Overdue')}}</p>
                            <h6 class="report-text mb-3">{{\Auth::user()->priceFormat($customer->customerOverdue($customer['id']))}}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-border-style table-border-style">
                <h5 class="d-inline-block mb-5">{{__('Proposal')}}</h5>

                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                        <tr>
                            <th>{{__('Proposal')}}</th>
                            <th>{{__('Issue Date')}}</th>
                            <th>{{__('Amount')}}</th>
                            <th>{{__('Status')}}</th>
                            @if(Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                                <th width="10%"> {{__('Action')}}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($customer->customerProposal($customer->id) as $proposal)
                            <tr>
                                <td class="Id">
                                    <a href="{{ route('proposal.show',\Crypt::encrypt($proposal->id)) }}" class="btn btn-outline-primary">{{ AUth::user()->proposalNumberFormat($proposal->proposal_id) }}
                                    </a>
                                </td>
                                <td>{{ Auth::user()->dateFormat($proposal->issue_date) }}</td>
                                <td>{{ Auth::user()->priceFormat($proposal->getTotal()) }}</td>
                                <td>
                                    @if($proposal->status == 0)
                                        <span class="badge bg-primary p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                    @elseif($proposal->status == 1)
                                        <span class="badge bg-warning p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                    @elseif($proposal->status == 2)
                                        <span class="badge bg-danger p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                    @elseif($proposal->status == 3)
                                        <span class="badge bg-info p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                    @elseif($proposal->status == 4)
                                        <span class="badge bg-success p-2 px-3 rounded status_badge">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                    @endif
                                </td>
                                @if(Gate::check('edit proposal') || Gate::check('delete proposal') || Gate::check('show proposal'))
                                    <td class="Action">
                                        <span>
                                          @if($proposal->is_convert==0)
                                                @can('convert invoice')
                                                    <div class="action-btn bg-warning ms-2">
                                                    {!! Form::open(['method' => 'get', 'route' => ['proposal.convert', $proposal->id],'id'=>'proposal-form-'.$proposal->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" data-original-title="{{__('Convert to Invoice')}}" title="{{__('Convert to Invoice')}}" data-confirm="You want to confirm convert to invoice. Press Yes to continue or Cancel to go back" data-confirm-yes="document.getElementById('proposal-form-{{$proposal->id}}').submit();">
                                                            <i class="ti ti-exchange text-white"></i>
                                                        </a>
                                                     {!! Form::close() !!}
                                                </div>
                                                @endcan
                                            @else
                                                @can('convert invoice')
                                                    <div class="action-btn bg-warning ms-2">
                                                    <a href="{{ route('invoice.show',\Crypt::encrypt($proposal->converted_invoice_id)) }}"
                                                       class="mx-3 btn btn-sm  align-items-center" data-bs-toggle="tooltip" title="{{__('Already convert to Invoice')}}" data-original-title="{{__('Already convert to Invoice')}}" >
                                                        <i class="ti ti-file text-white"></i>
                                                    </a>
                                                </div>
                                                @endcan
                                            @endif
                                            @can('duplicate proposal')
                                                <div class="action-btn bg-success ms-2">
                                                {!! Form::open(['method' => 'get', 'route' => ['proposal.duplicate', $proposal->id],'id'=>'duplicate-form-'.$proposal->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" data-original-title="{{__('Duplicate')}}"  title="{{__('Duplicate Proposal')}}" data-confirm="You want to confirm duplicate this invoice. Press Yes to continue or Cancel to go back" data-confirm-yes="document.getElementById('duplicate-form-{{$proposal->id}}').submit();">
                                                        <i class="ti ti-copy text-white text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                            @can('show proposal')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="{{ route('proposal.show',\Crypt::encrypt($proposal->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Show')}}" data-original-title="{{__('Detail')}}">
                                                        <i class="ti ti-eye text-white text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('edit proposal')
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="{{ route('proposal.edit',\Crypt::encrypt($proposal->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan

                                            @can('delete proposal')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['proposal.destroy', $proposal->id],'id'=>'delete-form-'.$proposal->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip"  title="Delete" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$proposal->id}}').submit();">
                                                        <i class="ti ti-trash text-white text-white"></i>
                                                     </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </span>
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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-border-style table-border-style">
                <h5 class="d-inline-block mb-5">{{__('Invoice')}}</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{__('Invoice')}}</th>
                            <th>{{__('Issue Date')}}</th>
                            <th>{{__('Due Date')}}</th>
                            <th>{{__('Due Amount')}}</th>
                            <th>{{__('Status')}}</th>
                            @if(Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                <th width="10%"> {{__('Action')}}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($customer->customerInvoice($customer->id) as $invoice)
                            <tr>
                                <td class="Id">
                                    <a href="{{ route('invoice.show',\Crypt::encrypt($invoice->id)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                    </a>
                                </td>
                                <td>{{ \Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                <td>
                                    @if(($invoice->due_date < date('Y-m-d')))
                                        <p class="text-danger"> {{ \Auth::user()->dateFormat($invoice->due_date) }}</p>
                                    @else
                                        {{ \Auth::user()->dateFormat($invoice->due_date) }}
                                    @endif
                                </td>
                                <td>{{\Auth::user()->priceFormat($invoice->getDue())  }}</td>
                                <td>
                                    @if($invoice->status == 0)
                                        <span class="badge bg-primary p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 1)
                                        <span class="badge bg-warning p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 2)
                                        <span class="badge bg-danger p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 3)
                                        <span class="badge bg-info p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 4)
                                        <span class="badge bg-success p-2 px-3 rounded status_badge">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @endif
                                </td>
                                @if(Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                    <td class="Action">
                                        <span>
                                             @can('copy invoice')
                                                <div class="action-btn bg-warning ms-2">
                                                    <a class="mx-3 btn btn-sm align-items-center"  id="{{ route('invoice.link.copy',$invoice->id) }}"  onclick="copyToClipboard(this)" data-bs-toggle="tooltip" data-original-title="{{__('Copy Invoice')}}"><i class="ti ti-link text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('duplicate invoice')
                                                <div class="action-btn bg-success ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" data-original-title="{{__('Duplicate')}}" title="{{__('Duplicate Invoice')}}" data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back" data-confirm-yes="document.getElementById('duplicate-form-{{$invoice->id}}').submit();">
                                                        <i class="ti ti-copy text-white text-white"></i>
                                                        {!! Form::open(['method' => 'get', 'route' => ['invoice.duplicate', $invoice->id],'id'=>'duplicate-form-'.$invoice->id]) !!}
                                                        {!! Form::close() !!}
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('show invoice')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="{{ route('invoice.show',\Crypt::encrypt($invoice->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Show')}}" data-original-title="{{__('Detail')}}">
                                                            <i class="ti ti-eye text-white text-white"></i>
                                                        </a>
                                                    </div>
                                            @endcan
                                            @can('edit invoice')
                                                <div class="action-btn bg-primary ms-2">
                                                        <a href="{{ route('invoice.edit',\Crypt::encrypt($invoice->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                            @endcan
                                            @can('delete invoice')
                                                <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id],'id'=>'delete-form-'.$invoice->id]) !!}

                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$invoice->id}}').submit();">
                                                            <i class="ti ti-trash text-white text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                            @endcan
                                            </span>
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
<div class="card">
    <div class="card-header" id="basicInfoHeader">
        <h5 class="mb-0 d-flex justify-content-between align-items-center">
            {{ __('Basic Info') }}
            <div class="float-end">
            <a data-size="md" data-url="{{ route('customer.basicinfo.edit',$customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit Basic Info')}}" data-title="{{__('Edit Basic Info')}}" class="btn btn-sm btn-primary">
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


                <!-- Company Website -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company Website') }}</p>
                    <h5><a href="{{ $customer->company_website }}" target="_blank">{{ $customer->company_website ?? '' }}</a></h5>
                </div>

                <!-- Company/Entity Name -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company / Entity Name') }}</p>
                    <h5>{{ $customer->company_entity_name ?? '' }}</h5>
                </div>

                <!-- Company/Entity Logo -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company / Entity Logo') }}</p>
                    @if($customer->company_entity_logo)
                    <img src="{{ asset('storage/app/public/' . $customer->company_entity_logo) }}" alt="Company Logo" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px;">
                    @endif
                </div>

                <!-- Company Phone LL1 -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company Phone LL1') }}</p>
                    <h5>{{ $customer->company_phone_ll1 ?? '' }}</h5>
                </div>

                <!-- Company Phone LL2 -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company Phone LL2') }}</p>
                    <h5>{{ $customer->company_phone_ll2 ?? '' }}</h5>
                </div>

                <!-- Company Email -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company Email') }}</p>
                    <h5>{{ $customer->company_email ?? '' }}</h5>
                </div>

                <!-- Address1 -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Address1') }}</p>
                    <h5>{{ $customer->address1 ?? '' }}</h5>
                </div>

                <!-- Address2 -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Address2') }}</p>
                    <h5>{{ $customer->address2 ?? '' }}</h5>
                </div>

                <!-- City -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('City') }}</p>
                    <h5>{{ $customer->city ?? '' }}</h5>
                </div>

                <!-- Region/State -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Region/State') }}</p>
                    <h5>{{ $customer->region ?? '' }}</h5>
                </div>

                <!-- Country -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Country') }}</p>
                    <h5>{{ $customer->country ?? '' }}</h5>
                </div>

                <!-- Zip Code -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Zip Code') }}</p>
                    <h5>{{ $customer->zip_code ?? '' }}</h5>
                </div>

                <!-- Company LinkedIn -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company LinkedIn') }}</p>
                    <h5><a href="{{ $customer->company_linkedin }}" target="_blank">{{ $customer->company_linkedin ?? '' }}</a></h5>
                </div>

                <!-- Company Location -->
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-0">{{ __('Company Location') }}</p>
                    <h5><a href="{{ $customer->company_location }}" target="_blank">{{ $customer->company_location ?? '' }}</a></h5>
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
                            <a data-size="md" data-url="{{ route('customer.sources.edit',$customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Source')}}" data-title="{{__('Add Source')}}" class="btn btn-sm btn-primary">
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
                                    @php
                                                            $sources = $customer->sources();

                                    @endphp

                                @foreach($sources as $source)
                                    <tr>
                                        <td>{{$source->name}} </td>
                                        {{-- @can('edit lead') --}}
                                            <td>
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['customer.sources.destroy', $customer->id,$source->id], 'class' => 'modalForm','id'=>'delete-form-'.$customer->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
                                        {{-- @endcan --}}
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
        <a data-size="md" data-url="{{ route('customer.primarycontact.edit',$customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit Primary Contact Info')}}" data-title="{{__('Edit Primary Contact')}}" class="btn btn-sm btn-primary">
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


                <div class="row">
                    <!-- Salutation -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Salutation') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->salutation) ? $customer->salutation : '' }}</h5>
                    </div>

                    <!-- First Name -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('First Name') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->first_name) ? $customer->first_name : '' }}</h5>
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Last Name') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->last_name) ? $customer->last_name : '' }}</h5>
                    </div>

                    <!-- Mobile Primary -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Mobile Primary') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->mobile_primary) ? $customer->mobile_primary : '' }}</h5>
                    </div>

                    <!-- Mobile Secondary -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Mobile Secondary') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->mobile_secondary) ? $customer->mobile_secondary : '' }}</h5>
                    </div>

                    <!-- Email Work -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Email Work') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->email_work) ? $customer->email_work : '' }}</h5>
                    </div>

                    <!-- Email Personal -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Email Personal') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->email_personal) ? $customer->email_personal : '' }}</h5>
                    </div>

                    <!-- Phone LL -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Phone LL') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->phone_ll) ? $customer->phone_ll : '' }}</h5>
                    </div>

                    <!-- Company Phone LL -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Company Phone LL') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->company_phone_ll) ? $customer->company_phone_ll : '' }}</h5>
                    </div>

                    <!-- Extension # -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('Extension #') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->extension) ? $customer->extension : '' }}</h5>
                    </div>

                    <!-- LinkedIn Profile -->
                    <div class="col-md-6 col-sm-6 mt-3">
                        <p class="text-muted text-sm mb-0">{{ __('LinkedIn Profile') }}</p>
                        <h5 class="mb-0">{{ !empty($customer->linkedin_profile) ? $customer->linkedin_profile : '' }}</h5>
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
                    <a data-size="md" data-bs-target="#contactModal" data-url="{{ route('customer.additional-contact.create',$customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create Additional Contact')}}" data-title="{{__('Create Additional Contact')}}" class="btn btn-sm btn-primary">
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
                @foreach(json_decode($customer->additional_contacts, true) ?? [] as $index => $contact)
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
                                {{-- @can('edit lead') --}}
                                    <div class="col-md-12 mt-2">
                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['customer.additional_contacts.destroy', $customer->id, $index], 'class' => 'modalForm', 'id' => 'delete-form-' . $index]) !!}
                                            <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                {{-- @endcan --}}
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

    @php
            $emails = $customer->emails;
    @endphp
    <div id="emails">
        <div class="row">

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5>{{__('Emails')}}</h5>
                            @can('create lead email')
                                <div class="float-end">
                                    <a data-size="md" data-url="{{ route('customer.emails.create',$customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create Email')}}" data-title="{{__('Create Email')}}" class="btn btn-sm btn-primary">
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
                    $tasks = $customer->tasks;
                    // dd($tasks);
                @endphp
                <div class="float-end">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="activityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-plus text-white"></i> {{ __('Add Activity') }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="activityDropdown">
                            <li>

                                <a class="dropdown-item" data-size="md" data-url="{{ route('customer.calls.create', $customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Call') }}" data-title="{{__('Add Call')}}" >
                                    {{ __('Add Call') }}
                                </a>

                            </li>
                            <li>
                                <a class="dropdown-item" data-size="md" data-url="{{ route('customer.tasks.create', $customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Task') }}" data-title="{{__('Add Task')}}" >
                                    {{ __('Add Task') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" data-size="md" data-url="{{ route('customer.meeting.create', $customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Meeting') }}" data-title="{{__('Add Meeting')}}">
                                    {{ __('Add Meeting') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" data-size="md" data-url="{{ route('customer.visit.create', $customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Add Visit') }}" data-title="{{__('Add Visit')}}">
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

        @php
                    $calls = $customer->calls;
                    $tasks = $customer->tasks;
                    $meetings = $customer->meetings;
                    $visits = $customer->visits;



        @endphp
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

                                                <div class="action-btn bg-info ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ URL::to('customer/' . $customer->id . '/call/' . $call->id . '/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Call') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>


                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['customer.calls.destroy', $customer->id, $call->id], 'class' => 'modalForm', 'id' => 'delete-form-' . $customer->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>

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
                                                            <div class="badge bg-primary mb-1">{{__(\App\Models\CustomerTask::$status[$task->status])}}</div>
                                                        @else
                                                            <div class="badge bg-warning mb-1">{{__(\App\Models\CustomerTask::$status[$task->status])}}</div>
                                                        @endif
                                                    </h5>
                                                    <small class="text-sm">{{__(\App\Models\CustomerTask::$priorities[$task->priority])}} - {{Auth::user()->dateFormat($task->date)}} {{Auth::user()->timeFormat($task->time)}}</small>
                                                    <span class="text-muted text-sm">
                                                        @if($task->status)
                                                            <div class="badge badge-pill badge-success mb-1">{{__(\App\Models\CustomerTask::$status[$task->status])}}</div>
                                                        @else
                                                            <div class="badge badge-pill badge-warning mb-1">{{__(\App\Models\CustomerTask::$status[$task->status])}}</div>
                                                        @endif
                                                    </span>
                                                </div>
                                                <span>
                                                @can('edit task')
                                                        <div class="action-btn bg-info ms-2">
                                                        <a href="#" class="" data-title="{{__('Edit Task')}}" data-url="{{route('customer.tasks.edit',[$customer->id,$task->id])}}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                    @endcan
                                                    @can('delete task')
                                                        <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['customer.tasks.destroy',$customer->id,$task->id], 'class' => 'modalForm']) !!}
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
                                                        <td>

                                                                <div class="action-btn bg-primary ms-2">
                                                                    <a href="#" data-url="{{route('customer.meeting.edit',[$customer->id,$meeting->id])}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Meeting')}}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Meeting')}}"><i class="ti ti-pencil text-white"></i></a>
                                                                </div>


                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['customer.meeting.destroy',$customer->id,$meeting->id], 'class' => 'modalForm']) !!}
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
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
                    @else
                    <div class="text-center">
                        No Meetings Available.!
                    </div>
                    @endif
                </div>

                <div id="content-files" class="tab-content" style="display: none;">
                    @if(!$visits->isEmpty())
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
                                    <p><strong>Recurrence:</strong> {{ \App\Models\CustomerVisit::$recurrances[$view->recurrence] ?? 'None' }}</p>
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
                                                                <img src="{{ asset('storage/customer_files/' . $file->file_path) }}" alt="{{ $file->file_name }}" width="200" height="auto">
                                                            </div>
                                                        @else
                                                            <a href="{{ asset('storage/customer_files/' . $file->file_path) }}" target="_blank">
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
                                            <div class="action-btn bg-info ms-2">
                                            <a href="#" class="" data-title="{{__('Edit Visit')}}" data-url="{{route('customer.visit.edit',[$customer->id,$view->id])}}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Visit')}}"><i class="ti ti-pencil text-white"></i></a>
                                        </div>
                                            <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['customer.visit.destroy',$customer->id,$view->id], 'class' => 'modalForm']) !!}
                                            <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                                            {!! Form::close() !!}
                                            </div>
                                </span>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center">
                        No Visits Available.!
                    </div>
                    @endif



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
                                <a data-size="md" data-url="{{ route('customer.products.edit',$customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Product')}}" data-title="{{__('Add Product')}}" class="btn btn-sm btn-primary">
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
                                    @foreach($customer->products() as $product)
                                        <tr>
                                            <td>
                                                {{$product->name}}
                                            </td>
                                            <td>
                                                {{\Auth::user()->priceFormat($product->sale_price)}}
                                            </td>
                                            @can('edit lead')
                                                <td>
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['customer.products.destroy', $customer->id,$product->id], 'class' => 'modalForm']) !!}
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
                            {{-- <div class="float-end">
                                <a data-size="lg" data-url="{{ route('leads.discussions.create',$customer->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Message')}}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus text-white"></i>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mt-2">
                            @if(!$customer->discussions->isEmpty())
                                @foreach($customer->discussions as $discussion)
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
                        {{ Form::model($customer, array('route' => array('customer.discussion.store', $customer->id), 'method' => 'POST', 'class' => 'modalForm')) }}
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
                        <textarea class="summernote-simple" name="note">{!! $customer->notes !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            <div class="float-end">

            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#leadtimeline" aria-expanded="false" aria-controls="leadtimeline" onclick="toggleleadtimelineIcon(this)">
                <i class="fas fa-chevron-right"></i> <!-- Starts with the right arrow -->
            </button>
            <script>
                function toggleleadtimelineIcon(button) {
                    const icon = button.querySelector('i');
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-right');
                }
            </script>
            </div>
        </div>
        <div id="leadtimeline" class="collapse" aria-labelledby="">

            <div class="card-body ">

                <div class="row leads-scroll" >
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        @if(!$customer->activities->isEmpty())
                            @foreach($customer->activities as $activity)
                                <li class="list-group-item card mb-3">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="ti {{ $activity->logIcon() }}"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <span class="text-dark text-sm">{{ __($activity->log_type) }}</span>
                                                    <h6 class="m-0">{!! $activity->getCustomerRemark() !!}</h6>
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

