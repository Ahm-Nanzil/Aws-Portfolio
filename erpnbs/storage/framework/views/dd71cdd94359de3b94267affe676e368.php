
<?php $__env->startSection('page-title'); ?>
            <?php echo e(__('Manage Leads')); ?> <?php if($pipeline): ?> - <?php echo e($pipeline->name); ?> <?php endif; ?>


<?php $__env->stopSection(); ?>


<?php $__env->startPush('css-page'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/summernote/summernote-bs4.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/dragula.min.css')); ?>" id="main-style-link">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('css/summernote/summernote-bs4.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/dragula.min.js')); ?>"></script>
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
                        var pipeline_id = '<?php echo e($pipeline->id); ?>';

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);
                        $.ajax({
                            url: '<?php echo e(route('leads.order')); ?>',
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<div class="row align-items-center">

<div class="col">

    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Lead')); ?></li>
</div>
<div class="col-auto">
    <div class="dropdown" style="position: relative;">
        <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; z-index: 2;">
            Filter Leads
        </button>
        <div class="dropdown-menu p-3" aria-labelledby="filterDropdown" style="width: 300px; position: absolute; top: 100%; left: 0; z-index: 1;" onclick="event.stopPropagation();">

            <!-- Search by Saved Filters Checkbox -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="searchBySavedFilters">
                <label class="form-check-label" for="searchBySavedFilters">Search by Saved Filters</label>
            </div>

            <!-- Saved Filters Dropdown (initially hidden) -->
            <div class="mb-3" id="savedFiltersContainer" style="display: none;">
                <select id="savedFilters" class="form-select" name="savedFilter">
                    <option value="">Select a saved filter</option>
                    <?php $__currentLoopData = $allfilter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(json_encode($filter->criteria)); ?>" data-title="<?php echo e($filter->title); ?>">
                            <?php echo e($filter->title); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Filter Form (initially visible) -->
            <form action="<?php echo e(route('leads.filter')); ?>" method="POST" id="filterForm">
                <?php echo csrf_field(); ?>
                <!-- Hidden input for saved filter -->
                <input type="hidden" id="saved_filter" name="saved_filter" value="">
                <input type="hidden" name="pipeline_id" value="<?php echo e($pipeline->id); ?>">

                <!-- Text Search -->
                <div class="mb-3">
                    <label for="textSearch" class="form-label">Text Search</label>
                    <input type="text" class="form-control" id="textSearch" name="textSearch" placeholder="Search by keyword...">
                </div>

                <!-- Lead Status -->
                <div class="mb-3">
                    <div class="form-group">
                        <?php echo e(Form::label('stages', __('Stages'), ['class'=>'form-label'])); ?>

                        <?php echo e(Form::select(
                            'stages[]',
                            $stages,
                            null,
                            ['class' => 'form-control select2', 'multiple' => '', 'id' => 'stage-select']
                        )); ?>

                    </div>
                </div>

                <!-- Lead Source -->
                <div class="mb-3">
                    <div class="form-group">
                        <?php echo e(Form::label('source', __('Source'), ['class'=>'form-label'])); ?>

                        <?php echo e(Form::select(
                            'source[]',
                            $sources,
                            null,
                            ['class' => 'form-control select2', 'multiple' => '', 'id' => 'source-select']
                        )); ?>

                    </div>
                </div>

                <!-- Assigned To -->
                <div class="mb-3">
                    <div class="form-group">
                        <?php echo e(Form::label('users', __('Owner'), ['class'=>'form-label'])); ?>

                        <?php echo e(Form::select(
                            'users[]',
                            $users,
                            null,
                            ['class' => 'form-control select2', 'multiple' => '', 'id' => 'user-select']
                        )); ?>

                    </div>
                </div>

                <!-- Date Range -->
                <div class="mb-3">
                    <label for="dateRange" class="form-label">Date Range</label>
                    <div>
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="startDate">
                    </div>
                    <div class="mt-2">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="endDate">
                    </div>
                </div>


                <!-- Location -->
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Search by Location...">
                </div>

                <!-- Save Filter Checkbox -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="saveFilterCheckbox" name="saveFilter">
                    <label class="form-check-label" for="saveFilterCheckbox">Save this filter for future use</label>
                </div>

                <!-- Filter Name Input (hidden initially) -->
                <div class="mb-3" id="filterNameContainer" style="display: none;">
                    <label for="filterName" class="form-label">Filter Name</label>
                    <input type="text" class="form-control" id="filterName" name="filterName" placeholder="Enter a name for this filter">
                </div>

                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Prevent dropdown from closing when clicking inside it
            document.querySelector(".dropdown-menu").addEventListener("click", function (e) {
                e.stopPropagation();
            });

            // Toggle saved filter dropdown and hide form fields
            document.getElementById('searchBySavedFilters').addEventListener('change', function() {
                const savedFiltersContainer = document.getElementById('savedFiltersContainer');
                const filterForm = document.getElementById('filterForm');
                const isChecked = this.checked;

                savedFiltersContainer.style.display = isChecked ? 'block' : 'none';
                filterForm.style.display = isChecked ? 'none' : 'block';
            });

            // Hide or show filter name input based on save filter checkbox
            document.getElementById('saveFilterCheckbox').addEventListener('change', function() {
                const filterNameContainer = document.getElementById('filterNameContainer');
                filterNameContainer.style.display = this.checked ? 'block' : 'none';
            });

            // Submit form to route on selecting a saved filter
            document.getElementById('savedFilters').addEventListener('change', function () {
                const savedFilterValue = this.value;
                if (savedFilterValue) {
                    document.getElementById('saved_filter').value = savedFilterValue;
                    document.getElementById('filterForm').submit();
                }
            });
        });
    </script>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>


    <div class="float-end">
        <?php echo e(Form::open(['route' => 'leads.change.pipeline', 'id' => 'change-pipeline', 'class' => 'btn btn-sm'])); ?>

        <?php echo e(Form::select('default_pipeline_id', $pipelines, $pipeline->id, ['class' => 'form-control select me-4', 'id' => 'default_pipeline_id'])); ?>


        <?php if(isset($filters) && !empty($filters)): ?>
            <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_array($value)): ?>
                    <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e(Form::hidden("filters[$key][]", $item)); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <?php echo e(Form::hidden("filters[$key]", $value)); ?>

                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

    <?php echo e(Form::close()); ?>




        <a href="<?php echo e(route('leads.list')); ?>" data-size="lg" data-bs-toggle="tooltip" title="<?php echo e(__('List View')); ?>" class="btn btn-sm btn-primary">
            <i class="ti ti-list"></i>
        </a>
        <a href="#" data-size="md"  data-bs-toggle="tooltip" title="<?php echo e(__('Import')); ?>" data-url="<?php echo e(route('leads.file.import')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Import Lead CSV file')); ?>" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="<?php echo e(route('leads.export')); ?>" data-bs-toggle="tooltip" title="<?php echo e(__('Export')); ?>" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>
        <a href="#" data-size="lg" data-url="<?php echo e(route('leads.create')); ?>" data-ajax-popup="true" data-bs-toggle="tooltip" title="<?php echo e(__('Create New Lead')); ?>" data-title="<?php echo e(__('Create Lead')); ?>" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-sm-12">
            <?php
                $lead_stages = $pipeline->leadStages;
                $json = [];
                foreach ($lead_stages as $lead_stage){
                    $json[] = 'task-list-'.$lead_stage->id;
                }
            ?>
            <div class="row kanban-wrapper horizontal-scroll-cards" data-containers='<?php echo json_encode($json); ?>' data-plugin="dragula">
                <?php $__currentLoopData = $lead_stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead_stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                            $query = $lead_stage->leadshierarchy(); // This returns a query builder

                            // Apply filters conditionally if they exist
                            if (isset($filters)) {
                                // Apply text search filter
                                if (isset($filters['textSearch']) && $filters['textSearch']) {
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
                                if (isset($filters['stages']) && $filters['stages']) {
                                    $query->whereIn('stage_id', $filters['stages']);
                                }

                                // Apply source filter (multi-select)
                                if (isset($filters['source']) && $filters['source']) {
                                    $query->whereIn('sources', $filters['source']);
                                }

                                // Apply users filter (multi-select)
                                if (isset($filters['users']) && $filters['users']) {
                                    $query->whereIn('user_id', $filters['users']);
                                }

                                // Apply date range filter
                                if (isset($filters['startDate']) && isset($filters['endDate']) && $filters['startDate'] && $filters['endDate']) {
                                    $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']]);
                                } elseif (isset($filters['startDate']) && $filters['startDate']) {
                                    $query->whereDate('created_at', '>=', $filters['startDate']);
                                } elseif (isset($filters['endDate']) && $filters['endDate']) {
                                    $query->whereDate('created_at', '<=', $filters['endDate']);
                                }

                                // Apply location filter
                                if (isset($filters['location']) && is_array($filters['location'])) {
                                    $query->where(function($q) use ($filters) {
                                        if (!empty($filters['location']['company_location'])) {
                                            $q->where('company_location', 'like', '%' . $filters['location']['company_location'] . '%');
                                        }
                                        if (!empty($filters['location']['country'])) {
                                            $q->where('country', 'like', '%' . $filters['location']['country'] . '%');
                                        }
                                        if (!empty($filters['location']['region'])) {
                                            $q->where('region', 'like', '%' . $filters['location']['region'] . '%');
                                        }
                                        if (!empty($filters['location']['city'])) {
                                            $q->where('city', 'like', '%' . $filters['location']['city'] . '%');
                                        }
                                        if (!empty($filters['location']['address1'])) {
                                            $q->where('address1', 'like', '%' . $filters['location']['address1'] . '%');
                                        }
                                        if (!empty($filters['location']['address2'])) {
                                            $q->where('address2', 'like', '%' . $filters['location']['address2'] . '%');
                                        }
                                    });
                                }

                                // Get the filtered leads
                                $leads = $query->get(); // Execute the query and get the results
                            } else {
                                // If no filters, get all leads for the current stage
                                $leads = $query->get(); // Execute the query and get all leads
                            }

                            // dd($leads->toArray());  // Optionally uncomment for debugging
                        ?>




                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    <span class="btn btn-sm btn-primary btn-icon count">
                                        <?php echo e(count($leads)); ?>

                                    </span>
                                </div>
                                <h4 class="mb-0"><?php echo e($lead_stage->name); ?></h4>
                            </div>
                            <div class="card-body kanban-box" id="task-list-<?php echo e($lead_stage->id); ?>" data-id="<?php echo e($lead_stage->id); ?>">
                                <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="card" data-id="<?php echo e($lead->id); ?>">
                                        <div class="pt-3 ps-3">
                                            <?php ($labels = $lead->labels()); ?>
                                            <?php if($labels): ?>
                                                <?php $__currentLoopData = $labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="badge-xs badge bg-<?php echo e($label->color); ?> p-2 px-3 rounded"><?php echo e($label->name); ?></div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-header border-0 pb-0 position-relative">
                                            <h5><a href="<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view lead')): ?><?php if($lead->is_active): ?><?php echo e(route('leads.show',$lead->id)); ?><?php else: ?>#<?php endif; ?> <?php else: ?>#<?php endif; ?>"><?php echo e($lead->name); ?></a></h5>
                                            <div class="card-header-right">
                                                <?php if(Auth::user()->type != 'Client'): ?>
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit lead')): ?>
                                                                <a href="#!" data-size="md" data-url="<?php echo e(URL::to('leads/'.$lead->id.'/labels')); ?>" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="<?php echo e(__('Labels')); ?>">
                                                                    <i class="ti ti-bookmark"></i>
                                                                    <span><?php echo e(__('Labels')); ?></span>
                                                                </a>

                                                                <a href="#!" data-size="lg" data-url="<?php echo e(URL::to('leads/'.$lead->id.'/edit')); ?>" data-ajax-popup="true" class="dropdown-item" data-bs-original-title="<?php echo e(__('Edit Lead')); ?>">
                                                                    <i class="ti ti-pencil"></i>
                                                                    <span><?php echo e(__('Edit')); ?></span>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete lead')): ?>
                                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]); ?>

                                                                <a href="#!" class="dropdown-item bs-pass-para">
                                                                    <i class="ti ti-archive"></i>
                                                                    <span> <?php echo e(__('Delete')); ?> </span>
                                                                </a>
                                                                <?php echo Form::close(); ?>

                                                            <?php endif; ?>


                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php
                                        $products = $lead->products();
                                        $sources = $lead->sources();
                                        ?>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <ul class="list-inline mb-0">

                                                    <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="<?php echo e(__('Product')); ?>">
                                                        <i class="f-16 text-primary ti ti-shopping-cart"></i> <?php echo e(count($products)); ?>

                                                    </li>

                                                    <li class="list-inline-item d-inline-flex align-items-center" data-bs-toggle="tooltip" title="<?php echo e(__('Source')); ?>">
                                                        <i class="f-16 text-primary ti ti-social"></i><?php echo e(count($sources)); ?>

                                                    </li>
                                                </ul>
                                                <div class="user-group">
                                                    <?php $__currentLoopData = $lead->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <img src="<?php if($user->avatar): ?> <?php echo e(asset('/storage/uploads/avatar/'.$user->avatar)); ?> <?php else: ?> <?php echo e(asset('storage/uploads/avatar/avatar.png')); ?> <?php endif; ?>" alt="image" data-bs-toggle="tooltip" title="<?php echo e($user->name); ?>">
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Portfolio\Azure-VM\erpnbs\resources\views/leads/index.blade.php ENDPATH**/ ?>