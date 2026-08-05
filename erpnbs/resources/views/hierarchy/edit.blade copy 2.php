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
            <a href="{{ route('hierarchy.create') }}"  title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>

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
        <h4>Current Hierarchy Structure</h4>
        <ul id="hierarchyList" class="list-unstyled"></ul>

        <input type="hidden" name="structure" id="hierarchyStructure" value="{{ $hierarchy->structure }}">

        <button type="submit" class="btn btn-success">Save Hierarchy</button>
        <a href="{{ route('hierarchy_structure') }}" class="btn btn-secondary">Cancel</a>

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
            return; // Exit the function            return;
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
        nodeWrapper.innerHTML = `
            ${node.name}
            <button class="btn btn-sm btn-info editNodeBtn">𝓮</button>
            <button class="btn btn-sm btn-danger deleteNodeBtn">-</button>
        `;

        listItem.appendChild(nodeWrapper);
        nodeWrapper.querySelector('.editNodeBtn').addEventListener('click', (event) => {
            event.preventDefault(); // Prevent form submission
            editNode = node;
            document.getElementById('edit_node_name').value = node.name;
            const modal = new bootstrap.Modal(document.getElementById('editNodeModal'));
            modal.show();
        });

        nodeWrapper.querySelector('.deleteNodeBtn').addEventListener('click', (event) => {
            event.preventDefault(); // Prevent form submission
            deleteNodeTarget = node; // Set the node to be deleted
            const modal = new bootstrap.Modal(document.getElementById('deleteNodeModal'));
            modal.show();
        });

        if (node.children.length > 0) {
            const subList = document.createElement('ul');
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
        }
    });

    document.getElementById('confirmDeleteNode').addEventListener('click', () => {
        if (deleteNodeTarget) {
            deleteNode(deleteNodeTarget);
            updateParentNodeDropdown(); // Update parent node dropdown after deleting a node
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteNodeModal'));
            modal.hide();
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
</script>
@endsection








