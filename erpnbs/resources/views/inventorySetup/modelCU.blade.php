@if(isset($model))
    {{ Form::model($model, ['route' => ['inventory.model.update', $model->id], 'method' => 'PUT', 'class' => 'modalForm']) }}
@else
    {{ Form::open(['route' => 'inventory.model.store', 'class' => 'modalForm']) }}
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
                    <th width="25%">{{ __('Model code') }}</th>
                    <th width="30%">{{ __('Model name') }}</th>
                    <th width="20%">{{ __('Order') }}</th>
                    <th width="10%">{{ __('Display') }}</th>
                    <th width="10%">{{ __('Note') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($model))
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td>
                            {{ Form::text("items[0][code]", $model->code ?? '', [
                                'class' => 'form-control',
                            ]) }}
                        </td>
                        <td>
                            {{ Form::text("items[0][name]", $model->name ?? '', [
                                'class' => 'form-control',
                            ]) }}
                        </td>
                        <td>
                            {{ Form::number("items[0][order]", $model->order ?? '', [
                                'class' => 'form-control',
                                'min' => '1',
                            ]) }}
                        </td>
                        <td class="text-center">
                            {{ Form::checkbox("items[0][display]", 1, $model->display ?? true, [
                                'class' => 'form-check-input',
                            ]) }}
                        </td>
                        <td>
                            {{ Form::text("items[0][note]", $model->note ?? '', [
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
    <input type="submit" value="{{ isset($model) ? __('Update') : __('Save') }}" class="btn btn-primary">
</div>

{{ Form::close() }}
