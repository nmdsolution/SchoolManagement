@extends('layout.master')

@section('title')
    {{ __('language_settings') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('language_settings') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="create-form" action="{{ url('language') }}" novalidate="novalidate"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label>{{ __('language_name') }} <span class="text-danger">*</span></label>
                                    <input name="name" type="text" required placeholder="{{ __('language_name') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label>{{ __('language_code') }} <span class="text-danger">*</span></label>
                                    <input name="code" type="text" required placeholder="{{ __('language_code') }}" class="form-control"/>
                                </div>
                            </div>
                            <div class="row">
                                {{-- <div class="form-group col-md-6 col-sm-12">
                                    <label>{{ __('upload_file') }} <span class="text-danger">*</span></label>
                                    <input type="file" name="file" class="file-upload-default" accept="application/json"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" accept="application/json"
                                               disabled="" placeholder="{{ __('upload_file') }}"/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary"
                                                    type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                </div> --}}

                                <div class="col-12 col-sm-12 col-md-2">
                                    <div class="form-group files">
                                        <label>{{ __('upload_file') }}</label>
                                        <input name="file" type="file" class="form-control">

                                    </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                    <br>
                                    <a class="btn btn-success" href="{{ url('language-sample') }}">{{ __('download_sample') }}</a>

                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-check-input mt-0 mx-2" type="checkbox" value="1" name="rtl" aria-label="Checkbox for following text input">
                                    <label class="mx-2">{{ __('Is RTL') }}</label>
                                </div>
                            </div>

                            <input class="btn btn-primary" type="submit" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('language') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('language-list');
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name')=>['data-field'=>"name", ],
                                        trans('code')=>['data-field'=>"code", 'data-sortable'=>true],
                                        trans('Is RTL')=>['data-field'=>"rtl", 'data-sortable'=>true, 'data-formatter'=>"languageRtlStatusFormatter"],
                                        trans('status')=>['data-field'=>"status", 'data-sortable'=>true, 'data-visible'=>false],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('language')],
                                        'deleteButton'=>['url'=>url('language')],
                                        'data-events'=>'languageEvents'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit') . ' ' . __('language') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{ url('language') }}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('language_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('language_name'), 'class' => 'form-control', 'id' => 'name']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('language_code') }} <span class="text-danger">*</span></label>
                                {!! Form::text('code', null, ['required', 'placeholder' => __('language_code'), 'class' => 'form-control', 'id' => 'code']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('upload_file') }}</label>
                                <input type="file" name="file" class="file-upload-default form-control"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 mx-1.5">
                                {!! Form::checkbox('rtl', null, ['required', 'class' => 'form-control', 'id' => 'rtl']) !!}
                                <label>{{ __('Is RTL') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
