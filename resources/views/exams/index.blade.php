@extends('layout.master')

@section('title')
    {{ __('manage') . ' ' . __('exam') }}
@endsection

@section('content')
    <div class="content-wrapper">
        @can('exam-create')
            <div class="page-header">
                <h3 class="page-title">
                    {{ __('manage') . ' ' . __('exam') }}
                </h3>
            </div>
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                {{ __('create') . ' ' . __('exams') }}
                            </h4>
                            {{--CENTER CREATE EXAM FORM START--}}
                            @role('Center')
                            <form class="pt-3 mt-6 add-exam-form create-form" data-success-function="createExamSuccess" method="POST" action="{{ url('exams') }}">
                                <div class="row">

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Type') }} <span class="text-danger">*</span></label>
                                        <select required name="type" id="type" class="form-control select" style="width:100%;" tabindex="-1" aria-hidden="true" required`>
                                            <option value="" hidden="">--{{ __('Select Exam Type') }}--</option>
                                            <option value="1">{{ __('Sequential Exam') }}</option>
                                            <option value="2">{{ __('Specific Exam') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('exam_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" placeholder="{{ __('exam_name') }}" class="form-control"/>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms sequential-exam">
                                        <label>{{ __('Exam Terms') }} <span class="text-danger">*</span></label>
                                        <select name="exam_term_id" id="exam_term_id" class="form-control">
                                            <option value="">--{{ __('Select Exam Term') }}--</option>
                                            @foreach ($exam_terms as $row)
                                                <option value="{{ $row->id }}" data-session-year="{{$row->session_year_id}}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms sequential-exam">
                                        <label>{{ __('Exam Sequences') }} <span class="text-danger">*</span></label>
                                        <select name="exam_sequence_id" id="exam_sequence_id" class="form-control">
                                            <option value="">--{{ __('Select Exam Sequence') }}--</option>
                                            @foreach ($exam_sequence as $row)
                                                <option value="{{ $row->id }}" data-term-id="{{$row->exam_term_id}}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if (isset($class_sections))
                                        <div class="form-group col-sm-12 col-md-6">
                                            <label>{{ __('class') }}<span class="text-danger">*</span></label>
                                            <br>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="select-all-class-section"/>
                                                    {{ __('Select All') }}
                                                </label>
                                            </div>
                                            <select name="class_section_id[]" id="class_section_id" class="form-control select" multiple>
                                                @foreach ($class_sections as $class_section)
                                                    <option value="{{$class_section['id']}}">
                                                        {{$class_section['class']['name']." ".$class_section['section']['name']}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="form-group col local-forms">
                                        <label>{{ __('exam_description') }}</label>
                                        <textarea id="description" name="description" placeholder="{{ __('exam_description') }}" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('teacher_status') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('teacher_status', '1',true,['required'=>true]) !!}
                                                {{ __('Active') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('teacher_status', '0',false ,['required'=>true]) !!}
                                                {{ __('Inactive') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('student_status') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('student_status', '1',true,['required'=>true]) !!}
                                                {{ __('Active') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('student_status', '0',false ,['required'=>true]) !!}
                                                {{ __('Inactive') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <input class="btn btn-primary" id="add-exam-btn" type="submit" value={{ __('submit') }}>
                            </form>
                            @endrole
                            {{--CENTER CREATE EXAM FORM END--}}

                            {{--TEACHER CREATE EXAM FORM START--}}
                            @role('Teacher')
                            <form class="teacher-create-exam-validate pt-3 mt-6 create-form" data-success-function="createExamSuccess" method="POST" action="{{ url('exams/subject-teacher/create') }}">
                                <div class="row">

                                    <div class="form-group col-sm-12 col-md-12 local-forms">
                                        <label>{{ __('exam_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" placeholder="{{ __('exam_name') }}" class="form-control"/>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms sequential-exam">
                                        <label>{{ __('Exam Terms') }} <span class="text-danger">*</span></label>
                                        <select name="exam_term_id" id="exam_term_id" class="form-control" required>
                                            <option value="">--{{ __('Select Exam Term') }}--</option>
                                            @foreach ($exam_terms as $row)
                                                <option value="{{ $row->id }}" data-session-year="{{$row->session_year_id}}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms sequential-exam">
                                        <label>{{ __('Exam Sequences') }} <span class="text-danger">*</span></label>
                                        <select name="exam_sequence_id" id="exam_sequence_id" class="form-control" required>
                                            <option value="">--{{ __('Select Exam Sequence') }}--</option>
                                            @foreach ($exam_sequence as $row)
                                                <option value="{{ $row->id }}" data-term-id="{{$row->exam_term_id}}" data-start-date="{{$row->start_date}}" data-end-date="{{$row->end_date}}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if (isset($class_sections))
                                        <div class="form-group col-sm-12 col-md-12">
                                            <label>{{ __('class') }}<span class="text-danger">*</span></label>
                                            <br>
                                            <select name="class_section_id" id="exam_classes_options" class="form-control select" required>
                                                <option value="" hidden="">--{{ __('Select Class Section') }}--</option>
                                                @foreach ($class_sections as $class_section)
                                                    <option value="{{$class_section['id']}}" data-class-id="{{$class_section['class']['id']}}">
                                                        {{$class_section['class']['name']." ".$class_section['section']['name']}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="form-group col-md-4 local-forms">
                                        <label>{{ __('subject') }}<span class="text-danger">*</span></label>
                                        <select name="timetable_subject_id" class="form-control exam_subjects_options" required>
                                            <option value="select_option">--{{ __('select') }}--</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 local-forms">
                                        <label>{{ __('total_marks') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="timetable_total_marks" class="total_marks form-control" placeholder="{{ __('total_marks') }}" min="1" required>
                                    </div>
                                    <div class="form-group col-md-4 local-forms">
                                        <label>{{ __('passing_marks') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="timetable_passing_marks" class="passing_marks form-control" placeholder="{{ __('passing_marks') }}" min="1" required>
                                    </div>

                                    <div class="form-group col-md-4 local-forms">
                                        <label>{{ __('start_time') }} <span class="text-danger">*</span></label>
                                        <input type="time" name="timetable_start_time" class="start_time form-control" placeholder="{{ __('start_time') }}" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-4 local-forms">
                                        <label>{{ __('end_time') }} <span class="text-danger">*</span></label>
                                        <input type="time" name="timetable_end_time" class="end_time form-control" placeholder="{{ __('end_time') }}" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-3 local-forms">
                                        <label>{{ __('date') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="timetable_date" id="timetable_date" class="disable-past-date form-control" placeholder="{{ __('date') }}" autocomplete="off" required>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col local-forms">
                                            <label>{{ __('exam_description') }}</label>
                                            <textarea id="description" name="description" placeholder="{{ __('exam_description') }}" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input class="btn btn-primary" id="add-exam-btn" type="submit" value={{ __('submit') }}>
                            </form>
                            @endrole
                            {{--TEACHER CREATE EXAM FORM END--}}
                        </div>
                    </div>
                </div>
            </div>
    </div>
    @endcan
    @can('exam-list')
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('exams') }}
                        </h4>

                        <table aria-describedby="mydesc" class='table table-striped table_list' id="table_list" data-toggle="table" data-url="{{ route('exams.show', 1) }}" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc" data-maintain-selected="true" data-export-types='["txt","excel"]' data-export-options='{ "fileName": "exam-list-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}' data-show-export="true" data-detail-formatter="examListFormatter">
                            <thead>
                            <tr>

                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no" data-sortable="false">{{ __('no') }}</th>
                                <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>
                                <th scope="col" data-field="description" data-sortable="true">{{ __('description') }}</th>
                                <th scope="col" data-field="class_name" data-sortable="false">{{ __('class') }}</th>
                                <th scope="col" data-field="publish" data-sortable="true" data-formatter="examPublishFormatter">{{ __('publish') }}</th>
                                <th scope="col" data-field="session_year_name" data-sortable="false">{{ __('session_years') }}</th>
                                <th scope="col" data-field="term_name" data-sortable="false">{{ __('Term') }}</th>
                                <th scope="col" data-field="sequence_name" data-sortable="false">{{ __('Sequence') }}</th>
                                @hasanyrole(['Center','Class Teacher'])
                                <th scope="col" data-field="timetable" data-formatter="marksUploadStatus" data-sortable="false">{{ __('Mark Upload Status') }}</th>
                                @endhasanyrole
                                @hasanyrole(['Center'])
                                <th scope="col" data-field="teacher_status" data-formatter="statusFormatter" data-sortable="false">{{ __('Teacher Status') }}</th>
                                <th scope="col" data-field="student_status" data-formatter="statusFormatter" data-sortable="false">{{ __('Student Status') }}</th>
                                @endhasanyrole
                                <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-sortable="false" data-events="examEvents">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('lesson') }}
                            </h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form class="pt-3 edit-exam-form" id="edit-form" action="{{ url('exams') }}" novalidate="novalidate">
                            <div class="modal-body">
                                <div id="message" style="display: none;">
                                    <h3 class="text-danger">{{trans('Sequence is not Activated. Please Activate the Sequence First')}}</h3>
                                </div>
                                <div id="form-content">
                                    <input type="hidden" name="edit_id" id="edit_id" value=""/>
                                    <input type="hidden" name="exam_id" id="edit_timetable_exam_id" class="form-control" required>
                                    <input type="hidden" name="class_section_id" id="edit_timetable_class_section_id" class=" form-control" required>
                                    <input type="hidden" name="session_year_id" id="edit_timetable_session_year_id" class="form-control" required>

                                    <div class="row">
                                        <div class="form-group col-md-4local-forms">
                                            <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                            <input type="text" id="edit_subject" class="form-control" value="" disabled/>
                                            <input type="hidden" name="edit_timetable[0][subject_id]" id="edit_subject_id" class="form-control" value=""/>
                                            <input type="hidden" name="edit_timetable[0][timetable_id]" id="edit_timetable_id" class="form-control" value=""/>
                                        </div>
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('total_marks') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="edit_timetable[0][total_marks]" id="edit_total_marks" class=" form-control" placeholder="{{ __('total_marks') }}" min="1" required>
                                        </div>
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('passing_marks') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="edit_timetable[0][passing_marks]" id="edit_passing_marks" class=" form-control" placeholder="{{ __('passing_marks') }}" min="1" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('start_time') }} <span class="text-danger">*</span></label>
                                            <input type="time" name="edit_timetable[0][start_time]" id="edit_start_time" class=" form-control" placeholder="{{ __('start_time') }}" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-4 local-forms">
                                            <label>{{ __('end_time') }} <span class="text-danger">*</span></label>
                                            <input type="time" name="edit_timetable[0][end_time]" id="edit_end_time" class=" form-control" placeholder="{{ __('end_time') }}" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-3 local-forms">
                                            <label>{{ __('date') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="edit_timetable[0][date]" id="edit_date" class=" form-control disable-past-date" placeholder="{{ __('date') }}" autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12 col-md-6">
                                            <b>{{ __('exam_name') }} : </b>
                                            <span id="edit_name"></span>
                                            {{--                                        <input type="text" required id="edit_name" name="name" placeholder="{{ __('exam_name') }}" class="form-control"/>--}}
                                        </div>
                                        <div class="form-group col-sm-12 col-md-12">
                                            <label>{{ __('Teacher Status') }} <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="d-flex">
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        {!! Form::radio('teacher_status', '1',true,['id'=>'edit_teacher_status_active','required'=>true]) !!}
                                                        {{ __('Active') }}
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        {!! Form::radio('teacher_status', '0',false ,['id'=>'edit_teacher_status_inactive','required'=>true]) !!}
                                                        {{ __('Inactive') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group col-sm-12 col-md-12">
                                            <label>{{ __('Student Status') }} <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="d-flex">
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        {!! Form::radio('student_status', '1',true,['id'=>'edit_student_status_active','required'=>true]) !!}
                                                        {{ __('Active') }}
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        {!! Form::radio('student_status', '0',false ,['id'=>'edit_student_status_inactive','required'=>true]) !!}
                                                        {{ __('Inactive') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        @endcan</div>
        @endsection

        @section('js')
            <script>
                $('#session_year_id').on('change', function () {
                    $('#exam_term_id option').hide();
                    if ($('#exam_term_id option[data-session-year="' + $(this).val() + '"]').length) {
                        $('#exam_term_id option[data-session-year="' + $(this).val() + '"]').show();
                        $('#exam_term_id option[value=""]').text('--Select Exam Term--');
                    } else {
                        $('#exam_term_id option[value=""]').text('--No Exam Terms Available--');
                        // $('#exam_term_id').append('<option value="">--No Exam Terms Available--</option>').val('');
                    }
                })

                $('#edit_session_year_id').on('change', function () {
                    $('#exam_term_id option').hide();
                    if ($('#edit_exam_term_id option[data-session-year="' + $(this).val() + '"]').length) {
                        $('#edit_exam_term_id option[data-session-year="' + $(this).val() + '"]').show();
                        $('#edit_exam_term_id option[value=""]').text('--Select Exam Term--');
                    } else {
                        $('#edit_exam_term_id option[value=""]').text('--No Exam Terms Available--');
                        // $('#exam_term_id').append('<option value="">--No Exam Terms Available--</option>').val('');
                    }
                })

                $('#type').on('change', function () {
                    // If Sequential Exam then show the Term and sequence
                    if ($(this).val() === "1") {
                        $('.sequential-exam').show();
                    } else {
                        $('.sequential-exam').hide();
                    }
                })
                $('#exam_term_id').on('change', function () {
                    $('#exam_sequence_id option').hide();
                    $('#exam_sequence_id option[data-term-id=' + $(this).val() + ']').show();
                })

                @role('Teacher')
                $('#exam_sequence_id').on('change', function () {
                    $('#timetable_date').data('DateTimePicker').options(eval({
                        'minDate': new Date($(this).find('option:selected').data('start-date')),
                        'maxDate': new Date($(this).find('option:selected').data('end-date'))
                    }));
                })
                @endrole

                function createExamSuccess(response) {
                    if (!response.error) {
                        $('#class_section_id').val('').trigger('change');
                        $('#type').val('').trigger('change');
                    }
                }
            </script>
        @endsection