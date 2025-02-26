@extends('layout.master')

@section('title')
    {{ __('Form Fields') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage').' '.__('Form Fields') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="card-title">
                                    {{ __('create').' '.__('Form Fields') }}
                                </h4>
                            </div>
                            <div class="col-6 text-end">
                                <a href="{{route('form-fields.import.index')}}">{{ __('Import Admin Form Fields') }}</a>
                            </div>
                        </div>
                        <form class="create-form pt-3" id="formdata" method="POST" novalidate="novalidate" action="{{route('form-fields.store')}}">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-5 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control','onkeypress' => 'return ((event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 8 || event.charCode == 32 || (event.charCode >= 48 && event.charCode <= 57));']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-5 local-forms">
                                    <label>{{ __('Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="text">{{ __('Text') }}</option>
                                        <option value="number">{{ __('Numeric Values') }}</option>
                                        <option value="dropdown">{{ __('Dropdown') }}</option>
                                        <option value="radio">{{ __('Radio Button') }}</option>
                                        <option value="checkbox">{{ __('Checkbox') }}</option>
                                        <option value="textarea">{{ __('Textarea') }}</option>
                                        <option value="file">{{ __('File Upload') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-2">
                                    <label>{{ __('Required') }} <span class="text-danger">*</span></label>
                                    <input type="checkbox" name="is_required" class="js-switch" value="1"/>
                                </div>

                                <div id="default-values-div" style="display: none;">
                                    <div class="form-group col-sm-12 col-md-3">
                                        <button type="button" class="add-more-default-values btn btn-outline-primary p-2 px-3">{{ __('Add Default Values') }} <span class="fa fa-plus"></span></button>
                                    </div>
                                    <div id="add-default-values">
                                        <div class="row">
                                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                                <label>{{ __('Default Values') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="default_values[]" class="form-control default_values" placeholder="{{ __('Default Values') }}" disabled/>
                                            </div>
                                            <div class="form-group col-sm-12 col-md-3">
                                                <button type="button" class="remove-default-values btn btn-danger p-2 px-3" disabled><span class="fa fa-times"></span></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                                <label>{{ __('Default Values') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="default_values[]" class="form-control default_values" placeholder="Default Values" disabled/>
                                            </div>
                                            <div class="form-group col-sm-12 col-md-3">
                                                <button type="button" class="remove-default-values btn btn-danger p-2 px-3" disabled><span class="fa fa-times"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">
                            {{ __('list').' '.__('Form Fields') }}
                        </h4>
                        <span class="text-danger">* {{ __('To Reorder the Form Fields, Drag the Table Row Up and Down and then Click on Update Rank') }}</span>
                        <div class="toolbar">
                            <div class="col-md-12 text-end">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#previewFormModal">{{ __('Preview Form') }}</a><br>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('form-fields.show',[1]);
                                    $columns = [
                                        trans('Rank')=>['data-field'=>'rank'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name')=>['data-field'=>'name'],
                                        trans('Type')=>["data-field"=>"type", "data-sortable"=>true],
                                        trans('Required')=>["data-field"=>"is_required", "data-sortable"=>true,"data-formatter"=>"badgeFormatter"],
                                        trans('Default Values')=>["data-field"=>"default_values", "data-sortable"=>true],
                                        trans('Other')=>["data-field"=>"other", "data-sortable"=>true, "data-visible"=>false],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('form-fields')],
                                        'deleteButton'=>['url'=>url('form-fields')],
                                        'data-events'=>'formFieldEvents'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn draggableRows="true" showPagination="false" sortName="rank" sortOrder="ASC"></x-bootstrap-table>
                            </div>
                        </div>
                        <div class="toolbar mt-3">
                            <button id="button" class="btn btn-primary">{{ __('Update Rank') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit').' '.__('Form Field') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{route('form-fields.update',1)}}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['id'=>'edit_name','required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('Type') }} <span class="text-danger">*</span></label>
                                <select name="type" id="edit_type" class="form-control">
                                    <option value="text">{{ __('Text') }}</option>
                                        <option value="number">{{ __('Numeric Values') }}</option>
                                        <option value="dropdown">{{ __('Dropdown') }}</option>
                                        <option value="radio">{{ __('Radio Button') }}</option>
                                        <option value="checkbox">{{ __('Checkbox') }}</option>
                                        <option value="textarea">{{ __('Textarea') }}</option>
                                        <option value="file">{{ __('File Upload') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('Required') }} <span class="text-danger">*</span></label>
                                <input type="checkbox" name="is_required" id="edit_is_required" class="js-switch" value="1"/>
                            </div>

                            <div id="edit-default-values-div" style="display: none;">
                                <div class="form-group col-sm-12 col-md-12">
                                    <button type="button" class="edit-add-more-default-values btn btn-outline-primary p-2 px-3">{{ __('Add Default Values') }} <span class="fa fa-plus"></span></button>
                                </div>
                                <div id="edit-add-default-values">
                                    <div class="row">
                                        <div class="form-group col-sm-10 col-md-10 local-forms">
                                            <label>{{ __('Default Values') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="default_values[]" class="form-control edit_default_values" placeholder="Default Values" disabled/>
                                        </div>
                                        <div class="form-group col-sm-2 col-md-2">
                                            <button type="button" class="edit-remove-default-values btn btn-danger p-2 px-3" disabled><span class="fa fa-times"></span></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-10 col-md-10 local-forms">
                                            <label>{{ __('Default Values') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="default_values[]" class="form-control edit_default_values" placeholder="Default Values" disabled/>
                                        </div>
                                        <div class="form-group col-sm-2 col-md-2">
                                            <button type="button" class="edit-remove-default-values btn btn-danger p-2 px-3" disabled><span class="fa fa-times"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewFormModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('Admission Form Preview') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" id="previewFormBody">
                        @foreach($formFields as $row)
                            @if($row->type==="text" || $row->type==="number")
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('name') }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                    <input type="{{$row->type}}" name="{{$row->name}}" placeholder="{{ucfirst($row->name)}}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                </div>
                            @endif

                            @if($row->type==="dropdown")
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ ucfirst($row->name) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                    <select name="{{ $row->name }}" class="form-control">
                                        @foreach(json_decode($row->default_values) as $options)
                                            <option value="{{$options}}">{{ucfirst($options)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if($row->type==="radio")
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ ucfirst($row->name) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                    <br>
                                    <div class="d-flex">
                                        @foreach(json_decode($row->default_values) as $options)
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" name="{{$row->name}}" value="{{$options}}">
                                                    {{ ucfirst($options) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($row->type==="checkbox")
                                <div class="form-group row">
                                    <label class="col-form-label col-md-2">{{ucfirst($row->name)}}</label>
                                    <div class="col-md-10">
                                        @foreach(json_decode($row->default_values) as $options)
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="{{$row->name}}"> {{ucfirst($options)}}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($row->type==="textarea")
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ ucfirst($row->name) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                    <textarea name="{{$row->name}}" cols="10" rows="3" placeholder="{{ucfirst($row->name)}}" class="form-control"></textarea>
                                </div>
                            @endif

                            @if($row->type==="file")
                                <div class="col-12 col-sm-12 col-md-6">
                                    <div class="form-group files row">
                                        <label class="col-4">{{ ucfirst($row->name) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                        <input name="logo" type="file" class="form-control">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('cancel')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#button').click(function () {
            let idByOrder = JSON.stringify($('#table_list').bootstrapTable('getData').map((row) => row.id));
            let data = new FormData();
            data.append('ids', idByOrder);
            data.append('_method', 'PATCH');
            ajaxRequest('POST', baseUrl + '/form-fields/change-rank', data, null, (response) => {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message)
            })
        })
    </script>
@endsection