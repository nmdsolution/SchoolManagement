@extends('layout.master')

@section('title')
    {{ __('class') }} {{__('teacher')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="car\`qd-title">
                            {{ __('assign') . ' ' . __('class') . ' ' . __('teacher') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <div id="toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3 local-forms">
                                            <label for="">{{ __('Class') }}</label>
                                            <select name="filter_class_id" id="filter_class_id" class="form-control">
                                                <option value="">{{ __('all') }}</option>
                                                @foreach ($classes as $class)
                                                    <option value={{ $class->id }}>
                                                        {{ $class->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                @php
                                    $url = url('class-teacher-list');
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                         trans('class')=>['data-field'=>'class'],
                                         trans('section')=>['data-field'=>'section'],
                                          trans('teacher')=>['data-field'=>'teacher'],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>route('class.teacher.store')],
                                        'deleteButton'=>false,
                                        'data-events'=>'classTeacherEvents'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="AssignTeacherQueryParams"></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('class') . ' ' . __('teacher') }}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="edit-class-teacher-form" action="{{ route('class.teacher.store') }}" novalidate="novalidate">
                            @csrf
                            <div class="modal-body">
                                <div class="row form-group">
                                    <div class="form-group col-sm-12 col-md-12">
                                        {{-- hidden input to store id --}}
                                        <input type="hidden" name="class_section_id" id="class_section_id_value">

                                        <label>{{ __('class') }} {{ __('section') }} <span class="text-danger">*</span></label>
                                        <select name="class_section_id_select" id="class_section_id" class="form-control" disabled>
                                            @foreach ($class_section as $section)
                                                <option value="{{ $section->id }}">
                                                    {{ $section->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('teacher') }} <span class="text-danger">*</span></label>
                                        <select name="teacher_id" id="teacher_id" class="form-control">
                                            @foreach ($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">
                                                    {{ $teacher->user->first_name . ' ' . $teacher->user->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <a style="cursor: pointer" id="remove_class_teacher" class="ml-4">{{__('click_here_to_remove_class_teacher')}}:- <span id="teacher_name"></span> </a>
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
        </div>
    </div>
@endsection
