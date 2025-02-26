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
                        <form action="{{ route('exams.submit-marks') }}" class="create-form" id="formdata">
                            <input type="hidden" name="class_id" id="class_id"/>
                            @csrf
                            <div class="row" id="toolbar">
                                <div class="form-group">
                                    @if(isset($exams))
                                        <select required name="exam_id" id="exam_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="" hidden="">{{ __('select') . ' ' . __('exam') }}</option>

                                            @foreach ($exams as $data)
                                                <option value="{{ $data->id }}" data-class="{{implode(',',$data->exam_class_section->pluck('class_id')->toArray())}}"> {{ $data->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select required name="exam_id" id="exam_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="">--- No Exams ---</option>
                                        </select>
                                    @endif
                                </div>
                            </div>

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
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                    <th scope="col" data-field="no" data-sortable="true">{{ __('no') }}</th>
                                    <th scope="col" data-field="student_name" data-sortable="true" data-formatter="examStudentNameFormatter">{{ __('name') }}</th>
                                    <th scope="col" data-field="total_marks" data-sortable="false">{{ __('total_marks') }}</th>
                                    <th scope="col" data-field="obtained_marks" data-sortable="false" data-formatter="obtainedMarksFormatter">{{ __('obtained_marks') }}</th>
                                    {{-- <th scope="col" data-field="teacher_review" data-sortable="false" data-formatter="teacherReviewFormatter">{{ __('teacher_review') }}</th> --}}
                                </tr>
                                </thead>
                            </table>
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
            }
        })
        $('#class_section_id').on('change', function (e) {
            let exam_id = $('#exam_id').val();
            let class_id = $('#class_section_id option:selected').data('class');
            let class_section_id = $('#class_section_id').val();
            $('#class_id').val(class_id);
            // console.log(exam_id, class_id);
            let url = baseUrl + '/exams/get-exam-subjects/' + exam_id + '/' + class_id + '/' + class_section_id;
            // let data = new FormData();
            // data.append('class_id', class_id);

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
        })

        $('#exam_id').on('change', function () {

            $('#exam_class_section_id option').hide();
            let exam_class_section = $(this).find('option:selected').data('class') + '';
            exam_class_section = exam_class_section.split(",")
            $('#class_section_id option').hide();

            $('#class_section_id').val('');
            $(exam_class_section).each(function (index, element) {
                $('#class_section_id option[data-class=' + element + ']').show();
            })
            // $('#exam_class_section_id').find('option[data-class=' + class_id + ']').show();

            // let url = baseUrl + '/exams/get-exam-subjects/' + exam_id;
            //
            // function successCallback(response) {
            //     let html = ''
            //     html = '<option>No Subjects</option>';
            //     if (response.data) {
            //         html = '<option value="">Select Subject</option>';
            //         $.each(response.data, function (key, data) {
            //             html += '<option value=' + data.subject.id + '>' + data.subject.name + ' - ' + data.subject.type + '</option>';
            //         });
            //     } else {
            //         html = '<option>No Subjects Found</option>';
            //     }
            //     $('#exam_subject_id').html(html);
            // }
            //
            // ajaxRequest('GET', url, null, null, successCallback, null);
        });
    </script>
@endsection
