@if(isset($subCategory))
    {{ Form::model($subCategory, ['route' => ['inventory.product-categories.sub.update', $subCategory->id], 'method' => 'PUT', 'class' => 'modalForm']) }}
@else
    {{ Form::open(['route' => 'inventory.product-categories.sub.store', 'class' => 'modalForm']) }}
@endif

<style>
    .table-excel .form-group {
        margin: 0;
    }
    .table-excel .form-control {
        border: none;
        border-radius: 0;
        padding: 5px 8px;
        height: 35px;
    }
    .table-excel td {
        padding: 0 !important;
    }
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
                    <th width="5%">#</th>
                    <th width="20%">{{ __('Sub Category Code') }}</th>
                    <th width="20%">{{ __('Sub Category Name') }}</th>
                    <th width="20%">{{ __('Main Category') }}</th>
                    <th width="10%">{{ __('Order') }}</th>
                    <th width="10%">{{ __('Display') }}</th>
                    <th width="15%">{{ __('Note') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($subCategory))
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td>
                            {{ Form::text("items[0][code]", $subCategory->code ?? '', [
                                'class' => 'form-control',
                            ]) }}
                        </td>
                        <td>
                            {{ Form::text("items[0][name]", $subCategory->name ?? '', [
                                'class' => 'form-control',
                            ]) }}
                        </td>
                        <td>
                            {{ Form::select("items[0][main_category_id]", $mainCategories, $subCategory->main_category_id ?? null, [
                                'class' => 'form-control',
                                'placeholder' => __(''),
                            ]) }}
                        </td>
                        <td>
                            {{ Form::number("items[0][order]", $subCategory->order ?? '', [
                                'class' => 'form-control',
                                'min' => '1',
                            ]) }}
                        </td>
                        <td class="text-center">
                            {{ Form::checkbox("items[0][display]", 1, $subCategory->display ?? true, [
                                'class' => 'form-check-input',
                            ]) }}
                        </td>
                        <td>
                            {{ Form::text("items[0][note]", $subCategory->note ?? '', [
                                'class' => 'form-control',
                            ]) }}
                        </td>
                    </tr>
                @else
                    @for($i = 0; $i < 9; $i++)
                        <tr>
                            <td class="text-center align-middle">{{ $i + 1 }}</td>
                            <td>
                                {{ Form::text("items[$i][code]", '', [
                                    'class' => 'form-control',
                                ]) }}
                            </td>
                            <td>
                                {{ Form::text("items[$i][name]", '', [
                                    'class' => 'form-control',
                                ]) }}
                            </td>
                            <td>
                                {{ Form::select("items[$i][main_category_id]", $mainCategories, null, [
                                    'class' => 'form-control',
                                    'placeholder' => __(''),
                                ]) }}
                            </td>
                            <td>
                                {{ Form::number("items[$i][order]", '', [
                                    'class' => 'form-control',
                                    'min' => '1',
                                ]) }}
                            </td>
                            <td class="text-center">
                                {{ Form::checkbox("items[$i][display]", 1, true, [
                                    'class' => 'form-check-input',
                                ]) }}
                            </td>
                            <td>
                                {{ Form::text("items[$i][note]", '', [
                                    'class' => 'form-control',
                                ]) }}
                            </td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Close') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ isset($subCategory) ? __('Update') : __('Save') }}" class="btn btn-primary">
</div>

{{ Form::close() }}
<script>
    $(document).on('submit', '.modalForm', function (e) {
    let isValid = true;

    $('.table-excel tbody tr').each(function () {
        const code = $(this).find('input[name^="items"][name$="[code]"]').val().trim();
        const name = $(this).find('input[name^="items"][name$="[name]"]').val().trim();
        const mainCategory = $(this).find('select[name^="items"][name$="[main_category_id]"]').val();

        // If either code or name is filled, require main category and subcategory
        if (code || name) {
            if (!mainCategory) {
                isValid = false;
                show_toastr('errror', 'Main Category is required for non-empty rows.');
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
