@extends('layout.master')

@section('title')
    {{ __('promote_student') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('promote_students_in_next_session') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('promote-student.store') }}" class="create-form" id="formdata">
                            @csrf
                            <div class="row" id="toolbar">
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('class') }} {{ __('section') }}
                                        <span class="text-danger">*</span></label>
                                    <select required name="class_section_id" id="student_class_section"
                                        class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('select') . ' ' . __('class') }}</option>
                                        @foreach ($class_sections as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('promote_in') }} <span class="text-danger">*</span></label>
                                    <select required name="session_year_id" id="session_year_id"
                                        class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('select') . ' ' . __('session_years') }}</option>
                                        @foreach ($session_year as $years)
                                            <option value="{{ $years->id }}">{{ $years->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('promote_class') }} <span class="text-danger">*</span></label>
                                    <select required name="new_class_section_id" id="new_student_class_section"
                                        class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('select') . ' ' . __('class') }}</option>
                                        @foreach ($class_sections as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @php
                                // $url = url('promote-student-list');
                                $url = url('promote-student-list');
                                $showPagination = false;
                                $data_response_handler = 'responseHandler';
                                $columns = [
                                    'CHECK' => ['data-field' => 'state', 'data-checkbox' => true],
                                    trans('no') => ['data-field' => 'no'],
                                    trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                    '' => ['data-field' => 'student_id', 'data-visible' => true],
                                    trans('name') => ['data-field' => 'name', 'data-sortable' => true],
                                    trans('result') => ['data-field' => 'result'],
                                    trans('status') => ['data-field' => 'status'],
                                ];
                                $actionColumn = false;
                            @endphp
                            <x-bootstrap-table :url=$url :showPagination=$showPagination :actionColumn=$actionColumn
                                :data_response_handler=$data_response_handler :columns=$columns sortOrder="ASC"
                                queryParams="PromotequeryParams"></x-bootstrap-table>

                            <textarea id="user_id" name="user_id" style="display: none"></textarea>

                            <input class="btn btn-primary btn_promote" id="create-btn" type="submit"
                                value={{ __('submit') }}>
                        </form>
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
    <script>

        $('.btn_promote').hide();

        function set_data() {
            $(document).ready(function() {
                student_class = $('#student_class_section').val();
                session_year = $('#session_year_id').val();
                promote_class = $('#new_student_class_section').val();

                if (student_class != '' && session_year != '' && promote_class != '') {
                    $('.btn_promote').show();
                } else {
                    $('.btn_promote').hide();
                }
            });
        }

        $('#student_class_section,#session_year_id,#new_student_class_section').on('change', function() {
            set_data();
        });

        var $table = $('#table_list')
        var selections = []
        var user_list = [];

        // ensures that the state of each of the rows is maintained.
        function responseHandler(res) {
            $.each(res.rows, function(i, row) {
                row.state = $.inArray(row.id, selections) !== -1
            })
            return res
        }

        // listens and takes actions to the different events.
        $(function() {
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
                    $('textarea#user_id').val(user_list);
                })
        })

        // --------------------------------
        var $button2 = $('#filter_class_section_id')
        $(function() {

            $button2.change(function() {
                $table.bootstrapTable('uncheckAll')
            })
        })

        window.actionEvents = {};
    </script>
@endsection
