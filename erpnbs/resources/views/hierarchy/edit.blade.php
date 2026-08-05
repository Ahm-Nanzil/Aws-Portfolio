
@extends('layouts.admin')

@section('page-title')
    {{__('Hierarchy Management')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Edit')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        {{-- @can('create goal type') --}}


        {{-- @endcan --}}
    </div>
@endsection







@section('content')
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container">

    <!-- Form to edit the hierarchy -->
    <form method="POST" action="{{ route('hierarchy.edit', $hierarchy->id) }}" id="hierarchyForm">
        @csrf

        <div class="mb-3">
            <label for="hierarchy_name" class="form-label">Hierarchy Name:</label>
            <input type="text" class="form-control" id="hierarchy_name" name="name" value="{{ $hierarchy->name }}" required>
        </div>

        <!-- Form for adding a new node -->
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
                    </select>
                </div>
            </div>
            <button type="button" class="btn btn-primary" id="addNodeBtn">Add Node</button>
        </div>

        <!-- Hierarchy Structure Display -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mt-4">
                    <div class="card-header">
                        Current Hierarchy Structure                    </div>
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
                            <p class="text-muted">The visual hierarchy tree will appear here as you edit or add nodes.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <input type="hidden" name="structure_old" id="" value="{{ $hierarchy }}">

        <input type="hidden" name="structure" id="hierarchyStructure" value="{{ $hierarchy->structure }}">

        <button type="submit" class="btn btn-success">Save Hierarchy</button>
        <a href="{{ route('hierarchy_structure') }}" class="btn btn-secondary">Go Back</a>

    </form>
</div>

<!-- Modal for editing node name -->
<div class="modal fade" id="editNodeModal" tabindex="-1" aria-labelledby="editNodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNodeModalLabel">Edit Node</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="edit_node_name" class="form-label">New Node Name:</label>
                <input type="text" class="form-control" id="edit_node_name" placeholder="New Node Name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveNodeChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for confirming node deletion -->
<div class="modal fade" id="deleteNodeModal" tabindex="-1" aria-labelledby="deleteNodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteNodeModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this node? Its children will be moved to the parent node.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteNode">Delete</button>
            </div>
        </div>
    </div>
</div>
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
    background: #ccc; /* Line color */
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
    let rootSet = hierarchyStructure.name !== '';
    let editNode = null;
    let deleteNodeTarget = null; // To store the node to be deleted

    function populateParentNodeDropdown(node, parentNodeSelect) {
        if (node.name !== '') {
            const option = document.createElement('option');
            option.value = node.name;
            option.text = node.name;
            parentNodeSelect.appendChild(option);
        }
        node.children.forEach(child => populateParentNodeDropdown(child, parentNodeSelect));
    }

    function updateParentNodeDropdown() {
        const parentNodeSelect = document.getElementById('parent_node');
        parentNodeSelect.innerHTML = '<option value="">None</option>'; // Clear existing options
        populateParentNodeDropdown(hierarchyStructure, parentNodeSelect); // Repopulate dropdown
    }

    populateParentNodeDropdown(hierarchyStructure, document.getElementById('parent_node'));

    document.getElementById('addNodeBtn').addEventListener('click', function () {
        const nodeName = document.getElementById('node_name').value.trim();
        const parentNode = document.getElementById('parent_node').value;

        if (!nodeName) {
            show_toastr('error', 'Please enter a node name');
            return; // Exit the function
        }

        if (!rootSet) {
            hierarchyStructure.name = nodeName;
            rootSet = true;
        } else {
            let parent = hierarchyStructure;
            if (parentNode) {
                parent = findNode(hierarchyStructure, parentNode);
                if (!parent) {
                    show_toastr('error', 'Parent node not found!');
                    return;
                }
            }
            const newNode = { name: nodeName, children: [] };
            parent.children.push(newNode);
        }

        updateHierarchyDisplay();
        updateParentNodeDropdown(); // Update parent node dropdown after adding a node
        document.getElementById('node_name').value = '';
        document.getElementById('parent_node').value = '';

        document.getElementById('hierarchyStructure').value = JSON.stringify(hierarchyStructure);

        // Update the visual tree
        renderVisualTree(hierarchyStructure);
    });

    function findNode(node, name) {
        if (node.name === name) return node;
        for (let i = 0; i < node.children.length; i++) {
            const result = findNode(node.children[i], name);
            if (result) return result;
        }
        return null;
    }

    function renderHierarchyList(node, parentElement) {
    const listItem = document.createElement('li');
    const nodeWrapper = document.createElement('div');
    nodeWrapper.classList.add('node-wrapper');

    // Use a span for the expand/collapse icon
    const expandCollapseIcon = document.createElement('span');
    expandCollapseIcon.textContent = node.children.length > 0 ? ' + ' : ''; // Show + only if there are children
    expandCollapseIcon.classList.add('expand-collapse-icon');

    // Create the node name and buttons
    nodeWrapper.innerHTML = `
        <span class="node-name">${node.name}</span>
        ________________________________<button class="btn btn-sm btn-info editNodeBtn">𝓮</button>
        <button class="btn btn-sm btn-danger deleteNodeBtn">-</button>
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

    // Buttons for editing and deleting nodes
    nodeWrapper.querySelector('.editNodeBtn').addEventListener('click', (event) => {
        event.preventDefault();
        editNode = node;
        document.getElementById('edit_node_name').value = node.name;
        const modal = new bootstrap.Modal(document.getElementById('editNodeModal'));
        modal.show();
    });

    nodeWrapper.querySelector('.deleteNodeBtn').addEventListener('click', (event) => {
        event.preventDefault();
        deleteNodeTarget = node;
        const modal = new bootstrap.Modal(document.getElementById('deleteNodeModal'));
        modal.show();
    });

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

    document.getElementById('saveNodeChanges').addEventListener('click', () => {
        if (editNode) {
            editNode.name = document.getElementById('edit_node_name').value.trim();
            updateHierarchyDisplay();
            updateParentNodeDropdown(); // Update parent node dropdown after editing a node
            document.getElementById('hierarchyStructure').value = JSON.stringify(hierarchyStructure);
            const modal = bootstrap.Modal.getInstance(document.getElementById('editNodeModal'));
            modal.hide();

            // Update the visual tree
            renderVisualTree(hierarchyStructure);
        }
    });

    document.getElementById('confirmDeleteNode').addEventListener('click', () => {
        if (deleteNodeTarget) {
            deleteNode(deleteNodeTarget);
            updateParentNodeDropdown(); // Update parent node dropdown after deleting a node
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteNodeModal'));
            modal.hide();

            // Update the visual tree
            renderVisualTree(hierarchyStructure);
        }
    });

    function deleteNode(node) {
        const parentNode = findParentNode(hierarchyStructure, node);
        if (!parentNode) {
            show_toastr('error', 'Cannot delete the root node.');
            return;
        }

        const nodeIndex = parentNode.children.indexOf(node);
        if (nodeIndex > -1) {
            const childNodes = node.children;
            parentNode.children.splice(nodeIndex, 1);
            childNodes.forEach(child => parentNode.children.push(child));
            updateHierarchyDisplay();
            document.getElementById('hierarchyStructure').value = JSON.stringify(hierarchyStructure);
        }
    }

    function findParentNode(node, childNode) {
        if (node.children.includes(childNode)) {
            return node;
        }
        for (let i = 0; i < node.children.length; i++) {
            const result = findParentNode(node.children[i], childNode);
            if (result) return result;
        }
        return null;
    }

    // Initial population of the hierarchy display and dropdown
    updateHierarchyDisplay();
    updateParentNodeDropdown();

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









