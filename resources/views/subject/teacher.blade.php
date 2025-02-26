@extends('layout.master')

@section('title')
    {{ __('subject') . ' ' . __('teacher') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('subject') . ' ' . __('teacher') }}
            </h3>
        </div>

        <div class="row">
            @can('subject-teacher-create')
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('assign') . ' ' . __('subject') . ' ' . __('teacher') }}
                            </h4>
                            <form class="assign_subject_teacher pt-3" action="{{ url('subject-teachers') }}" method="POST" novalidate="novalidate">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('class') }} {{ __('section') }} <span class="text-danger">*</span></label>
                                        <select name="class_section_id" id="class_section_id" class="class_section_id form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="">{{ __('select') }}</option>
                                            @foreach ($class_section as $section)
                                                <option value="{{$section->id}}" data-class="{{ $section->class->id }}"> {{ $section->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                        <select name="subject_id" id="subject_id" class="subject_id form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="">{{ __('select') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('teacher') }} <span class="text-danger">*</span></label>
                                        <select multiple name="teacher_id[]" id="teacher_id" class="form-control js-example-basic-single select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true"></select>
                                    </div>
                                </div>
                                <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

            @can('subject-teacher-list')
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('list') . ' ' . __('subject') . ' ' . __('teacher') }}
                            </h4>
                            <div class="row">

                                <div id="toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-2 mb-4 local-forms">
                                            <label for="">{{ __('class_section') }}</label>
                                            <select name="filter_class_section_id" id="filter_class_section_id" class="form-control">
                                                <option value="">{{ __('select_class_section') }}</option>
                                                @foreach ($class_section as $class)
                                                    <option value={{ $class->id }}>
                                                        {{ $class->full_name  }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-12 col-md-3 mb-4 local-forms">
                                            <label for="">{{ __('teacher') }}</label>
                                            <select name="filter_teacher_id" id="filter_teacher_id" class="form-control">
                                                <option value="">{{ __('select_teacher') }}</option>
                                                @foreach ($teachers as $teacher)
                                                    <option value={{ $teacher->id }}>
                                                        {{ $teacher->user->first_name . ' ' . $teacher->user->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-12 col-md-3 mb-4 local-forms">
                                            <label for="">{{ __('subject') }}</label>
                                            <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                                <option value="">{{ __('select_subject') }}</option>
                                                @foreach ($subjects as $subject)
                                                    <option value={{ $subject->id }}>
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    @php
                                        $url = url('subject-teachers-list');
                                        $columns = [
                                            trans('no')=>['data-field'=>'no'],
                                            trans('id')=>['data-field'=>'id','data-visible'=>false],
                                            trans('class_section_id')=>['data-field'=>'class_section_id','data-visible' => false],
                                            trans('class') . ' ' . trans('section') . ' ' . trans('name')=>['data-field'=>'class_section_name'],
                                            trans('subject_id')=>['data-field'=>'subject_id','data-visible' => false],
                                            trans('subject') . ' ' . trans('name')=>['data-field'=>'subject_name'],
                                            trans('teacher_id')=>['data-field'=>'teacher_id','data-visible' => false],
                                            trans('teacher') . ' ' . trans('name')=>['data-field'=>'teacher_name'],
                                            trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                            trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                        ];

                                        $actionColumn = array()

                                    @endphp

                                    {{-- @canany(['subject-teacher-edit', 'subject-teacher-delete']) --}}
                                    @php
                                        $actionColumn = [
                                            'editButton'=> ['url'=>url('subject-teachers')],
                                            'data-events'=>'subjectTeachersEvents',
                                            'deleteButton'=> ['url'=>url('subject-teachers')],
//                                            'customModal' => ['url' => url('subject-teachers'), 'customClass' => 'subject-teacher-delete'],
                                        ];
                                    @endphp
                                    {{-- @endcanany --}}

                                    <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="AssignSubjectTeacherQueryParams"></x-bootstrap-table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ __('edit') . ' ' . __('subject') . ' ' . __('teacher') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{ url('subject-teachers') }}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('class') }} {{ __('section') }} <span class="text-danger">*</span></label>
                                <select name="class_section_id" id="edit_class_section_id" class="class_section_id form-control select2" style="width:100%;">
                                    @foreach ($class_section as $section)
                                        <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                            {{ $section->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                <select name="subject_id" id="edit_subject_id" class="subject_id form-control select2" style="width:100%;">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('teacher') }} <span class="text-danger">*</span></label>
                                <select name="teacher_id" id="edit_teacher_id" class="form-control select2" style="width:100%;">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customModal" tabindex="-1" role="dialog" aria-labelledby="customModalTitle" aria-hidden="true">
        <form method="POST" id="delete-subject-teachers" action="/subject-teachers/updation">
            @csrf
            @method('DELETE')

            <input type="hidden" name="edit_id" id="customId">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{ trans("unassign_subject_teacher") }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ trans("confirm_unassign_subject") }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="button" class="btn btn-primary">{{ trans("delete") }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('js')

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {

            const item = document.getElementById('customModalId');
            item.addEventListener('click', function () {
                alert('clicked');
            })


            $('delete-subject-teachers').on('submit', function (event) {
                event.preventDefault();

                $.ajax({
                    url: '/subject-teachers/',
                    type: 'DELETE',
                    data: {},
                    success: function(response) {
                        showSuccessToast(response.message);
                    },
                    error: function(error) {
                        showErrorToast(error.message ?? "An Error message Occurred");
                    }
                });

                $('#customModal').modal('hide');

                $('#table_list').bootstrapTable('refresh');
            });
        });
    </script>
@endsection
