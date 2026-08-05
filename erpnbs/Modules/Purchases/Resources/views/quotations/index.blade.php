@extends('layouts.admin')
@section('page-title')
    {{__('Manage Quotations')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Quotations')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{-- <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('productservice.file.import') }}" data-ajax-popup="true" data-title="{{__('Import product CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('productservice.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a> --}}

        <a href="{{ route('quotations.create') }}" class="btn btn-sm btn-primary">
            create new estimate
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
                                <th class="text-center">{{ __('Estimate #') }}</th>
                                <th class="text-center">{{ __('Amount (USD)') }}</th>
                                <th class="text-center">{{ __('Vendors') }}</th>
                                <th class="text-center">{{ __('Purchase Request') }}</th>
                                <th class="text-center">{{ __('Date') }}</th>
                                <th class="text-center">{{ __('Expiry Date') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($estimates as $estimate)
                            <tr>
                                <td class="text-center">{{ $estimate->estimate_number }}</td>
                                <td class="text-center">{{ number_format($estimate->grand_total, 2) }}</td>
                                <td class="text-center">{{ $estimate->vendor_id }}</td>
                                <td class="text-center">{{ $estimate->purchase_request_id }}</td>
                                <td class="text-center">{{ $estimate->estimate_date }}</td>
                                <td class="text-center">{{ $estimate->expiry_date }}</td>
                                <td class="text-center">

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

