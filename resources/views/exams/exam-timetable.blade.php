@extends('layout.master')

@section('title')
    {{ __('manage') . ' ' . __('exam') . ' ' . __('timetable') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('exam') . ' ' . __('timetable') }}
            </h3>
        </div>

        @can('exam-timetable-create')
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="page-title mb-4">
                                {{ __('create') . ' ' . __('exam') . ' ' . __('timetable') }}
                            </h4>
                            <div class="form-group">
                                <form class="create_exam_timetable_form" action="{{ url('exam-timetable') }}" method="POST">
                                    <div class="row">
                                        <div class="form-group col-md-6 local-forms">
                                            <label>{{ __('exam') }} </label>
                                            <select name="exam_id" id="exam_options" class="form-control" required>
                                                <option value="">--{{ __('select') }}--</option>
                                                @foreach ($exams as $exam)
                                                    <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 local-forms">
                                            <label>{{ __('class') }} </label>
                                            <select name="class_section_id" id="exam_classes_options" class="form-control" required>
                                                <option value="">--{{ __('select') }}--</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="exam_timetable_content">
                                        <div class="row">
                                            <input type="hidden" name="timetable[0][timetable_id]" class="timetable_id form-control" required>
                                            <div class="form-group col-md-4 local-forms">
                                                <label>{{ __('subject') }} </label>
                                                <select name="timetable[0][subject_id]" class="form-control exam_subjects_options" required>
                                                    <option value="">--{{ __('select') }}--</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4 local-forms">
                                                <label>{{ __('total_marks') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="timetable[0][total_marks]" class="total_marks form-control" placeholder="{{ __('total_marks') }}" min="1" required>
                                            </div>
                                            <div class="form-group col-md-4 local-forms">
                                                <label>{{ __('passing_marks') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="timetable[0][passing_marks]" class="passing_marks form-control" placeholder="{{ __('passing_marks') }}" min="1" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4 local-forms">
                                                <label>{{ __('start_time') }} <span class="text-danger">*</span></label>
                                                <input type="time" name="timetable[0][start_time]" class="start_time form-control" placeholder="{{ __('start_time') }}" autocomplete="off" required>
                                            </div>
                                            <div class="form-group col-md-4 local-forms">
                                                <label>{{ __('end_time') }} <span class="text-danger">*</span></label>
                                                <input type="time" name="timetable[0][end_time]" class="end_time form-control" placeholder="{{ __('end_time') }}" autocomplete="off" required>
                                            </div>
                                            <div class="form-group col-md-3 local-forms">
                                                <label>{{ __('date') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="timetable[0][date]" class="form-control disable-past-date" placeholder="{{ __('date') }}" autocomplete="off" required>
                                            </div>
                                            <div class="form-group col-md-1">
                                                <button type="button" class="btn btn-primary btn-icon add-exam-timetable-content">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- container for adding multiple subjects time table when "+" btn is clicked --}}
                                    <div class="extra-timetable"></div>

                                    <input type="submit" class="btn btn-primary" value={{ __('submit') }} />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        @can('exam-timetable-list')
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('list') . ' ' . __('exam') . ' ' . __('timetable') }}
                            </h4>
                            <div id="toolbar" class="row exam_class_filter">

                                <div class="col">
                                    <label for="filter_exam_name">
                                        {{ __('exam') }}
                                    </label>
                                    <select name="filter_exam_name" id="filter_exam_name" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach ($exams as $exam)
                                            <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="filter_class_name">
                                        {{ __('class') }}
                                    </label>
                                    <select name="filter_class_name" id="filter_class_name" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach ($class_sections as $class_section)
                                            <option value="{{ $class_section->id }}">
                                                {{ $class_section->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @php
                                $url = route('exam-timetable.show', 1);
                                $columns = [
                                    trans('no') => ['data-field' => 'no'],
                                    trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                    trans('exam') . ' ' . trans('name') => ['data-field' => 'exam_name'],
                                    trans('class') => ['data-field' => 'class_name'],
                                    trans('timetable') => ['data-field' => 'timetable', 'data-formatter' => 'examTimetableFormatter'],
                                    trans('session_years') => ['data-field' => 'session_year'],
                                    trans('created_at') => ['data-field' => 'created_at', 'data-visible' => false],
                                    trans('updated_at') => ['data-field' => 'updated_at', 'data-visible' => false],
                                ];
                                $actionColumn=false;
                                if(Auth::user()->can('exam-timetable-edit')){
                                $actionColumn = [
                                    'editButton' => ['url' => url('section')],
                                    'deleteButton' => false,
                                    'data-events' => 'examTimetableEvents',
                                ];
                                }
                            @endphp
                            <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="ExamClassQueryParams"></x-bootstrap-table>
                        </div>
                    </div>
                </div>
                @endcan


                <!-- Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    {{ __('edit') . ' ' . __('exam') . ' ' . __('timetable') }}
                                </h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="edit_exam_timetable_tamplate" style="display:none">
                                    <div class="row">
                                        <input type="hidden" name="edit_timetable[0][timetable_id]" class="edit_timetable_id form-control" required/>
                                        <div class="form-group col-md-4local-forms">
                                            <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                            <select name="edit_timetable[0][subject_id]" class="form-control edit_exam_subjects_options" required></select>
                                        </div>
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('total_marks') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="edit_timetable[0][total_marks]" class="edit_total_marks form-control" placeholder="{{ __('total_marks') }}" min="1" required>
                                        </div>
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('passing_marks') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="edit_timetable[0][passing_marks]" class="edit_passing_marks form-control" placeholder="{{ __('passing_marks') }}" min="1" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('start_time') }} <span class="text-danger">*</span></label>
                                            <input type="time" name="edit_timetable[0][start_time]" class="edit_start_time form-control" placeholder="{{ __('start_time') }}" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('end_time') }} <span class="text-danger">*</span></label>
                                            <input type="time" name="edit_timetable[0][end_time]" class="edit_end_time form-control" placeholder="{{ __('end_time') }}" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-3 local-forms">
                                            <label>{{ __('date') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="edit_timetable[0][date]" class="edit_date form-control disable-past-date" placeholder="{{ __('date') }}" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-1 pl-0">
                                            <button type="button" class="btn btn-inverse-danger btn-icon remove-edit-exam-timetable-content">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                <form class="pt-3 edit-form-timetable" action="{{ url('exams/update-timetable') }}" novalidate="novalidate">
                                    <input type="hidden" name="exam_id" class="edit_timetable_exam_id form-control" required>
                                    <input type="hidden" name="class_section_id" class="edit_timetable_class_id form-control" required>
                                    <input type="hidden" name="session_year_id" class="edit_timetable_session_year_id form-control" required>

                                    <div class="edit-timetable-container"></div>
                                    <div class="col-md-4 pl-0 mb-4">
                                        <button type="button" class="btn btn-inverse-success add-new-timetable-data" title="Add new row">
                                            {{ __('Add New Data') }}
                                        </button>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close')}}</button>
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
    <script !src="">
        @if(request('exam_id'))
        $('#exam_options').val("{{request('exam_id')}}").trigger('change');
        @endif
                
        @if(request('class_section_id'))
        setTimeout(function () {
            $('#exam_classes_options').val("{{request('class_section_id')}}").trigger('change');
        }, 500)
        @endif
    </script>
@endsection
