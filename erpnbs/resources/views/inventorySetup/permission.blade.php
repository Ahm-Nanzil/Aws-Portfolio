@extends('layouts.admin')
@section('page-title')
    {{__('Manage Inventory Setup')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Permissions')}}</li>
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
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                    <tr>
                                        <th>{{__('Role')}} </th>
                                        <th>{{__('Permissions')}} </th>
                                        <th width="150">{{__('Action')}} </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($roles as $role)
                                        @if($role->name != 'client')
                                            <tr class="font-style">
                                                <td class="Role">{{ $role->name }}</td>
                                                <td class="Permission">
                                                    @foreach ($role->permissions()->pluck('name') as $permissionName)
                                                    <span
                                                        class="badge rounded p-2 m-1 px-3 bg-primary">{{ $permissionName }}</span>
                                                @endforeach
                                                </td>
                                                <td class="Action">
                                                <span>
                                                    @can('edit role')
                                                    {{-- <a href="{{ route('roles.edit.single',$role->id) }}">test..........</a> --}}
                                                        <div class="action-btn bg-info ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('roles.edit.single',$role->id) }}" data-ajax-popup="true"  data-size="lg" data-bs-toggle="tooltip" title="{{__('Single Pemission')}}" data-title="{{__('Single Permission')}}">
                                                            <i class="fas fa-shield-alt text-white"></i>
                                                        </a>
                                                        </div>
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ route('roles.edit',$role->id) }}" data-ajax-popup="true"  data-size="lg" data-bs-toggle="tooltip" title="{{__('Hierarchical Pemission')}}" data-title="{{__('Hierarchical Permission')}}">
                                                                <i class="fas fa-users-cog text-white"></i>
                                                            </a>
                                                            </div>
                                                    @endcan
                                                        @if($role->name != 'Employee')
                                                            {{-- @can('delete role')
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white"></i></a>
                                                                    {!! Form::close() !!}
                                                                 </div>
                                                            @endcan --}}
                                                        @endif
                                                </span>
                                                </td>
                                            </tr>
                                        @endif
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
@endsection
