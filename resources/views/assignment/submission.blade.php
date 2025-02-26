@extends('layout.master')

@section('title')
    {{ __('manage') . ' ' . __('assignment') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('assignment_submission') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('assignment_submission') }}
                        </h4>

                        <div id="toolbar">
                            <div class="row">

                                <div class="col-sm-12 col-md-3 mb-4 local-forms">
                                    <label for="">{{ __('subject') }}</label>
                                    <select name="filter_class_section_id" id="filter_class_section_id" class="form-control select">
                                        <option value="">{{ __('select') . ' ' . __('class') . ' ' . __('section') }}
                                        </option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}">{{ $section->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-12 col-md-3 mb-4 local-forms">
                                    <label for="">{{ __('subject') }}</label>
                                    <select name="filter_subject_id" id="filter_subject_id" class="form-control select">
                                        <option value="">{{ __('all') }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        @php
                            $url = route('assignment.submission.list');
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('assignment_name') => ['data-field' => 'assignment_name'],
                                trans('subject') => ['data-field' => 'subject'],
                                trans('student_name') => ['data-field' => 'student_name'],
                                trans('files') => ['data-field' => 'file', 'data-formatter' => 'fileFormatter'],
                                trans('status') => ['data-field' => 'status', 'data-formatter' => 'assignmentSubmissionStatusFormatter'],
                                trans('points') => ['data-field' => 'points'],
                                trans('feedback') => ['data-field' => 'feedback'],
                                trans('session_year_id') => ['data-field' => 'session_year_id', 'data-visible' => false],
                                trans('created_at') => ['data-field' => 'created_at', 'data-visible' => false],
                                trans('updated_at') => ['data-field' => 'updated_at', 'data-visible' => false],
                            ];
                            $actionColumn = [
                                'editButton' => ['url' => url('assignment-submission')],
                                'deleteButton' => false,
                                'data-events' => 'assignmentSubmissionEvents',
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                                           queryParams="AssignmentSubmissionQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('assignment_submission') }}
                            </h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 class-edit-form" id="edit-form" action="{{ url('assignment-submission') }}"
                              novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value=""/>
                            <div class="modal-body row">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('assignment_name') }}</label>
                                    <input type="text" name="" id="assignment_name" class="form-control" disabled>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('subject') }}</label>
                                    <input type="text" name="" id="subject" class="form-control" disabled>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('student_name') }}</label>
                                    <input type="text" name="" id="student_name" class="form-control" disabled>
                                </div>

                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('files') }}</label>
                                    <div id="files"></div>
                                </div>

                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="status"
                                                       id="status_accept" value="1">{{ __('accept') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="status"
                                                       id="status_reject" value="2">{{ __('reject') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-12 local-forms" id="points_div">
                                    <label>{{ __('points') }} <span id="assignment_points"></span></label>
                                    <input type="number" name="points" id="points" class="form-control" min="0">
                                </div>

                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('feedback') }}</label>
                                    {!! Form::textarea('feedback', null, ['class' => 'form-control', 'id' => 'feedback']) !!}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-primary" type="submit" value={{ __('edit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
