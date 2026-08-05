@extends('layouts.admin')
@section('page-title')
    <div class="row align-items-center">
        <div class="col">
            {{__('Manage Leads')}} @if($pipeline) - {{$pipeline->name}} @endif
        </div>

    </div>


@endsection


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
                        $.ajax({
                            url: '{{route('leads.order')}}',
                            type: 'POST',
                            data: {lead_id: id, stage_id: stage_id, order: order, new_status: new_status, old_status: old_status, pipeline_id: pipeline_id, "_token": $('meta[name="csrf-token"]').attr('content')},
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
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>


    <div class="col-auto">
        <div class="dropdown" style="position: relative;">
            <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; z-index: 2;">
                Filter Leads
            </button>
            <div class="dropdown-menu p-3" aria-labelledby="filterDropdown" style="width: 300px; position: absolute; top: 100%; left: 0; z-index: 1;" onclick="event.stopPropagation();">
                <div class="mb-3">
                    <label for="savedFilters" class="form-label">Saved Filters</label>
                    <select id="savedFilters" class="form-select">
                        <option value="">Select a saved filter</option>
                        @foreach ($allfilter as $filter)
                            <option value="{{ json_encode($filter->criteria) }}" data-title="{{ $filter->title }}">
                                {{ $filter->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <form action="{{ route('leads.filter') }}" method="POST">
                    @csrf
                    <!-- Text Search -->
                    <div class="mb-3">
                        <label for="textSearch" class="form-label">Text Search</label>
                        <input type="text" class="form-control" id="textSearch" name="textSearch" placeholder="Search by keyword...">
                    </div>

                    <!-- Lead Status -->
                    <div class="mb-3">
                        <div class="form-group">
                            {{ Form::label('stages', __('Stages'), ['class'=>'form-label']) }}
                            {{ Form::select(
                                'stages[]',
                                $stages,
                                null,
                                ['class' => 'form-control select2', 'multiple' => '', 'id' => 'stage-select']
                            ) }}
                        </div>
                    </div>

                    <!-- Lead Source -->
                    <div class="mb-3">
                        <div class="col-12 form-group">
                            {{ Form::label('sources', __('Sources'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                            {{ Form::select('sources[]', $sources, isset($selectedSources) ? $selectedSources : null, ['class' => 'form-control select2', 'id' => 'choices-multiple1', 'multiple' => '', 'required' => 'required']) }}
                        </div>
                    </div>

                    <!-- Assigned To -->
                    <div class="mb-3">
                        <div class="form-group">
                            {{ Form::label('users', __('Owner'), ['class'=>'form-label']) }}
                            {{ Form::select(
                                'users[]',
                                $users,
                                null,
                                ['class' => 'form-control select2', 'multiple' => '', 'id' => 'user-select']
                            ) }}
                        </div>
                    </div>

                    <!-- Date Created -->
                    <div class="mb-3">
                        <label for="dateCreated" class="form-label">Date Created</label>
                        <input type="date" class="form-control" id="dateCreated" name="dateCreated">
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Search by Location...">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </form>
            </div>
        </div>
    </div>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('savedFilters').addEventListener('change', function() {
        var selectedFilter = this.value ? JSON.parse(this.value) : null;

        console.log('Selected Filter:', selectedFilter); // Debugging to verify selected filter data

        // Auto-fill the form fields with the selected filter's data
        if (selectedFilter) {

            console.log('Sources:', selectedFilter.source);


            // Text Search
            if (selectedFilter.textSearch !== undefined) {
                document.getElementById('textSearch').value = selectedFilter.textSearch || '';
            }

            // Location
            if (selectedFilter.location !== undefined) {
                document.getElementById('location').value = selectedFilter.location || '';
            }

            // Date Created
            if (selectedFilter.dateCreated !== undefined) {
                document.getElementById('dateCreated').value = selectedFilter.dateCreated || '';
            }

            // Set the selected stages
            var stageSelect = document.getElementById('stage-select');
            if (selectedFilter.stages) {
                selectedFilter.stages.forEach(function(stage) {
                    var option = stageSelect.querySelector('option[value="' + stage + '"]');
                    if (option) {
                        option.selected = true;
                    }
                });
            }

            // Set the selected sources (for multiple select)
        var sourceSelect = document.getElementById('choices-multiple1');
        selectedFilter.source.forEach(function(source) {
            var option = sourceSelect.querySelector('option[value="' + source + '"]');
            if (option) {
                option.selected = true;
            }
        });
        console.log('Sources get:', sourceSelect); // Debugging

        console.log('Sources:', selectedFilter.source); // Debugging

            // Set the selected users
            var userSelect = document.getElementById('user-select');
            if (selectedFilter.users) {
                selectedFilter.users.forEach(function(user) {
                    var option = userSelect.querySelector('option[value="' + user + '"]');
                    if (option) {
                        option.selected = true;
                    }
                });
            }
        }
    });
});

</script> --}}

{{-- document.addEventListener('DOMContentLoaded', function() {
    // // Assuming selectedFilter is the object containing the filter data
    // var selectedFilter = {
    //     textSearch: 'mumbai',
    //     stages: ['4'],
    //     source: ['2'],
    //     users: ['13'],
    //     dateCreated: '2024-11-20',
    //     location: 'Dhaka'
    // };
        var selectedFilter = this.value ? JSON.parse(this.value) : null;


    // Set the selected sources for the select2 dropdown
    var sourceSelect = document.getElementById('choices-multiple1');
    if (sourceSelect && selectedFilter.source) {
        selectedFilter.source.forEach(function(source) {
            var option = sourceSelect.querySelector('option[value="' + source + '"]');
            if (option) {
                option.selected = true;
            }
        });
        // Trigger select2 to update the UI
        $('#choices-multiple1').trigger('change');
    }
}); --}}
{{-- <script>
    window.onload = function() {
        // Check if savedFilters element exists
        var savedFilters = document.getElementById('savedFilters');

        if (savedFilters) {
            console.log("Element found, adding event listener.");

            savedFilters.addEventListener('change', function() {
                console.log("Change event triggered on savedFilters");

                // Dummy filter data for testing
                var selectedFilter = {
                    textSearch: 'mumbai',
                    stages: ['4'],
                    source: ['2'],
                    users: ['13'],
                    dateCreated: '2024-11-20',
                    location: 'Dhaka'
                };

                // Update the text search input
                document.getElementById('textSearch').value = selectedFilter.textSearch || '';

                // Update stages dropdown
                var stageSelect = document.getElementById('stage-select');
                if (stageSelect && selectedFilter.stages) {
                    Array.from(stageSelect.options).forEach(option => {
                        option.selected = selectedFilter.stages.includes(option.value);
                    });
                    $(stageSelect).trigger('change'); // Update select2
                }

                // Update sources dropdown
                var sourceSelect = document.getElementById('choices-multiple1');
                if (sourceSelect && selectedFilter.source) {
                    Array.from(sourceSelect.options).forEach(option => {
                        option.selected = selectedFilter.source.includes(option.value);
                    });
                    $(sourceSelect).trigger('change'); // Update select2
                }

                // Update users dropdown
                var userSelect = document.getElementById('user-select');
                if (userSelect && selectedFilter.users) {
                    Array.from(userSelect.options).forEach(option => {
                        option.selected = selectedFilter.users.includes(option.value);
                    });
                    $(userSelect).trigger('change'); // Update select2
                }

                // Update date created input
                document.getElementById('dateCreated').value = selectedFilter.dateCreated || '';

                // Update location input
                document.getElementById('location').value = selectedFilter.location || '';
            });
        } else {
            console.error("Element 'savedFilters' not found in the DOM.");
        }
    };
</script> --}}



@endsection

@section('action-btn')


    <div class="float-end">
        {{ Form::open(array('route' => 'deals.change.pipeline','id'=>'change-pipeline','class'=>'btn btn-sm')) }}
        {{ Form::select('default_pipeline_id', $pipelines,$pipeline->id, array('class' => 'form-control select me-4','id'=>'default_pipeline_id')) }}
        {{ Form::close() }}

        <a href="{{ route('leads.list') }}" data-size="lg" data-bs-toggle="tooltip" title="{{__('List View')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-list"></i>
        </a>
        <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('leads.file.import') }}" data-ajax-popup="true" data-title="{{__('Import Lead CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('leads.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('leads.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Lead')}}" data-title="{{__('Create Lead')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            @php
                $lead_stages = $pipeline->leadStages;
                $json = [];
                foreach ($lead_stages as $lead_stage){
                    $json[] = 'task-list-'.$lead_stage->id;
                }
            @endphp
            <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
                @foreach($lead_stages as $lead_stage)
                @php
                    $query = $lead_stage->leads(); // This returns a query builder

                    // Apply filters conditionally if they exist
                    if (isset($filters)) {
                        // Apply text search filter
                        if ($filters['textSearch']) {
                            $query->where(function($q) use ($filters) {
                                $q->where('name', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('email', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('company_entity_name', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('company_email', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('company_phone_ll1', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('company_phone_ll2', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('address1', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('address2', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('city', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('region', 'like', '%' . $filters['textSearch'] . '%')
                                ->orWhere('country', 'like', '%' . $filters['textSearch'] . '%');
                            });
                        }

                        // Apply stages filter (multi-select)
                        if ($filters['stages']) {
                            $query->whereIn('stage_id', $filters['stages']);
                        }

                        // Apply source filter (multi-select)
                        if ($filters['source']) {
                            $query->whereIn('sources', $filters['source']);
                        }

                        // Apply users filter (multi-select)
                        if ($filters['users']) {
                            $query->whereIn('user_id', $filters['users']);
                        }

                        // Apply date created filter
                        if ($filters['dateCreated']) {
                            $query->whereDate('created_at', $filters['dateCreated']);
                        }

                        // Apply location filter
                        if ($filters['location']) {
                            $query->where('location', 'like', '%' . $filters['location'] . '%');
                        }

                        // Get the filtered leads
                        $leads = $query->get(); // Execute the query and get the results
                    } else {
                        // If no filters, get all leads for the current stage
                        $leads = $query->get(); // Execute the query and get all leads
                    }

                    // dd($leads);  // Optionally uncomment for debugging
                @endphp



                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    <span class="btn btn-sm btn-primary btn-icon count">
                                        {{count($leads)}}
                                    </span>
                                </div>
                                <h4 class="mb-0">{{$lead_stage->name}}</h4>
                            </div>
                            <div class="card-body kanban-box" id="task-list-{{$lead_stage->id}}" data-id="{{$lead_stage->id}}">
                                @foreach($leads as $lead)
                                    <div class="card" data-id="{{$lead->id}}">
                                        <div class="pt-3 ps-3">
                                            @php($labels = $lead->labels())
                                            @if($labels)
                                                @foreach($labels as $label)
                                                    <div class="badge-xs badge bg-{{$label->color}} p-2 px-3 rounded">{{$label->name}}</div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="card-header border-0 pb-0 position-relative">
                                            <h5><a href="@can('view lead')@if($lead->is_active){{route('leads.show',$lead->id)}}@else#@endif @else#@endcan">{{$lead->name}}</a></h5>
                                            <div class="card-header-right">
                                                @if(Auth::user()->type != 'Client')
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            @can('edit lead')
                                                                <a href="#!" data-size="md" data-url="{{ URL::to('leads/'.$lead->id.'/labels') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Labels')}}">
                                                                    <i class="ti ti-bookmark"></i>
                                                                    <span>{{__('Labels')}}</span>
                                                                </a>

                                                                <a href="#!" data-size="lg" data-url="{{ URL::to('leads/'.$lead->id.'/edit') }}" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="{{__('Edit Lead')}}">
                                                                    <i class="ti ti-pencil"></i>
                                                                    <span>{{__('Edit')}}</span>
                                                                </a>
                                                            @endcan
                                                            @can('delete lead')
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                <a href="#!" class="dropdown-item bs-pass-para">
                                                                    <i class="ti ti-archive"></i>
                                                                    <span> {{__('Delete')}} </span>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            @endcan


                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <?php
                                        $products = $lead->products();
                                        $sources = $lead->sources();
                                        ?>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <ul class="list-inline mb-0">

                                                    <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{__('Product')}}">
                                                        <i class="f-16 text-primary ti ti-shopping-cart"></i> {{count($products)}}
                                                    </li>

                                                    <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{__('Source')}}">
                                                        <i class="f-16 text-primary ti ti-social"></i>{{count($sources)}}
                                                    </li>
                                                </ul>
                                                <div class="user-group">
                                                    @foreach($lead->users as $user)
                                                        <img src="@if($user->avatar) {{asset('/storage/uploads/avatar/'.$user->avatar)}} @else {{asset('storage/uploads/avatar/avatar.png')}} @endif" alt="image" data-bs-toggle="tooltip" title="{{$user->name}}">
                                                    @endforeach
                                                </div>
                                            </div>
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
