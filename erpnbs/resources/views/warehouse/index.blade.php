@extends('layouts.admin')
@section('page-title')
    {{__('Warehouse')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Warehouse')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">

        <a href="#" data-size="lg" data-url="{{ route('warehouse.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Warehouse')}}"  class="btn btn-sm btn-primary">
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
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Address')}}</th>
                                    <th>{{__('City')}}</th>
                                    <th>{{__('Code')}}</th> <!-- Added Code column -->
                                    <th>{{__('State')}}</th> <!-- Added State column -->
                                    <th>{{__('Postal Code')}}</th> <!-- Added Postal Code column -->
                                    <th>{{__('Display')}}</th> <!-- Added Display column -->
                                    <th>{{__('Hide When Out of Stock')}}</th> <!-- Added Hide When Out of Stock column -->
                                    <th>{{__('Note')}}</th> <!-- Added Note column -->
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($warehouses as $warehouse)
                                    <tr class="font-style">
                                        <td>{{ $warehouse->name }}</td>
                                        <td>{{ $warehouse->address }}</td>
                                        <td>{{ $warehouse->city }}</td>
                                        <td>{{ $warehouse->code }}</td> <!-- Displaying the Code -->
                                        <td>{{ $warehouse->state }}</td> <!-- Displaying the State -->
                                        <td>{{ $warehouse->postal_code }}</td> <!-- Displaying the Postal Code -->
                                        <td>{{ $warehouse->display ? 'Yes' : 'No' }}</td> <!-- Displaying Display status -->
                                        <td>{{ $warehouse->hide_when_out_of_stock ? 'Yes' : 'No' }}</td> <!-- Displaying Hide When Out of Stock status -->
                                        <td>{{ $warehouse->note }}</td> <!-- Displaying the Note -->

                                        @if(Gate::check('show warehouse') || Gate::check('edit warehouse') || Gate::check('delete warehouse'))
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

                                                @can('show warehouse')
                                                    <div class="action-btn bg-warning ms-2">
                                                        <a href="{{ route('warehouse.show',$warehouse->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                           data-bs-toggle="tooltip" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                    </div>
                                                @endcan
                                                @can('edit warehouse')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center" data-url="{{ route('warehouse.edit',$warehouse->id) }}" data-ajax-popup="true"  data-size="lg " data-bs-toggle="tooltip" title="{{__('Edit')}}"  data-title="{{__('Edit Warehouse')}}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete warehouse')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['warehouse.destroy', $warehouse->id],'id'=>'delete-form-'.$warehouse->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white"></i></a>
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
