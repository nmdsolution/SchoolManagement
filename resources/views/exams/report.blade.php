@extends('layout.master')

@section('title')
    {{ __('exam') . ' ' . __('Report') }}
@endsection

@section('content')
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title">
                {{ __('exam') . ' ' . __('Report') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 mb-3 local-forms">
                                <label for="">{{ __('exam') }}</label>
                                {!! Form::select('general_exam_id', $exams, $select_value, ['class' => 'form-control select','id' => 'general_exam_id','placeholder' => '-- Select Exam --',]) !!}
                            </div>

                            <div class="col-sm-12 col-md-4 mb-3 local-forms">
                                <label for="">{{ __('Class Group') }}</label>
                                {!! Form::select('class_group_id', $class_groups, null, ['class' => 'form-control select','id' => 'class_group_id','placeholder' => __('Select Class Group'),])!!}
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            {{-- <div class="row"> --}}
            <div class="co-md-12 col-sm-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h4 class="card-title">{{ __('Result') }}</h4>
                            <table class="table">
                                <thead>
                                <th>#</th>
                                <th>{{ __('Total Students') }}</th>
                                <th>{{ __('Total Attempt') }}</th>
                                <th>{{ __('Pass') }}</th>
                                <th>{{ __('Fail') }}</th>
                                </thead>
                                <tbody id="result-data">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- </div> --}}

            {{-- OVERALL GRAPHS --}}
            <div class="col-sm-12 col-md-4">
                <div class="card card-chart">
                    <div class="card-header mb-4">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h5 class="card-title">{{ __('OVERALL PERFORMANCE') }}</h5>
                            </div>
                            <div class="col-4">
                                <ul class="chart-list-out">
                                    <li><span class="circle-pass"></span>{{ __('PASS') }}</li>
                                    <li><span class="circle-fail"></span>{{ __('FAIL') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="height: 355px">
                        <div id="chart-donut-overall"></div>
                    </div>
                </div>
            </div>
            {{-- END OVERALL GRAPHS --}}

            {{-- CLASS WISE GRAPHS --}}
            <div class="col-sm-12 col-md-8">
                <div class="card card-chart">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5 class="card-title">{{ __('CLASS WISE REPORT') }}</h5>
                            </div>
                            <div class="col-6">
                                <ul class="chart-list-out">
                                    <li><span class="circle-pass"></span>{{ __('PASS') }}</li>
                                    <li><span class="circle-fail"></span>{{ __('FAIL') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="bar-chart"></div>
                    </div>
                </div>
            </div>
            {{-- END CLASS WISE GRAPHS --}}
        </div>

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        {{ __('Class Wise Report') }}
                    </h4>
                    <div id="toolbar_a">
                        <div class="row">
                            <select hidden name="class_wise_report" id="class_wise_report"> </select>
                            <select hidden name="filter_class_group" id="filter_class_group"> </select>
                        </div>
                    </div>
                    @php
                        $url = url('class-wise-report');
                        $data_search = false;
                        $table_refresh = false;
                        $table_column = false;
                        $table_export = true;
                        $actionColumn = false;
                        $showPagination = false;
                        $table_id = 'table_class_wise_report';
                        // $toolbar = '#toolbar_a';
                        $toolbar = '';
                        $columns = [
                            trans('no')=>['data-field'=>'no'],
                            trans('Class Name')=>['data-field'=>'class_name'],
                            trans('Total Student')=>['data-field'=>'total_student'],
                            trans('Total Attempt')=>['data-field'=>'total_attempt'],
                            trans('Pass (%)')=>['data-field'=>'pass'],
                            trans('Fail (%)')=>['data-field'=>'fail'],
                            trans('Highest (%)')=>['data-field'=>'highest_per'],
                            trans('Lowest (%)')=>['data-field'=>'lowest_per'],
                        ];
                    @endphp
                    <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn :data_search=$data_search :table_id=$table_id :table_refresh=$table_refresh :table_export=$table_export :toolbar=$toolbar :table_column=$table_column :showPagination=$showPagination queryParams="classWiseReport"></x-bootstrap-table>

                </div>
            </div>
        </div>

        <div class="row">
            <hr>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-sm-12 col-md-4 mt-2 mb-2 local-forms">
                                <label for="">{{ __('exam') }}</label>
                                {!! Form::select('exam_id', $exams, null, [
                                    'class' => 'form-control filter_data select',
                                    'id' => 'exam_id',
                                    'placeholder' => __('Select Exam'),
                                ]) !!}
                            </div>

                            <div class="col-sm-12 col-md-4 mt-2 mb-2 local-forms">
                                <label for="">{{ __('Class Group') }}</label>
                                {!! Form::select('class_group_id', $class_groups, null, [
                                    'class' => 'form-control filter_data select',
                                    'id' => 'top_student_class_group_id',
                                    'placeholder' => __('Select Class Group'),
                                ])!!}
                            </div>

                            <div class="col-sm-12 col-md-2 mt-2 mb-2 local-forms">
                                <label for="">{{ __('class') }}</label>
                                {!! Form::select('class_id', [], null, [
                                    'class' => 'form-control filter_data select',
                                    'id' => 'class_id',
                                    'placeholder' => __('Select Class'),
                                ]) !!}
                            </div>

                            <div class="col-sm-12 col-md-2 mt-2 mb-2 local-forms">
                                <label for="">{{ __('section') }}</label>
                                {!! Form::select('section_id', [], null, [
                                    'class' => 'form-control filter_data select',
                                    'id' => 'section_id',
                                    'placeholder' => __('Select Section'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class="card-title">{{ __('TOP STUDENTS') }}</h5>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-3 mb-2">
                                        {!! Form::text('top', null, [
                                            'class' => 'form-control form-control-sm',
                                            'placeholder' => __('Enter top student'),
                                            'id' => 'top_student',
                                        ]) !!}
                                    </div>
                                    <div id="toolbar">

                                        <select hidden name="exam_id" id="hidden_exam_id"></select>
                                        <select hidden name="section_id" id="hidden_section_id"></select>
                                        <select hidden name="class_id" id="hidden_class_id"> </select>
                                        <select hidden name="top_student_class_group_id" id="hidden_top_student_class_group_id"> </select>
                                    </div>

                                    {{-- GIVE CLASS FOR DIV SCROLL #scroll-div --}}
                                    <div class="">
                                        @php
                                            $url = url('exams/report/top/students/list');
                                            $data_search = false;
                                            $table_refresh = true;
                                            $table_column = false;
                                            $table_export = true;
                                            $actionColumn = false;
                                            $showPagination = false;
                                            $table_id = 'top_student_table_list';
                                            $columns = [
                                                trans('no') => ['data-field' => 'no'],
                                                trans('name') => ['data-field' => 'name'],
                                                trans('total_marks') => ['data-field' => 'total_marks'],
                                                trans('obtained_marks') => ['data-field' => 'obtained_marks'],
                                                trans('percentage') => ['data-field' => 'percentage'],
                                                trans('grade') => ['data-field' => 'grade'],
                                            ];

                                        @endphp
                                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn :data_search=$data_search :table_refresh=$table_refresh :table_id=$table_id :table_export=$table_export :table_column=$table_column :showPagination=$showPagination queryParams="topStudentQueryParams"></x-bootstrap-table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class="card-title">{{ __('PASS') }} / {{ strtoupper(__('FAIL')) }}</h5>
                            </div>
                            <div class="row">
                                <div id="chart-donut-class-section"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class="card-title">{{ __('RATIO BETWEEB BOYS & GIRLS') }}</h5>
                            </div>
                            <div class="row">
                                <div id="chart-donut-gender-ratio"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h4 class="card-tite">{{ __('FAIL STUDENTS') }}</h4>
                        </div>
                        <div class="row">
                            @php
                                $url = url('fail-students-list');

                                $data_search = false;
                                $table_refresh = false;
                                $table_column = false;
                                $table_export = true;
                                $actionColumn = false;
                                // $showPagination = true;
                                $table_id = 'fail_student_list';
                                $columns = [
                                    trans('no') => ['data-field' => 'no'],
                                    trans('name') => ['data-field' => 'name'],
                                ];

                            @endphp
                            <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn :data_search=$data_search :table_id=$table_id :table_refresh=$table_refresh :table_export=$table_export :table_column=$table_column queryParams="failStudentQueryParams"></x-bootstrap-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-10">
                                    <h5 class="card-title">{{ __('SUBJECT HIGHEST') }}</h5>
                                </div>
                                <div class="col-2">
                                    <ul class="chart-list-out">
                                        <li><span class="circle-pass"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table_list table-bordered table-hover mb-0" data-mobile-responsive="true">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('subject') }}</th>
                                    <th>{{ __('name') }}</th>
                                    <th>{{ __('total_marks') }}</th>
                                    <th>{{ __('obtained_marks') }}</th>
                                </tr>
                                </thead>
                                <tbody id="highest">


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-10">
                                    <h5 class="card-title">{{ __('SUBJECT LOWEST') }}</h5>
                                </div>
                                <div class="col-2">
                                    <ul class="chart-list-out">
                                        <li><span class="circle-fail"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table_list table-bordered table-hover mb-0" data-mobile-responsive="true">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('subject') }}</th>
                                    <th>{{ __('name') }}</th>
                                    <th>{{ __('total_marks') }}</th>
                                    <th>{{ __('obtained_marks') }}</th>
                                </tr>
                                </thead>
                                <tbody id="lowest">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- </div> --}}
@endsection

@section('js')
    <script>

        // window.onload = $("#general_exam_id").prop("selectedIndex", 1).val().trigger('change');
        window.onload = setTimeout(() => {
            // $('#general_exam_id').trigger('change');

        }, 1000);
        setTimeout(() => {
            window.onload = $('#general_exam_id').trigger('change');
        }, 500);


        $('.filter_data').change(function () {
            var exam_id = $('#exam_id').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_id').val();
            var top_student_class_group_id = $('#top_student_class_group_id').val();

            if (exam_id != null) {
                $('#hidden_exam_id').html("<option value=" + exam_id + ">" + exam_id + "</option>");
                $('#hidden_exam_id').val(exam_id).trigger('change');
            }

            if (class_id != null) {
                $('#hidden_class_id').html("<option value=" + class_id + ">" + class_id + "</option>");
                $('#hidden_class_id').val(class_id).trigger('change');
            }

            if (section_id != null) {
                $('#hidden_section_id').html("<option value=" + section_id + ">" + section_id + "</option>");
                $('#hidden_section_id').val(section_id).trigger('change');
            }

            if (top_student_class_group_id != null) {
                $('#hidden_top_student_class_group_id').html("<option value=" + top_student_class_group_id + ">" + top_student_class_group_id + "</option>");
                $('#hidden_top_student_class_group_id').val(top_student_class_group_id).trigger('change');
            }

            setTimeout(() => {
                $('#top_student_table_list').bootstrapTable('refresh');
                $('#fail_student_list').bootstrapTable('refresh');
            }, 1000);


            if (exam_id) {
                var url = baseUrl + '/subject/highest-lowest/' + exam_id + '/' + class_id + '/' + section_id;
                $.ajax({
                    type: "get",
                    url: url,
                    success: function (response) {
                        let html = '';
                        let i = 1;
                        // HIGHEST DATA
                        $.each(response.data.highest, function (index, data) {
                            let student_names = [];
                            let total_marks = 0;
                            let obtained_marks = 0;
                            $.each(data.exam_marks, function (indexInArray, exam_mark) {
                                student_names.push(" " + exam_mark.student.user.full_name)
                                total_marks = exam_mark.timetable.total_marks;
                                obtained_marks = exam_mark.obtained_marks;
                            });
                            html += '<tr>';
                            html += '<td>' + i++ + '</td>';
                            html += '<td>';
                            html += data.name;
                            html += '</td>';
                            html += '<td class="text-wrap">';
                            html += student_names;
                            html += '</td>';
                            html += '<td class="text-end">';
                            html += total_marks;
                            html += '</td>';
                            html += '<td class="text-end">';
                            html += obtained_marks;
                            html += '</td>';
                            html += '</tr>';
                        });
                        $('#highest').html(html);
                        html = '';
                        i = 1;
                        // LOWEST DATA
                        $.each(response.data.lowest, function (index, data) {
                            let student_names = [];
                            let total_marks = 0;
                            let obtained_marks = 0;
                            $.each(data.exam_marks, function (indexInArray, exam_mark) {
                                student_names.push(" " + exam_mark.student.user.full_name)
                                total_marks = exam_mark.timetable.total_marks;
                                obtained_marks = exam_mark.obtained_marks;
                            });
                            html += '<tr>';
                            html += '<td>' + i++ + '</td>';
                            html += '<td>';
                            html += data.name;
                            html += '</td>';
                            html += '<td class="text-wrap">';
                            html += student_names;
                            html += '</td>';
                            html += '<td class="text-end">';
                            html += total_marks;
                            html += '</td>';
                            html += '<td class="text-end">';
                            html += obtained_marks;
                            html += '</td>';
                            html += '</tr>';
                        });
                        $('#lowest').html(html);

                        class_wise(response.data.pass, response.data.fail)
                        gender_wise(response.data.total_male, response.data.total_female)

                    }
                });
            }

        })

        // Top student list base on enter
        $('#top_student').keyup(function (e) {
            e.preventDefault();
            $('#top_student').val($(this).val()).trigger('change');
            setTimeout(() => {
                $('#top_student_table_list').bootstrapTable('refresh');
            }, 500);
        });
        // 

        // OVERALL EXAM GRAPH
        $('#general_exam_id,#class_group_id').change(function (e) {
            e.preventDefault();
            var exam_id = $('#general_exam_id').val();
            var class_group_id = $('#class_group_id').val();
            if (class_group_id) {
                $('#filter_class_group').html("<option value=" + class_group_id + ">" + class_group_id + "</option>");
                setTimeout(() => {
                    $('#filter_class_group').val(class_group_id).trigger('change');
                }, 500);
            } else {
                $('#filter_class_group').html("<option value=''>''</option>");
                setTimeout(() => {
                    $('#filter_class_group').val('').trigger('change');
                }, 500);
            }
            $('#class_wise_report').html("<option value=" + exam_id + ">" + exam_id + "</option>");

            setTimeout(() => {
                $('#class_wise_report').val(exam_id).trigger('change');
            }, 500);


            setTimeout(() => {
                $('#table_class_wise_report').bootstrapTable('refresh');
            }, 1000);

            var pass = fail = class_name = class_wise_fail = class_wise_pass = total_students = total_exam_attempt = total_pass = total_fail = 0;

            var url = baseUrl + '/overall/result/' + exam_id + '/' + class_group_id;
            $.ajax({
                type: "get",
                url: url,
                success: function (response) {
                    pass = response.data.pass;
                    fail = response.data.fail;
                    class_name = response.data.class;
                    class_wise_pass = response.data.class_wise_pass;
                    class_wise_fail = response.data.class_wise_fail;
                    if (response.data.total_students) {
                        total_students = response.data.total_students;
                    }
                    if (response.data.total_attempt_exams) {
                        total_exam_attempt = response.data.total_attempt_exams;
                    }
                    if (response.data.total_pass) {
                        total_pass = response.data.total_pass;
                    }
                    if (response.data.total_fail) {
                        total_fail = response.data.total_fail;
                    }
                    let html = '';
                    setTimeout(() => {
                        html += '<tr>';
                        html += '<td>1</td>';
                        html += '<td>' + total_students + '</td>';
                        html += '<td>' + total_exam_attempt + '</td>';
                        html += '<td>' + total_pass + '</td>';
                        html += '<td>' + total_fail + '</td>';
                        html += '</tr>';
                        $('#result-data').html(html);
                        barchart(class_name, class_wise_pass, class_wise_fail)
                        orverall(pass, fail)
                    }, 500);
                }
            });
        });
    </script>
@endsection
