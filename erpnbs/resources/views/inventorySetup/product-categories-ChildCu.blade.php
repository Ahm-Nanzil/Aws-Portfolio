@if(isset($childCategory))
    {{ Form::model($childCategory, ['route' => ['inventory.product-categories.child.update', $childCategory->id], 'method' => 'PUT', 'class' => 'modalForm']) }}
@else
    {{ Form::open(['route' => 'inventory.product-categories.child.store', 'class' => 'modalForm']) }}
@endif

<style>
    .table-excel .form-group { margin: 0; }
    .table-excel .form-control {
        border: none;
        border-radius: 0;
        padding: 5px 8px;
        height: 35px;
    }
    .table-excel td { padding: 0 !important; }
    .table-excel thead th {
        background-color: #f8f9fa;
        padding: 8px !important;
    }
    .table-excel .form-check-input {
        margin: 0;
        height: 35px;
    }
</style>

<div class="modal-body">
    <div class="table-responsive">
        <table class="table table-bordered table-excel">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Child Category Code') }}</th>
                    <th>{{ __('Child Category Name') }}</th>
                    <th>{{ __('Main Category Name') }}</th>
                    <th>{{ __('Sub Category Name') }}</th>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Display') }}</th>
                    <th>{{ __('Note') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($childCategory))
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td>{{ Form::text("items[0][code]", $childCategory->code, ['class' => 'form-control']) }}</td>
                        <td>{{ Form::text("items[0][name]", $childCategory->name, ['class' => 'form-control']) }}</td>
                        <td>{{ Form::select("items[0][main_category_id]", $mainCategories, $childCategory->main_category_id, ['class' => 'form-control main-category-select', 'placeholder' => __(''), 'data-index' => 0]) }}</td>
                        <td>{{ Form::select("items[0][sub_category_id]", $subCategories->where('main_category_id', $childCategory->main_category_id)->pluck('name', 'id'), $childCategory->sub_category_id, ['class' => 'form-control sub-category-select', 'placeholder' => __(''), 'data-index' => 0]) }}</td>
                        <td>{{ Form::number("items[0][order]", $childCategory->order, ['class' => 'form-control', 'min' => 1]) }}</td>
                        <td class="text-center">{{ Form::checkbox("items[0][display]", 1, $childCategory->display, ['class' => 'form-check-input']) }}</td>
                        <td>{{ Form::text("items[0][note]", $childCategory->note, ['class' => 'form-control']) }}</td>
                    </tr>
                @else
                    @for($i = 0; $i < 9; $i++)
                        <tr>
                            <td class="text-center align-middle">{{ $i + 1 }}</td>
                            <td>{{ Form::text("items[$i][code]", '', ['class' => 'form-control']) }}</td>
                            <td>{{ Form::text("items[$i][name]", '', ['class' => 'form-control']) }}</td>
                            <td>{{ Form::select("items[$i][main_category_id]", $mainCategories, null, ['class' => 'form-control main-category-select', 'placeholder' => __(''), 'data-index' => $i]) }}</td>
                            <td>{{ Form::select("items[$i][sub_category_id]", [], null, ['class' => 'form-control sub-category-select', 'placeholder' => __(''), 'data-index' => $i]) }}</td>
                            <td>{{ Form::number("items[$i][order]", '', ['class' => 'form-control', 'min' => 1]) }}</td>
                            <td class="text-center">{{ Form::checkbox("items[$i][display]", 1, true, ['class' => 'form-check-input']) }}</td>
                            <td>{{ Form::text("items[$i][note]", '', ['class' => 'form-control']) }}</td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Close') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ isset($childCategory) ? __('Update') : __('Save') }}" class="btn btn-primary">
</div>

{{ Form::close() }}

<script>
    const subCategories = @json($subCategories);

    $(document).on('change', '.main-category-select', function () {
        const mainCategoryId = $(this).val();
        const rowIndex = $(this).data('index');
        const subCategoryDropdown = $(`.sub-category-select[data-index="${rowIndex}"]`);

        const filteredSubCategories = subCategories.filter(sub => sub.main_category_id == mainCategoryId);

        subCategoryDropdown.empty();
        subCategoryDropdown.append('<option value="">{{ __("") }}</option>');

        filteredSubCategories.forEach(sub => {
            subCategoryDropdown.append(`<option value="${sub.id}">${sub.name}</option>`);
        });
    });
</script>
<script>
    $(document).on('submit', '.modalForm', function (e) {
    let isValid = true;

    $('.table-excel tbody tr').each(function () {
        const code = $(this).find('input[name^="items"][name$="[code]"]').val().trim();
        const name = $(this).find('input[name^="items"][name$="[name]"]').val().trim();
        const mainCategory = $(this).find('select[name^="items"][name$="[main_category_id]"]').val();
        const subCategory = $(this).find('select[name^="items"][name$="[sub_category_id]"]').val();

        // If either code or name is filled, require main category and subcategory
        if (code || name) {
            if (!mainCategory) {
                isValid = false;
                show_toastr('errror', 'Main Category is required for non-empty rows.');
                return false; // Exit loop
            }
            if (!subCategory) {
                isValid = false;
                show_toastr('errror', 'Sub Category is required for non-empty rows.');
                return false; // Exit loop
            }
        }
    });

    // Prevent form submission if validation fails
    if (!isValid) {
        e.preventDefault();
    }
});

</script>
