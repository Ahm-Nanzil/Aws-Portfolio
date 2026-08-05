@extends('layouts.admin')

@section('page-title')
    {{__('Hierarchy Management')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('New')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        {{-- @can('create goal type') --}}


        {{-- @endcan --}}
    </div>
@endsection



@section('content')
<div class="container">

    <!-- Form to create new hierarchy -->
    <form method="POST" action="{{ route('hierarchy.create') }}" id="hierarchyForm">
        @csrf
        <div class="mb-3">
            <label for="hierarchy_name" class="form-label">Hierarchy Name:</label>
            <input type="text" class="form-control" id="hierarchy_name" name="name" required>
        </div>

        <!-- Form for building hierarchy -->
        <div class="mb-3">
            <h4>Add Node</h4>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="node_name" class="form-label">Node Name:</label>
                    <input type="text" class="form-control" id="node_name" placeholder="e.g., Sales Manager (EMEA)">
                </div>
                <div class="col-md-6">
                    <label for="parent_node" class="form-label">Parent Node (optional):</label>
                    <select class="form-control" id="parent_node">
                        <option value="">None</option>
                        <!-- JS will dynamically add available nodes here -->
                    </select>
                </div>
            </div>

            <button type="button" class="btn btn-primary" id="addNodeBtn">Add Node</button>
        </div>

        <!-- Container to display the current hierarchy structure -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mt-4">
                    <div class="card-header">
                        Current Hierarchy Structure
                    </div>
                    <div class="card-body">
                        <ul id="hierarchyList" class="list-unstyled">
                            <li>Start building your hierarchy...</li>
                        </ul>
                        <!-- Hidden input to store the hierarchy structure in JSON format -->
                        <input type="hidden" name="structure" id="hierarchyStructure">

                        <button type="submit" class="btn btn-success">Save Hierarchy</button>

                        <a href="{{ route('hierarchy_structure') }}" class="btn btn-secondary">Go Back</a>
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
                        <div id="visualTreeContainer" style="width: 100%; height: 50vh; border: 1px solid #ccc;">
                            <p class="text-muted">The visual hierarchy tree will appear here as you add nodes.</p>
                        </div>


                    </div>
                </div>
            </div>


        </div>





    </form>
</div>

<!-- Add JavaScript to dynamically add nodes and create hierarchy structure -->
<script>
    // Initialize an empty object for hierarchy structure
    let hierarchyStructure = { name: '', children: [] };
    let rootSet = false;  // Flag to check if the root node is set

    // Set the root name when the hierarchy name changes
    document.getElementById('hierarchy_name').addEventListener('input', function () {
        document.getElementById('hierarchyStructure').value = JSON.stringify(hierarchyStructure);
    });

    // Handle adding new nodes
    document.getElementById('addNodeBtn').addEventListener('click', function () {
        const nodeName = document.getElementById('node_name').value.trim();
        const parentNode = document.getElementById('parent_node').value;

        if (!nodeName) {
            alert("Please enter a node name");
            return;
        }

        if (!rootSet) {
            hierarchyStructure.name = nodeName;
            rootSet = true;
        } else {
            let parent = hierarchyStructure;
            if (parentNode) {
                parent = findNode(hierarchyStructure, parentNode);
                if (!parent) {
                    alert("Parent node not found!");
                    return;
                }
            }

            const newNode = { name: nodeName, children: [] };
            parent.children.push(newNode);
        }

        const hierarchyList = document.getElementById('hierarchyList');
        hierarchyList.innerHTML = '';
        renderHierarchyList(hierarchyStructure, hierarchyList);

        // Reset input fields
        document.getElementById('node_name').value = '';
        document.getElementById('parent_node').value = '';

        // Add the new node to the parent dropdown
        const option = document.createElement('option');
        option.value = nodeName;
        option.text = nodeName;
        document.getElementById('parent_node').appendChild(option);

        // Update hidden input with the new structure
        document.getElementById('hierarchyStructure').value = JSON.stringify(hierarchyStructure);

        // Update the visual tree
        renderVisualTree(hierarchyStructure);
    });

    // Recursive function to find a node by name
    function findNode(node, name) {
        if (node.name === name) {
            return node;
        }

        for (let i = 0; i < node.children.length; i++) {
            const result = findNode(node.children[i], name);
            if (result) return result;
        }

        return null;
    }

    // Recursive function to render the hierarchy as an unordered list
    function renderHierarchyList(node, parentElement) {
        const listItem = document.createElement('li');
        listItem.textContent = node.name;

        if (node.children.length > 0) {
            const subList = document.createElement('ul');
            node.children.forEach(child => {
                renderHierarchyList(child, subList);
            });
            listItem.appendChild(subList);
        }

        parentElement.appendChild(listItem);
    }

    // Render the initial structure as an empty hierarchy
    document.getElementById('hierarchyStructure').value = JSON.stringify(hierarchyStructure);

    // Function to render the visual hierarchy tree with connected lines
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
</script>
@endsection


