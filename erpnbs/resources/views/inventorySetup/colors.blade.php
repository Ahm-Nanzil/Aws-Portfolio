@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Colors')}}</li>
@endsection
@push('css-page')

@endpush
@push('script-page')

@endpush

@section('action-btn')
    <div class="float-end">
            <a href="#" data-url="{{ route('inventory.colors.create') }}" data-ajax-popup="true" data-title="{{__('Create New Color')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.inventory_setup')
        </div>
        <div class="col-9">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>
                                Colors List
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Color Code') }}</th>
                                                <th>{{ __('Color Name') }}</th>
                                                <th>{{ __('Color Hex') }}</th>
                                                <th>{{ __('Order') }}</th>
                                                <th>{{ __('Note') }}</th>
                                                <th>{{ __('Display') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($colors as $color)
                                                <tr>
                                                    <td>{{ $color->color_code }}</td>
                                                    <td>{{ $color->color_name }}</td>
                                                    <td>
                                                        <span class="badge" style="background-color: {{ $color->color_hex }};">
                                                            {{ $color->color_hex }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $color->order }}</td>
                                                    <td>{{ $color->note }}</td>
                                                    <td>{{ $color->display ? __('Yes') : __('No') }}</td>
                                                    <td>
                                                        <!-- Actions (Edit, Delete) -->
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="#" class=""  data-url="{{ route('inventory.colors.edit', $color->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Color')}}"><i class="ti ti-pencil text-white"></i></a>
                                                        </div>
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['inventory.colors.destroy', $color->id], 'class' => 'modalForm','id'=>'delete-form-'.$color->id]) !!}
                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                            {!! Form::close() !!}
                                                        </div>

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">{{ __('No colors found.') }}</td>
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
@endsection
