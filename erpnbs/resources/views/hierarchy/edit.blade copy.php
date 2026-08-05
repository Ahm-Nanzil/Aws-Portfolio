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
        @can('create goal type')
            <a href="#" data-url="{{ route('goaltype.create') }}" data-ajax-popup="true" data-title="{{__('Create New Goal Type')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>

        @endcan
    </div>
@endsection






@section('content')
<div class="container">
    <!-- Form to edit the hierarchy -->
    <form method="POST" action="{{ route('hierarchy.edit', $hierarchy->id) }}" id="hierarchyForm">
        @csrf

        <div class="mb-3">
            <label for="hierarchy_name" class="form-label">Hierarchy Name:</label>
            <input type="text" class="form-control" id="hierarchy_name" name="name" value="{{ $hierarchy->name }}" required>
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

        <h4>Visual Hierarchy Tree</h4>
        <div id="treeContainer" style="border: 1px solid #ddd; padding: 10px; width: 100%; height: 500px;"></div>

        <!-- Hidden input to store the hierarchy structure in JSON format -->
        <input type="hidden" name="structure" id="hierarchyStructuretree" value="{{ $hierarchy->structure }}">

        <button type="submit" class="btn btn-success mt-3">Save Hierarchy</button>
        <a href="{{ route('hierarchy_structure') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>

<script src="https://d3js.org/d3.v6.min.js"></script>

<script>
// Parse existing hierarchy structure from JSON
let hierarchyStructuretree = JSON.parse(document.getElementById('hierarchyStructuretree').value);
let rootSettree = hierarchyStructuretree.name !== ''; // Check if the root node is set

// Define D3.js tree diagram
const width = document.getElementById('treeContainer').clientWidth;
const height = 500;

const svg = d3.select("#treeContainer")
    .append("svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
    .attr("transform", "translate(50,50)");

// Create tree layout
const treeLayout = d3.tree().size([width - 100, height - 100]);

// Function to render tree from data
function renderTree(data) {
    const root = d3.hierarchy(data);

    // Generate tree layout
    treeLayout(root);

    // Add links (lines connecting nodes)
    const links = svg.selectAll(".link")
        .data(root.links())
        .enter()
        .append("path")
        .attr("class", "link")
        .attr("d", d3.linkVertical()
            .x(d => d.x)
            .y(d => d.y))
        .style("fill", "none")
        .style("stroke", "#ccc");

    // Add nodes (circles and text labels)
    const nodes = svg.selectAll(".node")
        .data(root.descendants())
        .enter()
        .append("g")
        .attr("class", "node")
        .attr("transform", d => `translate(${d.x},${d.y})`)
        .call(d3.drag() // Add drag functionality
            .on("start", dragStarted)
            .on("drag", dragged)
            .on("end", dragEnded));

    nodes.append("circle")
        .attr("r", 10)
        .style("fill", "#fff")
        .style("stroke", "#69b3a2");

    nodes.append("text")
        .attr("dy", 4)
        .attr("x", d => d.children ? -15 : 15)
        .style("text-anchor", d => d.children ? "end" : "start")
        .text(d => d.data.name);
}

// Drag events
function dragStarted(event, d) {
    d3.select(this).raise().attr("stroke", "black");
}

function dragged(event, d) {
    d.x = event.x;
    d.y = event.y;
    d3.select(this).attr("transform", `translate(${d.x},${d.y})`);

    // Update links to reflect dragged position
    svg.selectAll(".link")
        .attr("d", d3.linkVertical()
            .x(d => d.x)
            .y(d => d.y));
}

function dragEnded(event, d) {
    d3.select(this).attr("stroke", null);
}

// Initial render of the tree
renderTree(hierarchyStructuretree);

// Add new node functionality
document.getElementById('addNodeBtn').addEventListener('click', function () {
    const nodeName = document.getElementById('node_name').value.trim();
    const parentNode = document.getElementById('parent_node').value;

    if (!nodeName) {
        alert("Please enter a node name");
        return;
    }

    if (!rootSettree) {
        hierarchyStructuretree.name = nodeName;
        rootSettree = true;
    } else {
        let parent = hierarchyStructuretree;
        if (parentNode) {
            parent = findNode(hierarchyStructuretree, parentNode);
            if (!parent) {
                alert("Parent node not found!");
                return;
            }
        }
        const newNode = { name: nodeName, children: [] };
        parent.children.push(newNode);
    }

    // Clear and re-render tree with updated structure
    svg.selectAll("*").remove();
    renderTree(hierarchyStructuretree);

    // Reset inputs
    document.getElementById('node_name').value = '';
    document.getElementById('parent_node').value = '';

    // Update hidden input field
    document.getElementById('hierarchyStructuretree').value = JSON.stringify(hierarchyStructuretree);
});

// Recursive function to find node
function findNode(node, name) {
    if (node.name === name) return node;
    if (node.children) {
        for (const child of node.children) {
            const result = findNode(child, name);
            if (result) return result;
        }
    }
    return null;
}
</script>
@endsection







