@extends('layouts.admin')

@section('page-title')
    {{__('Hierarchy Management')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Structure')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        {{-- @can('create goal type') --}}
        @if(!$hasIncompleteHierarchy)
        <a href="{{ route('hierarchy.create') }}" title="{{ __('Create') }}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
        @endif


        {{-- @endcan --}}
    </div>
@endsection


@section('content')


@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if ($hierarchies->isEmpty())
    <p>No hierarchies found.</p>
@else
@foreach ($hierarchies->reverse() as $index => $hierarchy) <!-- Using $index for unique ID -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">

        <h3>{{ $hierarchy->name }}</h3>
        {{-- <a href="{{ route('hierarchy.next', $hierarchy->id) }}" class="btn btn-secondary btn-sm ms-2">&gt;</a> --}}

        <div>
            <a href="{{ route('hierarchy.previous', $hierarchy->id) }}" class="btn btn-secondary btn-sm">History</a>
            <a href="{{ route('hierarchy.edit', $hierarchy->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <div class="action-btn bg-danger ms-2">
                {!! Form::open(['method' => 'DELETE', 'route' => ['hierarchy.destroy', $hierarchy->id],'id'=>'delete-form-'.$hierarchy->id]) !!}
                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="card-body d-flex justify-content-center align-items-center" style="height: 400px; position: relative;"> <!-- Centering the content -->
        <style>
            .link {
                fill: none;
                stroke: #ccc;
                stroke-width: 2px;
            }

            .node circle {
                fill: #fff;
                stroke: steelblue;
                stroke-width: 3px;
            }

            .node text {
                font: 12px sans-serif;
            }

            /* Ensure the SVG scales responsively */
            svg {
                width: 100%;
                height: auto;
            }
        </style>

        <!-- Placeholder for the hierarchy tree -->
        <div id="hierarchy-chart-{{ $index }}" style="width: 100%; height: 100%;"></div> <!-- Unique ID for each chart -->

        <!-- Include D3.js from CDN -->
        <script src="https://d3js.org/d3.v6.min.js"></script>

        <script>
            // Define the hierarchy data in JSON format
            var data = {!! json_encode($hierarchy->structure) !!};

            // Set dimensions for the SVG element
            var width = document.getElementById('hierarchy-chart-{{ $index }}').clientWidth, height = 400;

            // Create an SVG container
            var svg = d3.select("#hierarchy-chart-{{ $index }}").append("svg") // Use unique ID
                .attr("viewBox", `0 0 ${width} ${height}`) // Set viewBox for responsive scaling
                .append("g")
                .attr("transform", "translate(40,40)");

            // Create a tree layout and size it based on the height
            var tree = d3.tree().size([width - 80, height - 100]);

            // Create a root hierarchy node
            var root = d3.hierarchy(data);

            // Apply the tree layout to the data
            tree(root);

            // Draw the links (connections)
            svg.selectAll(".link")
                .data(root.links())
                .enter().append("path")
                .attr("class", "link")
                .attr("d", d3.linkVertical()
                .x(d => d.x)
                .y(d => d.y));

            // Draw the nodes (people/roles)
            var node = svg.selectAll(".node")
                .data(root.descendants())
                .enter().append("g")
                .attr("class", "node")
                .attr("transform", d => `translate(${d.x},${d.y})`);

            // Add circles for each node
            node.append("circle").attr("r", 10);

            // Add labels for each node
            node.append("text")
                .attr("dy", ".35em")
                .attr("x", d => d.children ? -13 : 13)
                .style("text-anchor", d => d.children ? "end" : "start")
                .text(d => d.data.name);
        </script>
    </div>
</div>
@endforeach

@endif

@endsection
