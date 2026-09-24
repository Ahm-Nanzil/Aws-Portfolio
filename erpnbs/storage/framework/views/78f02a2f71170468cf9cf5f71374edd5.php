

<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Hierarchy Management')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Structure')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>
    <div class="float-end">
        
        <?php if(!$hasIncompleteHierarchy): ?>
        <a href="<?php echo e(route('hierarchy.create')); ?>" title="<?php echo e(__('Create')); ?>" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
        <?php endif; ?>


        
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>


<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<?php if($hierarchies->isEmpty()): ?>
    <p>No hierarchies found.</p>
<?php else: ?>
<?php $__currentLoopData = $hierarchies->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hierarchy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <!-- Using $index for unique ID -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">

        <h3><?php echo e($hierarchy->name); ?></h3>
        

        <div>
            <a href="<?php echo e(route('hierarchy.previous', $hierarchy->id)); ?>" class="btn btn-secondary btn-sm">History</a>
            <a href="<?php echo e(route('hierarchy.edit', $hierarchy->id)); ?>" class="btn btn-warning btn-sm">Edit</a>
            <div class="action-btn bg-danger ms-2">
                <?php echo Form::open(['method' => 'DELETE', 'route' => ['hierarchy.destroy', $hierarchy->id],'id'=>'delete-form-'.$hierarchy->id]); ?>

                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="<?php echo e(__('Delete')); ?>"><i class="ti ti-trash text-white"></i></a>
                <?php echo Form::close(); ?>

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
        <div id="hierarchy-chart-<?php echo e($index); ?>" style="width: 100%; height: 100%;"></div> <!-- Unique ID for each chart -->

        <!-- Include D3.js from CDN -->
        <script src="https://d3js.org/d3.v6.min.js"></script>

        <script>
            // Define the hierarchy data in JSON format
            var data = <?php echo json_encode($hierarchy->structure); ?>;

            // Set dimensions for the SVG element
            var width = document.getElementById('hierarchy-chart-<?php echo e($index); ?>').clientWidth, height = 400;

            // Create an SVG container
            var svg = d3.select("#hierarchy-chart-<?php echo e($index); ?>").append("svg") // Use unique ID
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
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Portfolio\Azure-VM\erpnbs\resources\views/hierarchy/index.blade.php ENDPATH**/ ?>