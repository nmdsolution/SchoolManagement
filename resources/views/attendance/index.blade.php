@extends('layout.master')

@section('title')
    {{ __('attendance') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('attendance') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('attendance') }}
                        </h4>
                        <form action="{{ route('attendance.store') }}" class="create-form" id="formdata">
                            @csrf
                            <div class="row" id="toolbar">
                                <div class="col">
                                    <label>{{ __('class_section') }}</label>
                                    <select required name="class_section_id" id="timetable_class_section"
                                            class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('select') . ' ' . __('class') }}</option>
                                        @foreach ($class_sections as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="term_id">{{ __('Term') }}</label>
                                    <select name="term_id" id="term_id" class="form-control">
                                        <option value="">--{{ __('Select') }}--</option>
                                        @foreach ($terms as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="show_student_list">
                                <table aria-describedby="mydesc" class='table student_table table_list' id='table_list'
                                       data-toggle="table" data-url="{{ url('student-list') }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="false"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                       data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                       data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1"
                                       data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                       data-sort-order="desc" data-maintain-selected="true" data-export-types='["txt","excel"]'
                                       data-export-options='{ "fileName": "student-absence-list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                                       data-query-params="queryParams">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">
                                            {{ __('id') }}</th>
                                        <th scope="col" data-field="no" data-sortable="true" data-visible="false">{{ __('no') }}</th>

                                        <th scope="col" data-field="student_id" data-sortable="true">
                                            {{ __('student_id') }}</th>
                                        <th scope="col" data-field="admission_no" data-sortable="true">
                                            {{ __('admission_no') }}</th>
                                        <th scope="col" data-field="roll_no" data-sortable="true">{{ __('roll_no') }}
                                        </th>
                                        <th scope="col" data-field="name" data-sortable="false">{{ __('name') }}
                                        </th>
                                        <th scope="col" data-field="absences" data-sortable="false">{{ __('total_absence') }}</th>
                                        <th scope="col" data-field="justified" data-sortable="false">{{ __('justified_absence') }}</th>
                                        <th scope="col" data-field="unjustified" data-sortable="false">{{ __('unjustified_absence') }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <input class="btn btn-primary btn_attendance mt-4" id="create-btn" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function queryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
                'class_section_id': $('#timetable_class_section').val(),
                'term_id': $('#term_id').val()
            };
        }
    </script>

    <script>

        function set_data() {
            $(document).ready(function () {
                student_class = $('#timetable_class_section').val();
                term_id = $('#term_id').val();
                session_year = $('#date').val();
            });
        }

        $('#timetable_class_section,#term_id').on('change', function () {
            set_data();
        });
    </script>
@endsection
