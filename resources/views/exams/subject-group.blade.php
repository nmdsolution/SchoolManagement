@extends('layout.master')

@section('title')
    {{ __('Exam Result Subject Group') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Exam Result Subject Group') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create Group') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" data-success-function="customTermSuccess" action="{{ route('result-subject-group.store') }}" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Rank') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('position', null, ['required', 'placeholder' => __('Rank'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Group List') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('result-subject-group.show',[1]);
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name ')=>['data-field'=>'name'],
                                        trans('Rank')=>['data-field'=>'position'],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('exam/result-subject-group')],
                                        'deleteButton'=>['url'=>url('exam/result-subject-group')],
                                        'data-events'=>'examResultSubjectGroupEvents'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Assign Subject Group to Class') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('exam.subject-group.assigned-list');
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name ')=>['data-field'=>'name'],
                                        trans('Subject Group')=>['data-field'=>'exam_result_subject_group','data-formatter'=>'subjectGroupFormatter'],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=> url('exam/result-subject-group'), 'redirection' => true],
                                        'deleteButton'=>false,
                                        'data-events'=>'changeSubjectGroups'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn sortName="id" queryParams="SubjectGroupQueryParam" sortOrder="asc"></x-bootstrap-table>
                            </div>
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
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Edit Exam Sequence') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="edit-form" class="editform" action="{{ url('exam/result-subject-group') }}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" name="id" id="edit_id">

                        <span>{{ __('Exam Subject Group') }}</span>
                        <div class="row mt-3">

                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['id'=>'edit_name','required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Rank') }} <span class="text-danger">*</span></label>
                                {!! Form::number('position', null, ['id'=>'edit_rank','required', 'placeholder' => __('Rank'), 'class' => 'form-control']) !!}
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
@section('script')
    <script>
        function customTermSuccess() {
            $('#exam_term_id option:selected').remove();
        }
    </script>
@endsection