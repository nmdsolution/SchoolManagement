@extends('layout.master')

@section('title')
    {{ __('exam_marks') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('exam_marks') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('exam_marks') }}
                        </h4>
                        <form action="{{ route('exams.submit-marks') }}" class="create-form mt-4" id="formdata">
                            <input type="hidden" name="class_section_id" id="class_section_id"/>
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label for="">{{ __('exam') }}</label>
                                    @if(isset($exams))
                                        <select required name="exam_id" id="exam_id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="" hidden="">{{ __('select') . ' ' . __('exam') }}</option>
                                            @foreach ($exams as $data)
                                                <option value="{{ $data->exam->id }}" data-class-section="{{$data->class_section_id}}"> {{ $data->exam->name.' - '.$data->class_section->full_name}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select required name="exam_id" id="exam_id" class="form-control " style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="">--- {{ __('No Exams') }} ---</option>
                                        </select>
                                    @endif
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label for="">{{ __('subject') }}</label>
                                    <select required name="subject_id" id="exam_subject_id" class="form-control " style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('select_subject') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-12 text-center">
                                    <button type="button" id="search" class="btn btn-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                            <div class="show_student_list">
                                <table aria-describedby="mydesc" class='table student_table table_list' id='table_list'
                                       data-toggle="table" data-url="{{ route('exams.marks-list') }}"
                                       data-click-to-select="true" data-side-pagination="server"
                                       data-pagination="false" data-page-list="[5, 10, 20, 50, 100, 200]"
                                       data-search="true" data-show-columns="true" data-show-refresh="true"
                                       data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1"
                                       data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                       data-sort-order="desc" data-maintain-selected="true" data-export-types='["txt","excel"]'
                                       data-export-options='{ "fileName": "exam-result-list-<?= date(' d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                                       data-query-params="uploadMarksqueryParams" data-toolbar="#toolbar">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="false" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no" data-sortable="true">{{ __('no') }}</th>
                                        <th scope="col" data-field="student_name" data-sortable="true" data-formatter="examStudentNameFormatter">{{ __('name') }}</th>
                                        <th scope="col" data-field="total_marks" data-sortable="false">{{ __('total_marks') }}</th>

                                        <th scope="col" data-field="obtained_marks" data-sortable="false" data-formatter="obtainedMarksFormatter">{{ __('obtained_marks') }}</th>

                                        {{-- <th scope="col" data-field="teacher_review" data-sortable="false" data-formatter="teacherReviewFormatter">{{ __('teacher_review') }}</th> --}}
                                    </tr>
                                    </thead>
                                </table>
                                <div class="form-group col-sm-12 col-md-2 local-forms mt-3 mb-0">
                                    <label for="">{{ __('Status') }}</label>
                                    <select name="marks_upload_status" id="marks_upload_status" class="form-control">
                                        <option value="0">{{ __('Pending') }}</option>
                                        <option value="2">{{ __('In Progress') }}</option>
                                        <option value="1">{{ __('Submitted') }}</option>
                                    </select>
                                </div>
                                <input class="btn btn-primary mt-4" id="create-btn-result" type="submit" value={{ __('submit') }}>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#search').on('click , input', function () {
            $('.show_student_list').show();
            $('.student_table').bootstrapTable('refresh');
        });
        $('#table_list').on('load-success.bs.table', function (e, response) {
            if (response.error == true) {
                showErrorToast(response.message);
                $('.show_student_list').hide();
            } else {
                $('#marks_upload_status').val(response.marks_upload_status);
            }
        })

        $('#exam_id').on('change', function () {
            let exam_id = $(this).val();
            let class_section_id = $(this).find('option:selected').data('class-section');
            $('#class_section_id').val(class_section_id)
            let url = baseUrl + '/exams/get-exam-subjects/' + exam_id + '/' + class_section_id;

            function successCallback(response) {
                let html = ''
                html = '<option>No Subjects</option>';
                if (response.data) {
                    html = '<option value="" hidden="">Select Subject</option>';
                    $.each(response.data, function (key, data) {
                        html += '<option value=' + data.subject.id + '>' + data.subject.name + ' - ' + data.subject.type + '</option>';
                    });
                } else {
                    html = '<option>No Subjects Found</option>';
                }
                $('#exam_subject_id').html(html);
            }

            ajaxRequest('GET', url, null, null, successCallback, null);
        });
    </script>
@endsection
