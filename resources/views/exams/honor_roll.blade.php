@extends('layout.master')

@section('title')
    {{ __('Honor Roll') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Honor Roll') }}
                        </h4>
                        
                        {{-- @php
                            $actionColumn = '';
                            $url = url('honor-roll-student-list',$report_id);
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('Name') => ['data-field' => 'student_name'],
                                trans('Action') => ['data-field' => 'action'],
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                            queryParams="honorRollParams">
                        </x-bootstrap-table> --}}


                        @php
                            $actionColumn = '';
                            $url = url('honor-roll-student-list',$report_id);
                            $actionColumn = false;
                            $columns = [
                                trans('') => ['data-field' => 'state', 'data-checkbox' => true],
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('Name') => ['data-field' => 'student_name'],
                                // trans('Action') => ['data-field' => 'action'],
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                            queryParams="honorRollParams">
                        </x-bootstrap-table>
                        <form action="{{ url('students/honor-roll-certificates') }}" method="post">
                            @csrf
                            <textarea id="exam_report_id" name="exam_report_id" style="display: none"></textarea>
                            <input type="submit" class="btn btn-theme mt-4" value="{{ __('submit') }}">
                        </form>

                    </div>
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
    var $table = $('#table_list')
    var selections = []
    var user_list = [];

    function responseHandler(res) {
        $.each(res.rows, function (i, row) {
            row.state = $.inArray(row.id, selections) !== -1
        })
        return res
    }

    $(function () {
        $table.on('check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table',
            function (e, rowsAfter, rowsBefore) {
                user_list = [];
                var rows = rowsAfter
                if (e.type === 'uncheck-all') {
                    rows = rowsBefore
                }
                var ids = $.map(!$.isArray(rows) ? [rows] : rows, function (row) {
                    return row.id
                })

                var func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference'
                selections = window._[func](selections, ids)
                selections.forEach(element => {
                    user_list.push(element);
                });
                $('textarea#exam_report_id').val(user_list);
            })
    })

    // --------------------------------
    var $button2 = $('#filter_class_section_id')
    $(function () {

        $button2.change(function () {
            $table.bootstrapTable('uncheckAll')
        })
    })

    window.actionEvents = {};
</script>

@endsection
