@extends('layouts.admin')
@section('page-title')
    {{__('Manage Purchase Request')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Purchase Request')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{-- <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('productservice.file.import') }}" data-ajax-popup="true" data-title="{{__('Import product CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('productservice.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a> --}}

        <a href="{{ route('purchase_request.create') }}" class="btn btn-sm btn-primary">
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
                                <th class="text-center">{{ __('Purchase Request Code') }}</th>
                                <th class="text-center">{{ __('Purchase Request Name') }}</th>
                                <th class="text-center">{{ __('Requester') }}</th>
                                <th class="text-center">{{ __('Department') }}</th>
                                <th class="text-center">{{ __('Request Time') }}</th>
                                <th class="text-center">{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($purchaseRequests as $request)
                            <tr>
                                <td class="text-center">{{ $request->purchase_request_code }}</td>
                                <td class="text-center">{{ $request->purchase_request_name }}</td>
                                <td class="text-center">{{ $request->requester_id ?? 'N/A' }}</td> <!-- assuming requester relationship is set -->
                                <td class="text-center">{{ $request->department_id ?? 'N/A' }}</td> <!-- assuming department relationship is set -->
                                <td class="text-center">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                <td class="text-center">{{ ucfirst($request->status) }}</td>
                                <td class="text-center">
                                    {{-- <a href="{{ route('purchase_request.show', $request->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                    </a>
                                    <a href="{{ route('purchase_request.edit', $request->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                                    </a> --}}
                                    <!-- You can add delete button or any other actions as needed -->

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
                                        <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__(' Details')}}" data-title="{{__(' Details')}}">
                                            <i class="ti ti-eye text-white"></i>
                                        </a>
                                    </div>
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('purchase_request.edit', $request->id) }}" class="mx-3 btn btn-sm align-items-center"> <i class="ti ti-pencil text-white"></i></a>
                                        </div>

                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['purchase_request.destroy', $request->id], 'id'=>'delete-form-'.$request->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}">
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


@endsection

