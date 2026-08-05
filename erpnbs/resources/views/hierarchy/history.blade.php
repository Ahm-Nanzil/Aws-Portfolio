
@extends('layouts.admin')

@section('page-title')
    {{__('Hierarchy Management')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">History</li>
    @if($hierarchy!=null)

    <li class="breadcrumb-item">{{$hierarchy->name}}</li>
    @endif
    @endsection

@section('action-btn')
@if($hierarchy!=null)

    <div class="float-end">
        <a href="{{ route('hierarchy_structure') }}" class="btn btn-secondary">Go Back</a>

        <a href="{{ route('hierarchy.previous', $hierarchy->id) }}" class="btn btn-primary btn-sm">&lt;</a>
        <a href="{{ route('hierarchy.next', $hierarchy->id) }}" class="btn btn-primary btn-sm ms-2">&gt;</a>

        <a href="{{ route('hierarchy.edit', $hierarchy->id) }}"
            title="{{ __('Edit') }}"
            class="btn btn-sm btn-primary">
            <i class="ti edit">Edit</i>
         </a>
         <div class="action-btn bg-danger ms-2">
            {!! Form::open(['method' => 'DELETE', 'route' => ['hierarchy.destroy', $hierarchy->id],'id'=>'delete-form-'.$hierarchy->id]) !!}
            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
            {!! Form::close() !!}
        </div>

    </div>
@endif
@endsection

@section('content')

@if($hierarchy==null)
<div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div style="text-align: center;">
        There is no History left <br>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Go Back</a>
    </div>
</div>

@else
<div class="container">

    <!-- Hierarchy Structure Display -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mt-4">
                <div class="card-header">
                    @if($hierarchy->child==null)
                        Current
                    @endif
                     Hierarchy Structure Updated : {{ $hierarchy->updated_at->format('Y-m-d H:i:s') }}
                </div>
                <div class="card-body">
                    <ul id="hierarchyList" class="list-unstyled"></ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Visual Tree Card -->
            <div class="card mt-4">
                <div class="card-header">
                    Visual Hierarchy Tree
                </div>
                <div class="card-body">
                    <div id="visualTreeContainer" style="width: 100%; height: 500px; border: 1px solid #ccc;">
                        <p class="text-muted">The visual hierarchy tree will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="structure" id="hierarchyStructure" value="{{ $hierarchy->structure }}">

</div>
@endif
<style>
ul {
    list-style-type: none; /* Remove default list styles */
    padding-left: 20px; /* Indent child nodes */
    position: relative; /* Position context for pseudo-elements */
}

li {
    position: relative; /* Make li relative for absolute positioning of lines */
    margin: 10px 0; /* Space between nodes */
}

/* Connect lines from parent to child */
li:before {
    content: "";
    position: absolute;
    left: -10px; /* Position line to the left of the node */
    top: 12px; /* Position line at the middle of the node */
    width: 1px; /* Line width */
    height: 10px; /* Line height */
    background: #ccc; /* Line color */
}

/* Horizontal line for parent-child connection */
li > ul:before {
    content: "";
    position: absolute;
    left: -20px; /* Position line to the left of the node */
    top: 20px; /* Position the horizontal line below the parent */
    width: 10px; /* Width of the line before children */
    height: 1px; /* Line height */
}

/* Collapse class to hide the child nodes */
ul.collapsed {
    display: none; /* Hide the child nodes by default when collapsed */
}

/* Adjust line visibility for leaf nodes */
li:last-child:before {
    height: 10px; /* Shorten line height for last child */
}

/* Style for expand/collapse icons */
.expand-collapse-icon {
    cursor: pointer; /* Change cursor to pointer for expand/collapse */
    margin-right: 5px; /* Space between icon and node name */
}
</style>

<script>
    let hierarchyStructure = JSON.parse(document.getElementById('hierarchyStructure').value);

    function renderHierarchyList(node, parentElement) {
        const listItem = document.createElement('li');
        const nodeWrapper = document.createElement('div');
        nodeWrapper.classList.add('node-wrapper');

        // Use a span for the expand/collapse icon
        const expandCollapseIcon = document.createElement('span');
        expandCollapseIcon.textContent = node.children.length > 0 ? ' + ' : ''; // Show + only if there are children
        expandCollapseIcon.classList.add('expand-collapse-icon');

        // Create the node name
        nodeWrapper.innerHTML = `
            <span class="node-name">- - - - - - - - - ${node.name}</span>
        `;

        nodeWrapper.prepend(expandCollapseIcon); // Add icon before the node name

        // Toggle expand/collapse functionality
        nodeWrapper.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent event bubbling to parent elements
            if (node.children.length > 0) {
                const subList = listItem.querySelector('ul');
                if (subList) {
                    subList.classList.toggle('collapsed'); // Toggle visibility of children
                    expandCollapseIcon.textContent = subList.classList.contains('collapsed') ? ' + ' : ' - '; // Change icon
                }
            }
        });

        listItem.appendChild(nodeWrapper);

        // Check for children and render them
        if (node.children.length > 0) {
            const subList = document.createElement('ul');
            subList.classList.add('collapsed'); // Add class to hide by default
            node.children.forEach(child => renderHierarchyList(child, subList));
            listItem.appendChild(subList);
        }

        parentElement.appendChild(listItem);
    }

    function updateHierarchyDisplay() {
        const hierarchyList = document.getElementById('hierarchyList');
        hierarchyList.innerHTML = '';
        renderHierarchyList(hierarchyStructure, hierarchyList);
    }

    // Initial population of the hierarchy display
    updateHierarchyDisplay();

    // Function to render the visual tree using SVG
    function renderVisualTree(hierarchy) {
        const container = document.getElementById('visualTreeContainer');
        container.innerHTML = '';  // Clear previous content

        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', '100%');

        const root = hierarchy;
        const levelGap = 100;
        const nodeGap = 100;
        let xOffset = 400;
        let yOffset = 50;

        // Function to draw nodes and lines recursively
        function drawNode(node, parentX, parentY, x, y) {
            // Draw a circle for the node
            const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', x);
            circle.setAttribute('cy', y);
            circle.setAttribute('r', '20');
            circle.setAttribute('stroke', 'black');
            circle.setAttribute('fill', 'lightblue');
            svg.appendChild(circle);

            // Add text to the node
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', x);
            text.setAttribute('y', y + 5);
            text.setAttribute('text-anchor', 'middle');
            text.textContent = node.name;
            svg.appendChild(text);

            // If it's not the root node, draw a line connecting to the parent
            if (parentX !== null && parentY !== null) {
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', parentX);
                line.setAttribute('y1', parentY);
                line.setAttribute('x2', x);
                line.setAttribute('y2', y - 20);  // Offset so it connects to the top of the child node
                line.setAttribute('stroke', 'black');
                svg.appendChild(line);
            }

            // Calculate the new positions for the child nodes
            const nextY = y + levelGap;
            let nextX = x - (node.children.length - 1) * nodeGap / 2;

            // Draw each child node recursively
            node.children.forEach(child => {
                drawNode(child, x, y + 20, nextX, nextY);  // Offset y by 20 so it connects at the bottom of the parent node
                nextX += nodeGap;
            });
        }

        // Start drawing from the root
        drawNode(root, null, null, xOffset, yOffset);

        container.appendChild(svg);
    }

    // Render the initial visual tree
    renderVisualTree(hierarchyStructure);
</script>

@endsection
