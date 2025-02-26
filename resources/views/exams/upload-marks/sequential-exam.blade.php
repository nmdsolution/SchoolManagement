@extends('layout.master')

@section('title')
    {{ __('Upload Sequential Exam marks') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Upload Sequential Exam marks') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                <label for="">{{ __('Sequence') }}</label>
                                <select required name="sequence_id" id="sequence_id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if(isset($exams))
                                        <option value="" hidden="">{{ __('Select Sequence') }}</option>
                                        @foreach ($sequences as $sequence)
                                            <option value="{{ $sequence->id }}"> {{ $sequence->name}}</option>
                                        @endforeach
                                    @else
                                        <option value="">--- {{ __('No Sequence') }} ---</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                <label for="">{{ __('Class') }}</label>
                                <select required name="class_id" id="class_section_dropdown" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if(isset($exams))
                                        <option value="" hidden="">{{ __('Select Class') }}</option>
                                        @foreach ($class_sections as $class_section)
                                            <option value="{{ $class_section->id }}"> {{ $class_section->full_name}}</option>
                                        @endforeach
                                    @else
                                        <option value="">--- {{ __('No Class Section') }} ---</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                <label for="">{{ __('exam') }}</label>

                                <select required name="exam_id" id="exam_id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if(isset($exams))
                                        <option value="" hidden="">{{ __('select') . ' ' . __('exam') }}</option>
                                        @foreach ($exams as $data)
                                            @if(isset($data->exam->timetable[0]))
                                                <option value="{{ $data->exam->id }}" data-class-section="{{$data->class_section_id}}" data-sequence="{{$data->exam->sequence_id}}" data-subject="{{$data->exam->timetable[0]->subject_id}}"> {{ $data->exam->timetable[0]->subject->name .' - '.$data->class_section->full_name}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="">--- {{ __('No Exams') }} ---</option>
                                    @endif
                                </select>

                            </div>
                            <div class="form-group col-sm-12 col-md-12 text-center">
                                <button type="button" id="search" class="btn btn-primary">{{ __('Search') }}</button>
                            </div>
                        </div>
                        {{--@include('exams.upload-marks._change_max_marks') --}}
                        <form action="{{ route('exams.submit-marks') }}" class="create-form mt-4" id="formdata">
                            <input type="hidden" name="sequence_id" id="hidden_sequence_id"/>
                            <input type="hidden" name="class_section_id" id="class_section_id"/>
                            <input type="hidden" name="subject_id" id="exam_subject_id"/>
                            <input type="hidden" name="exam_id" id="submit_marks_exam_id"/>
                            @csrf

                            <div class="show_student_list">
                                <table aria-describedby="mydesc" class='table student_table table_list' id='table_list'
                                       data-toggle="table" data-url="{{ route('exams.marks-list') }}"
                                       data-click-to-select="true" data-side-pagination="server"
                                       data-pagination="false" data-page-list="[5, 10, 20, 50, 100, 200]"
                                       data-search="true" data-show-columns="true" data-show-refresh="true"
                                       data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1"
                                       data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="student_name"
                                       data-sort-order="asc" data-maintain-selected="true" data-export-types='["txt","excel"]'
                                       data-export-options='{ "fileName": "exam-result-list-<?= date(' d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                                       data-query-params="uploadMarksqueryParams" data-toolbar="#toolbar">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no" data-sortable="false" data-visible="false">{{ __('no') }}</th>
                                        <th scope="col" data-field="student_name" data-sortable="true" data-formatter="examStudentNameFormatter">{{ __('name') }}</th>
                                        <th scope="col" data-field="total_marks" data-sortable="false" data-visible="false">{{ __('total_marks') }}</th>
                                        <th scope="col" data-field="obtained_marks" data-sortable="false" data-formatter="obtainedMarksFormatter">{{ __('obtained_marks') }}</th>
                                        {{-- <th scope="col" data-field="teacher_review" data-sortable="false" data-formatter="teacherReviewFormatter">{{ __('teacher_review') }}</th> --}}
                                    </tr>
                                    </thead>
                                </table>
                                <div class="form-group col-sm-12 col-md-5 local-forms mt-5" id="subject_competency_group" style="display: none;">
                                    <label for="subject_competency">{{ __('Competency') }} (optional)</label>
                                    <input type="text" name="subject_competency" id="subject_competency" class="form-control" maxlength="50"
                                           value="{{ isset($subject_competency) && isset($subject_competency->competence) ? $subject_competency->competence : '' }}">
                                </div>
                            @if(auth()->user()->teacher)
                                <!-- Hidden status field for teachers -->
                                    <input type="hidden" name="marks_upload_status" id="marks_upload_status" value="1">
                                @else
                                    <div class="form-group col-sm-12 col-md-2 local-forms mt-3 mb-0">
                                        <label for="">{{ __('Status') }}</label>
                                        <select name="marks_upload_status" id="marks_upload_status" class="form-control">
                                            <option value="1" selected>{{ __('Submitted') }}</option>
                                            <option value="2">{{ __('In Progress') }}</option>
                                            <option value="0">{{ __('Pending') }}</option>
                                        </select>
                                    </div>
                            @endif
                            <!-- Submit button should always be displayed -->
                                <input class="btn btn-primary mt-4" id="create-btn-result" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#search').on('click', function () {
            let sequence_id = $('#sequence_id').val();
            let class_id = $('#class_section_dropdown').val();
            let exam_id = $('#exam_id').val();

            if (sequence_id && class_id && exam_id) {
                $('.show_student_list').show();
                $('#subject_competency_group').show();
                $('.student_table').bootstrapTable('refresh');
            } else {
                alert('Please select Sequence, Class, and Exam before searching.');
            }
        });
        $('#table_list').on('load-success.bs.table', function (e, response) {
            $('#total-marks-update-form').show();
            if (response.error === true) {
                showErrorToast(response.message);
                $('.show_student_list').hide();
            } else {
                $('#total-marks-update-form').show();
                $('#total-marks-update-form #total_marks').val(response.total_marks);
                $('#total-marks-update-form #passing_marks').val(response.passing_marks);
                $('#exam_timetable_id').val(response.timetable_id);

                // Update status field regardless of user type
                if (response.marks_upload_status === undefined || response.marks_upload_status === null || response.marks_upload_status === 0) {
                    $('#marks_upload_status').val('1'); // Default to Submitted
                } else {
                    $('#marks_upload_status').val(response.marks_upload_status); // Use the defined value
                }

                // Update subject competency input
                $('#subject_competency').val(response.subject_competency ? response.subject_competency.competence : '');
            }
        });


        $('#sequence_id,#class_section_dropdown').on('change', function (e) {
            e.preventDefault();

            let sequence_id = $('#sequence_id').val();
            let class_id = $('#class_section_dropdown').val();
            $('#exam_id').val('');

            if (sequence_id && class_id) {
                $('#exam_id').find('option').hide();
                $('#exam_id').find('option[data-class-section="' + class_id + '"][data-sequence="' + sequence_id + '"]').show();

                $.ajax({
                    url: "{{ route('exams.sequential.upload-marks') }}",
                    type: 'GET',
                    data: { sequence_id, class_id },
                    success: function (data) {
                        $('#exam_id').empty().append('<option value="">Sélectionner un examen</option>');
                        data.exams.forEach(exam => {
                            $('#exam_id').append(`<option value="${exam.exam.id}" data-class-section="${exam.class_section_id}" data-sequence="${exam.exam.exam_sequence_id}" data-subject="${exam.exam.timetable[0].subject_id}" >${exam.exam.timetable[0].subject.name}</option>`);
                        });
                    },
                    error: function () {
                        alert('Erreur lors du chargement des examens.');
                    }
                });
            }

            // if (sequence_id && class_id) {
            //     $('#exam_id').find('option').hide();
            //     $('#exam_id').find('option[data-class-section="' + class_id + '"][data-sequence="' + sequence_id + '"]').show();
            // }
        })

        $('#exam_id').on('change', function (e) {
            e.preventDefault();
            let selectedOption = $(this).find('option:selected').data();

            if (selectedOption) {
                $('#class_section_id').val(selectedOption.classSection);
                $('#hidden_sequence_id').val(selectedOption.sequence);
                $('#exam_subject_id').val(selectedOption.subject);
                $('#submit_marks_exam_id').val($(this).val());
            }
        });

        function updateTotalMarksSuccess() {
            $('#total-marks-update-form').hide();
            $('.student_table').bootstrapTable('refresh');
        }
    </script>
@endsection