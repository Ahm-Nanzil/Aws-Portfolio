@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Reset')}}</li>
@endsection
@push('css-page')

@endpush
@push('script-page')

@endpush

@section('action-btn')

@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.inventory_setup')
        </div>
        <div class="col-9">
            <div class="row">
                <form action="{{ route('inventory.reset') }}" method="POST" onsubmit="return confirm('Are you sure you want to reset all inventory data? This action cannot be undone!')">
                    @csrf
                    <button type="submit" class="btn btn-danger">Reset Inventory</button>
                </form>

            </div>

        </div>
    </div>
@endsection
