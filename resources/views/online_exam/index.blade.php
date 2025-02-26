@extends('layout.master')

@section('title')
    {{ __('manage') . ' ' . __('online') . ' ' . __('exam') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('online') . ' ' . __('exam') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            {{ __('create') . ' ' . __('online') . ' ' . __('exam') }}
                        </h4>
                        <form class="pt-3 mt-6" id="create-form" method="POST" action="{{ route('online-exam.store') }}">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3 local-forms">
                                    <label>{{ __('class') }} <span class="text-danger">*</span></label>
                                    <select required name="class_id" class="form-control class_id select2 online-exam-class-id" style="width:100%;" tabindex="-1" aria-hidden="true" id="class_id">
                                        <option value="">--- {{ __('select') . ' ' . __('class') }} ---</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->class->id }}">{{ $class->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3 local-forms">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select required name="subject_id" class="form-control subject_id select2 online-exam-subject-id" style="width:100%;" tabindex="-1" aria-hidden="true" id="subject_id">
                                        <option value="">--- {{ __('select') . ' ' . __('subject') }} ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="online-exam-title" name="title" placeholder="{{ __('title') }}" class="form-control" required/>
                                </div>
                                <div class="form-group col-md-2 col-sm-12 local-forms">
                                    <label>{{ __('exam') }} {{ __('key') }} <span class="text-danger">*</span></label>
                                    <input type="number" id="online-exam-key" name="exam_key" placeholder="{{ __('exam_key') }}" class="form-control" required/>
                                </div>
                                <div class="form-group col-md-2 col-sm-12 local-forms">
                                    <label>{{ __('duration') }} <span class="text-danger">*</span>
                                        <span class="text-info small">( {{ __('in_minutes') }} )</span></label>

                                    <input type="number" id="online-exam-duration" name="duration" placeholder="{{ __('duration') }}" class="form-control" required/>
                                </div>
                                <div class="form-group col-md-2 col-sm-12 local-forms">
                                    <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                                    {{-- <input type="text" id="online-exam-date" name="date" class="datetimepicker date form-control" placeholder="{{ __('date') }}" autocomplete="off" required> --}}
                                    <input type="datetime-local" id="online-exam-start-date" name="start_date" placeholder="{{ __('start_date') }}" class='form-control' required>
                                </div>
                                <div class="form-group col-md-2 col-sm-12 local-forms">
                                    <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                    {{-- <input type="text" id="online-exam-date" name="date" class="datetimepicker date form-control" placeholder="{{ __('date') }}" autocomplete="off" required> --}}
                                    <input type="datetime-local" id="online-exam-end-date" name="end_date" placeholder="{{ __('end_date') }}" class='form-control' required>
                                </div>
                            </div>
                            <input class="btn btn-primary" id="add-online-exam-btn" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('exams') }}
                        </h4>
                        <div id="toolbar" class="row">

                            <div class="form-group col-sm-12 mb-4 col-md-3 local-forms">
                                <label>{{ __('class') }}</label>
                                <select name="class_id" id="filter_class_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('all') }}</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->class->id }}">
                                            {{ $class->class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 mb-4 col-md-3 local-forms">
                                <label>{{ __('subject') }}</label>
                                <select name="subject_id" id="filter_subject_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('all') }}</option>
                                </select>
                            </div>
                        </div>
                        @php
                            $url = route('online-exam.show', 1);
                            $columns = [
                                trans('No') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('class') => ['data-field' => 'class_name', 'data-sortable' => true],
                                trans('subject') => ['data-field' => 'subject_name', 'data-sortable' => true],
                                trans('title') => ['data-field' => 'title', 'data-sortable' => true],
                                trans('exam_key') => ['data-field' => 'exam_key', 'data-sortable' => true],
                                trans('duration') => ['data-field' => 'duration', 'data-sortable' => true],
                                trans('start_date') => ['data-field' => 'start_date', 'data-sortable' => true],
                                trans('end_date') => ['data-field' => 'end_date', 'data-sortable' => true],
                                trans('total_question') => ['data-field' => 'total_questions', 'data-sortable' => true],
                            ];
                            $actionColumn = [
                                'customButton' => [['url' => url('get-exam-question-index'),'iconClass' => 'fa fa-question-circle', 'customClass' => 'add_question'],['url' => url('online-exam/result'),'iconClass' => 'feather-file']],
                                'editButton' => ['url' => url('online-exam')],
                                'deleteButton' => ['url' => url('online-exam')],
                                'data-events' => 'onlineExamEvents',
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="onlineExamQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- model --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') }} {{ __('online') }}
                        {{ __('exam') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="edit-form" class="pt-3 edit-form" action="{{ url('online-exam') }}">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-body row">
                        <div class="form-group local-forms">
                            <label>{{ __('class') }} <span class="text-danger">*</span></label>
                            <select required name="edit_class_id" id="edit-online-exam-class-id" class="form-control select2 online-exam-class-id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                <option value="">--- {{ __('select') . ' ' . __('class') }} ---</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->class->id }}">{{ $class->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group local-forms">
                            <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                            <select required name="edit_subject_id" id="edit-online-exam-subject-id" class="form-control select2 online-exam-subject-id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                <option value="">--- {{ __('select') . ' ' . __('subject') }} ---</option>
                            </select>
                        </div>
                        <div class="form-group local-forms">
                            <label>{{ __('title') }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit-online-exam-title" name="edit_title" placeholder="{{ __('title') }}" class="form-control" required/>
                        </div>

                        <div class="form-group local-forms col-md-6">
                            <label>{{ __('exam') }} {{ __('key') }} <span class="text-danger">*</span></label>
                            <input type="number" id="edit-online-exam-key" name="edit_exam_key" placeholder="{{ __('exam_key') }}" class="form-control" required/>
                        </div>
                        <div class="form-group local-forms col-md-6">
                            <label>{{ __('duration') }}
                                <span class="text-danger">* </span><span class="text-info small">( {{ __('in_minutes') }} )</span></label>

                            <input type="number" id="edit-online-exam-duration" name="edit_duration" placeholder="{{ __('duration') }}" class="form-control" required/>
                        </div>

                        <div class="form-group local-forms col-md-6">
                            <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="edit-online-exam-start-date" name="edit_start_date" placeholder="{{ __('start_date') }}" class='form-control' required>
                        </div>
                        <div class="form-group local-forms col-md-6">
                            <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="edit-online-exam-end-date" name="edit_end_date" placeholder="{{ __('end_date') }}" class='form-control' required>
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
@endsection
