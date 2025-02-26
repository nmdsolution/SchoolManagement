@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>@lang('create_department')</h3>
            </div>
            <div class="card-body">
                @php
                    $buttons = [
                        ['label' => __('submit'), 'color' => 'primary']
                    ];
                @endphp
                <x-ls::form formview="vertical" id="department_form" :buttons="$buttons" action="{{ route('department.store') }}" method="post">
                    <x-ls::text name="name" id="name" :label="__('department_name')" />
                    <x-ls::select-model :options="$users->prepend(__('select_responsible'), null)" classes="select2" name="responsible_id" id="responsible_id" :label="__('responsible')"/>
                    <x-ls::select-model multiple :options="$subjects" classes="select2" name="subjects" id="subjects" :label="__('subjects')"/>
                </x-ls::form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
            <h4 class="card-title">
                            {{ __('list').' '.__('departments') }}
                        </h4>
                        @php
                            $url = route('department.list', [1]);
                            $columns = [
                                trans('no')=>['data-field'=>'no'],
                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                trans('name')=>['data-field'=>'name'],
                                trans('responsible')=>['data-field'=>'responsible'],
                                trans('subjects')=>['data-field'=>'subjects', 'data-formatter'=>'departmentSubjectsFormatter'],
                                trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                            ];
                            $actionColumn = [
                                'editButton'=>['url'=>url('department')],
                                'deleteButton'=>['url'=>url('department')],
                                'data-events'=>'departmentEvents'
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn id="table_list" toolbar="false"></x-bootstrap-table>
            </div>
        </div>
    </div>
</div>

<x-ls::modal></x-ls::modal>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#department_form').on('submit', function (e) {
            e.preventDefault();
            let url = route('department.store');
            let data = new FormData();
            data.append('name', $('#name').val());
            data.append('responsible_id', $('#responsible_id').val());
            data.append('subjects', $('#subjects').val());

            console.log(data);
            

            function successCallback(response) {
                showSuccessToast(response.message);
                $('#table_list').bootstrapTable('refresh');
                $('#department_form')[0].reset();
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: document.querySelector('input[name=_token]').value,
                    name: $('#name').val(),
                    responsible_id: $('#responsible_id').val(),
                    subjects: $('#subjects').val(),
                },
                success: successCallback,
                error: errorCallback
            });
        });
        
    });
</script>
@endsection