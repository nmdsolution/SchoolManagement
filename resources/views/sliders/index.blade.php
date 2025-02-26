@extends('layout.master')

@section('title')
    {{ __('sliders') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('sliders') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('sliders') }}
                        </h4>
                        <div class="col-6">
                            <form class="pt-3 sliders-create-form" id="create-form" action="{{ route('sliders.store') }}" method="POST" data-success-function="successFunction" novalidate="novalidate" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4">
                                        <div class="form-group files">
                                            <label>{{ __('image') }} <span class="text-danger">*</span></label>
                                            <input name="image" type="file" required class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8">
                                        <div class="form-group4">
                                            <label>{{ __('URL') }}</label>
                                            <input type="text" name="url" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('Centers') }}<span class="text-danger">*</span></label>
                                    <br>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="select-all-centers"/>Select All
                                        </label>
                                    </div>
                                    <select name="center_id[]" id="center-id" class="form-control select" multiple required>
                                        @foreach ($centers as $row)
                                            <option value="{{$row['id']}}">{{$row['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('Roles') }}<span class="text-danger">*</span></label>
                                    <br>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="select-all-roles"/>Select All
                                        </label>
                                    </div>
                                    <select name="role_id[]" id="role-id" class="form-control select" multiple required>
                                        @foreach ($roles as $row)
                                            <option value="{{$row['id']}}">{{$row['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input class="btn btn-primary" id="create-btn" type="submit" value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('sliders') }}
                        </h4>

                        @php
                            $url = route('sliders.show', [1]);
                            $columns = [
                                trans('no')=>['data-field'=>'no'],
                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                trans('image')=>['data-field'=>'image','data-formatter'=>'imageFormatter'],
                                trans('URL')=>['data-field'=>'url','data-formatter'=>'urlFormatter'],
                                trans('Centers')=>['data-field'=>'centers','data-formatter'=>'sliderFormatter'],
                                trans('Roles')=>['data-field'=>'roles','data-formatter'=>'sliderFormatter'],
                                trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                            ];
                            $actionColumn = [
                                'editButton'=>['url'=>url('sliders')],
                                'deleteButton'=>['url'=>url('sliders')],
                                'data-events'=>'sliderEvents'
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('sliders') }}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 sliders-edit-form" id="edit-form" action="{{ url('sliders') }}" novalidate="novalidate">
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" id="edit-id" value=""/>

                                <div class="form-group">
                                    <div class="col-12 col-sm-12 col-md-12">
                                        <div class="form-group files">
                                            <label>{{ __('image') }}</label>
                                            <input name="image" type="file" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 mb-3 ">
                                        <div class="form-group4">
                                            <label>{{ __('URL') }}</label>
                                            <input type="text" name="url" id="edit_url" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('Centers') }}<span class="text-danger">*</span></label>
                                        <select name="center_id[]" id="edit-center-id" class="form-control select" multiple required>
                                            @foreach ($centers as $row)
                                                <option value="{{$row['id']}}">{{$row['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('Roles') }}<span class="text-danger">*</span></label>
                                        <select name="role_id[]" id="edit-role-id" class="form-control select" multiple required>
                                            @foreach ($roles as $row)
                                                <option value="{{$row['id']}}">{{$row['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('close')}}</button>
                                <input class="btn btn-primary" type="submit" value={{ __('edit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function successFunction(response) {
            if (!response.error) {
                $('#center-id').val('').trigger('change');
                $('#role-id').val('').trigger('change');
            }
        }

        $('#edit-center-id').on("select2:unselecting", function (e) {
            $('#edit-form').append("<input type='hidden' name='delete_center_id[]' value='" + e.params.args.data.id + "'/>");
        }).on("select2:selecting", function (e) {
            $('input[name="delete_center_id[]"][value="' + e.params.args.data.id + '"]').remove();
        }).trigger('change');

        $('#edit-role-id').on("select2:unselecting", function (e) {
            $('#edit-form').append("<input type='hidden' name='delete_role_id[]' value='" + e.params.args.data.id + "'/>");
        }).on("select2:selecting", function (e) {
            $('input[name="delete_role_id[]"][value="' + e.params.args.data.id + '"]').remove();
        }).trigger('change');

        $('#edit-center-id,#edit-role-id').select2({
            dropdownParent: $('#editModal')
        });
    </script>
@endsection