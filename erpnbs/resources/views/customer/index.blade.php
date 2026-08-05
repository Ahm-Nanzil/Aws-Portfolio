@extends('layouts.admin')
@php
 //   $profile=asset(Storage::url('uploads/avatar/'));
$profile=\App\Models\Utility::get_file('uploads/avatar');
@endphp
@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
@endpush
@push('script-page')
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {

                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');

                        var old_status = $("#" + source.id).data('status');
                        var new_status = $("#" + target.id).data('status');
                        var stage_id = $(target).attr('data-id');
                        var pipeline_id = '{{$pipeline->id}}';

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);
                         // Log data before the AJAX call
    console.log({
        customer_id: id,
        stage_id: stage_id,
        order: order,
        new_status: new_status,
        old_status: old_status,
        pipeline_id: pipeline_id,
        _token: $('meta[name="csrf-token"]').attr('content')
    });
                        $.ajax({
                            url: '{{route('customer_stages.order')}}',
                            type: 'POST',
                            data: {customer_id: id, stage_id: stage_id, order: order, new_status: new_status, old_status: old_status, pipeline_id: pipeline_id, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('error', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);


    </script>
    <script>
        $(document).on("change", "#default_pipeline_id", function () {
            $('#change-pipeline').submit();
        });
    </script>
        <script>
            $(document).on('click', '#billing_data', function () {
                $("[name='shipping_name']").val($("[name='billing_name']").val());
                $("[name='shipping_country']").val($("[name='billing_country']").val());
                $("[name='shipping_state']").val($("[name='billing_state']").val());
                $("[name='shipping_city']").val($("[name='billing_city']").val());
                $("[name='shipping_phone']").val($("[name='billing_phone']").val());
                $("[name='shipping_zip']").val($("[name='billing_zip']").val());
                $("[name='shipping_address']").val($("[name='billing_address']").val());
            })

        </script>
        <script>
            $(document).on('submit', '.modalForm', function (e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');

                // Save the current URL dynamically
                var showLeadUrl = window.location.href;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        // Close the modal
                        $('#commonModal').modal('hide');
                        show_toastr('success', 'Successfully Done');

                        // Refresh the page or redirect explicitly to showLeadUrl
                        window.location.href = showLeadUrl;
                    },
                    error: function (xhr) {
                        $('#website-feedback').text('Duplicate Identified, Please give something else').show();
                        show_toastr('error', 'Duplicate Website Identified, Please give something else');
                    }
                });
            });
        </script>

@endpush

@section('page-title')
    {{__('Manage Customers')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Customer')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        {{ Form::open(array('route' => 'customer.change.pipeline','id'=>'change-pipeline','class'=>'btn btn-sm')) }}
        {{ Form::select('default_pipeline_id', $pipelines,$pipeline->id, array('class' => 'form-control select me-4','id'=>'default_pipeline_id')) }}
        {{ Form::close() }}
        <a href="{{ route('customer.list') }}" data-size="lg" data-bs-toggle="tooltip" title="{{__('List View')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-list"></i>
        </a>
        <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('customer.file.import') }}" data-ajax-popup="true" data-title="{{__('Import customer CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('customer.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>

        <a href="#" data-size="lg" data-url="{{ route('customer.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Customer')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')


<div class="row">
    <div class="col-sm-12">
        @php
            $stages = $pipeline->customerStages;

                 $json = [];
                 foreach ($stages as $stage){
                     $json[] = 'task-list-'.$stage->id;
                 }
        @endphp
        <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
            @foreach($stages as $stage)

                @php($customers = $stage->customers())
                {{-- @dd($customers) --}}
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <div class="float-end">
                                <span class="btn btn-sm btn-primary btn-icon count">
                                    {{count($customers)}}
                                </span>
                            </div>
                            <h4 class="mb-0">{{$stage->name}}</h4>
                        </div>
                        <div class="card-body kanban-box" id="task-list-{{$stage->id}}" data-id="{{$stage->id}}">
                            @foreach($customers as $customer)
                                <div class="card" data-id="{{ $customer->id }}">
                                    <div class="card-header border-0 pb-0">
                                        <h5>
                                            @can('show customer')

                                            <a href="{{ route('customer.show',\Crypt::encrypt($customer['id'])) }}">
                                                {{ $customer->name }}
                                            </a></h5>
                                            @endcan

                                        <div class="card-header-right">
                                            <div class="btn-group card-option">
                                                <button type="button" class="btn dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @can('show customer')
                                                        <a href="{{ route('customer.show',\Crypt::encrypt($customer['id'])) }}" class="dropdown-item">
                                                            <i class="ti ti-eye"></i>
                                                            <span>{{ __('View') }}</span>
                                                        </a>
                                                    @endcan
                                                    @can('edit customer')
                                                        <a href="#" data-url="{{ route('customer.edit', $customer->id) }}" data-ajax-popup="true" class="dropdown-item">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>
                                                    @endcan
                                                    @can('delete customer')
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['customer.destroy', $customer->id], 'id' => 'delete-form-' . $customer->id]) !!}
                                                        <a href="#" class="dropdown-item bs-pass-para">
                                                            <i class="ti ti-trash"></i>
                                                            <span>{{ __('Delete') }}</span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>{{ __('Contact:') }}</strong> {{ $customer->contact }}</p>
                                        <p><strong>{{ __('Email:') }}</strong> {{ $customer->email }}</p>
                                        <p><strong>{{ __('Balance:') }}</strong> {{ \Auth::user()->priceFormat($customer->balance) }}</p>
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
@endsection
