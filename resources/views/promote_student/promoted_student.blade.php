@extends('layout.master')

@section('title')
    {{ __('Promoted Student') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Promoted') . ' ' . __('Student') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">

                        </h4>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row" id="toolbar">
                                        <div class="col-md-6 col-sm-12">
                                            <label for="">{{__('session_year')}}</label>
                                            {!! Form::select('session_year_id', $session_years, null, ['class' =>
                                            'form-control select', 'id' => 'session_year_id']) !!}
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <label for="">{{__('class_section')}}</label>
                                            <select name="class_section_id" id="class_section_id" class="form-control select">
                                                <option value="">{{__('all')}}</option>
                                                @foreach ($class_sections as $class_section)
                                                    <option value="{{ $class_section->id }}">{{ $class_section->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @php
                                        $actionColumn = '';
                                        $url = url('promoted-student-list');

                                        $columns = [
                                            '' => ['data-field' => 'state', 'data-checkbox' => true],
                                            trans('no') => ['data-field' => 'no'],
                                            trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                            trans('student_name') => ['data-field' => 'student_name'],
                                            trans('class_section') => ['data-field' => 'class_section'],
                                            trans('Result') => ['data-field' => 'result','data-formatter' => 'student_result'],
                                            trans('status') => ['data-field' => 'status','data-formatter' => 'student_status'],
                                        ];
                                        $actionColumn = [
    //                                      'editButton' => false,
                                        'deleteButton' => [
                                            'url' => '/student-session'
                                        ],
                                        'customButton'=>[
                                            ['iconClass'=>'feather-file','url'=>url('student/school/certificate'),'title'=>'School Certificate','customClass'=>'school-certificate'],
                                        ],
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="promotedStudentParam"></x-bootstrap-table>

                                <button class="btn btn-outline-primary mt-2" onclick="showConfirmationModal()">
                                    {{ __('cancel_promotion') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="test">
        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
             aria-labelledby="confirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmationModalLabel"> {{ trans("undo_student_promotion") }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans("undo_promotion_confirmation") }}</p>
                        <p class="font-weight-bold">{{ trans("important") }}:</p>
                        <p>
                            {{ trans("promotion_info") }}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{trans('cancel')}}</button>

                        <textarea id="student_session_list" name="student_session_list" style="display:none;"></textarea>

                        <button id="studentSessionCancel" type="button"  class="btn btn-primary">{{ trans ('undo_promotion') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        var $table = $('#table_list')
        var selections = []
        var user_list = [];

        // ensures that the state of each of the rows is maintained.
        function responsHeandler(res) {
            alert(res);
            $.each(res.rows, function(i, row) {
                row.state = $.inArray(row.id, selections) !== -1
            })
            return res
        }

        // listens and takes actions to the different events.
        $(function() {
            // code that runs when the document is ready.
            $table.on('check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table',
                function(e, rowsAfter, rowsBefore) {
                    user_list = [];
                    var rows = rowsAfter
                    if (e.type === 'uncheck-all') {
                        rows = rowsBefore
                    }
                    var ids = $.map(!$.isArray(rows) ? [rows] : rows, function(row) {
                        return row.id
                    })

                    var func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference'
                    selections = window._[func](selections, ids)
                    selections.forEach(element => {
                        user_list.push(element);
                    });
                    $('textarea#student_session_list').val(user_list);
                })
        })
        window.actionEvents = {}

        $('#studentSessionCancel').on('click', function (e) {
            e.preventDefault();

            const studentSessionList = $('textarea#student_session_list').val() ?? ' ';

            const confirmationModal = $('#confirmationModal');

            $.ajax({
                type: "GET",
                url: "{{ url('/student-session/delete') }}",
                data: {
                    'student_session_list': studentSessionList,
                },
                success: function(response) {
                    $('#table_list').bootstrapTable('refresh');
                    confirmationModal.modal('hide');
                    showSuccessToast(response.message);
                },
                error: function(error) {
                    confirmationModal.modal('hide');
                    showErrorToast(error.message);
                },
            });
        })

        function student_result(_value, row) {
            let html;
            if (row.result === 1) {
                html = '<span class="badge rounded-pill badge-soft-success">{{ trans("PASS") }}</span>';
            } else {
                html = '<span class="badge rounded-pill badge-soft-danger">{{ trans('Repeat') }}</span>';
            }
            return html;
        }

        function showConfirmationModal() {
            $("#confirmationModal").modal('show');
        }


        function student_status(_value, row) {
            let html;
            if (row.status === 1) {
                html = '<span class="badge badge-soft-secondary badge-border">{{ trans("continue") }}</span>';
            } else {
                html = '<span class="badge badge-soft-danger badge-border">{{ trans("dismissed") }}</span>';
            }
            return html;
        }


    </script>
@endsection

