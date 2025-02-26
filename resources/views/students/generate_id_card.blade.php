@extends('layout.master')

@section('title')
    {{ __('students') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('students') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Download ID Card') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="mb-4 local-forms">
                                    @if (!Auth::user()->teacher)
                                        <label for="">{{ __('class_section') }}</label>
                                        <select name="filter_class_section_id" id="filter_class_section_id"
                                                class="form-control">
                                            <option value="">{{ __('select_class_section') }}</option>
                                            @foreach ($class_section as $class)
                                                <option value={{ $class->id }}>
                                                    {{ $class->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('students-list');
                                    $data_response_handler = 'responseHandler';
                                    $columns = [
                                        trans('') => ['data-field' => 'state', 'data-checkbox' => true],
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('user_id') => ['data-field' => 'user_id', 'data-sortable' => false, 'data-visible' => false],
                                        trans('full_name') => ['data-field' => 'first_name', 'data-sortable' => false],
                                        // trans('last_name') => ['data-field' => 'last_name', 'data-sortable' => false],
                                        trans('dob') => ['data-field' => 'dob', 'data-sortable' => false],
                                        trans('image') => ['data-field' => 'image', 'data-sortable' => false, 'data-formatter' => 'imageFormatter'],
                                        trans('class') . ' ' . trans('section') . ' ' . trans('id') => ['data-field' => 'class_section_id', 'data-sortable' => false, 'data-visible' => false],
                                        trans('class') . ' ' . trans('section') => ['data-field' => 'class_section_name', 'data-sortable' => false],
                                        trans('gr_number') => ['data-field' => 'admission_no', 'data-sortable' => false],
                                        trans('roll_no') => ['data-field' => 'roll_number', 'data-sortable' => false],
                                        trans('admission_date') => ['data-field' => 'admission_date', 'data-sortable' => false],
                                    ];
                                    $actionColumn = '';
                                    // $actionColumn = [
                                    //     'editButton' => ['url' => url('students')],
                                    //     'deleteButton' => ['url' => url('students')],
                                    //     'data-events' => 'studentEvents',
                                    // ];
                                @endphp
                                <x-bootstrap-table :url=$url :data_response_handler=$data_response_handler :columns=$columns
                                                   :actionColumn=$actionColumn queryParams="StudentDetailQueryParams" sortName="users.first_name" sortOrder="asc"></x-bootstrap-table>
                            </div>
                            <form action="{{ url('students/generate-id-card') }}" method="post" target="_blank">
                                @csrf
                                <textarea id="user_id" name="user_id" style="display: none"></textarea>
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
                    $('textarea#user_id').val(user_list);
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

@section('js')
    <script type="text/javascript">
        function queryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }
    </script>
@endsection
