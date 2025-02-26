@extends('layout.master')

@section('title')
    {{ __('subject') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('subject') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('subject') }}
                        </h4>
                        <form class="pt-3 subject-create-form" id="create-form" action="{{ route('subject.store') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group local-forms col-md-6 col-sm-12">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>

                                <div class="form-group local-forms col-md-6 col-sm-12">
                                    <label>{{ __('subject_code') }}</label>
                                    <input name="code" type="text" placeholder="{{ __('subject_code') }}" class="form-control"/>
                                </div>

                                <div class="form-group ">
                                    <label>{{ __('type') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="type" id="theory" value="Theory">
                                                {{__('Theory')}}
                                            </label>
                                        </div>
                                        <div class="form-check  form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="type" id="practical" value="Practical">
                                                {{__('Practical')}}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group local-forms col-md-6 col-sm-12">
                                    <label>{{ __('bg_color') }} <span class="text-danger">*</span></label>
                                    <input name="bg_color" type="text" placeholder="{{ __('bg_color') }}" class="color-picker" autocomplete="off"/>
                                </div>

                                <div class="form-group col-md-6 col-sm-12 ">
                                    {{--                                    <label class="col-form-label col-md-1">{{ __('image') }}</label>--}}
                                    <input class="form-control" type="file" name="image">
                                </div>
                                <div class="col-12">
                                    <div class="student-submit">
                                        <button type="submit" id="create-btn" class="btn btn-primary">{{ __('submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('subject') }}
                        </h4>
                        @php
                            $url = url('subject-list');
                            $columns = [
                                trans('no')=>['data-field'=>'no'],
                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                trans('name')=>['data-field'=>'name'],
                                trans('subject_code')=>['data-field'=>'code','data-sortable'=>true],
                                trans('bg_color')=>['data-field'=>'bg_color','data-formatter'=>"bgColorFormatter",],
                                trans('image')=>['data-field'=>'image','data-formatter'=>"imageFormatter",],
                                trans('type')=>['data-field'=>'type','data-sortable'=>true],
                                trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                            ];
                            $actionColumn = [
                                'editButton'=>['url'=>url('subject')],
                                'deleteButton'=>['url'=>url('subject')],
                                'data-events'=>'subjectEvents'
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="SubjectQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('subject') }}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 subject-edit-form" id="edit-form" action="{{ url('subject') }}" novalidate="novalidate">
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" id="edit_id" value=""/>
                                <div class="form-group local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="edit_name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('type') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input edit" name="type" id="edit_theory" value="Theory">
                                                {{__('Theory')}}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input edit" name="type" id="edit_practical" value="Practical">
                                                {{__('Practical')}}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group local-forms">
                                    <label>{{ __('subject_code') }}</label>
                                    <input name="code" id="edit_code" type="text" placeholder="{{ __('subject_code') }}" class="form-control"/>
                                </div>

                                <div class="form-group local-forms">
                                    <label>{{ __('bg_color') }} <span class="text-danger">*</span></label>
                                    <input name="bg_color" id="edit_bg_color" type="text" placeholder="{{ __('bg_color') }}" class="color-picker" autocomplete="off"/>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('image') }}</label>
                                    <input class="form-control" type="file" id="edit_image" name="image">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                                    <input class="btn btn-primary" type="submit" value={{ __('edit') }} />
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">


        function bgColorFormatter(value, row) {
            return "<p style='background-color:" + row.bg_color + "' class='color-code-box'>" + row.bg_color + "</p>";
        }

        // function imageFormatter(value, row) {
        //     return "<img src='" + row.image + "' class='img-fluid' />";

        // }
    </script>
@endsection
