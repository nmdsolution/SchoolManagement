
@extends('layout.master')

@section('title')
    {{ __('Sequential Exam') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{__("List Sequential Exam") }}
                        </h4>

{{--                        <livewire:sequential-exam-create :class_sections="$class_sections" :exam_terms="$exam_terms" />--}}

                        <div id="toolbar" class="mt-4">
                            <div class="row">
                                <div class="col-sm-12 col-md-2 mb-4 local-forms">
                                    <label for="">{{ __('class_section') }}</label>
                                    <select name="filter_class_section_id" id="filter_class_section_id" class="form-control">
                                        <option value="">{{ __('select_class_section') }}</option>
                                        @foreach ($class_sections as $class)
                                            <option value={{ $class->id }}>{{ $class->full_name  }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-2 mb-4 local-forms">
                                    <label for="">{{ __('Sequence') }}</label>
                                    <select name="filter_sequence_id" id="filter_sequence_id" class="form-control">
                                        <option value="">{{ __('Select Sequence') }}</option>
                                        @foreach ($sequences as $sequence)
                                            <option value={{ $sequence->id }}>{{ $sequence->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table table-striped table_list' id="table_list" data-toggle="table" data-url="{{ route('exams.sequential.show') }}"
                               data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc" data-maintain-selected="true" data-export-types='["txt","excel"]' data-export-options='{ "fileName": "sequential-exam-list-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}' data-show-export="true"
                               data-detail-formatter="sequentialExamListFormatter" data-detail-view="true" data-query-params="examsQueryParams"
                               data-toolbar="#toolbar">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no" data-sortable="false">{{ __('no') }}</th>
                                <th scope="col" data-field="class_name" data-sortable="false">{{ __('class') }}</th>
                                <th scope="col" data-field="term_name" data-sortable="false">{{ __('Term') }}</th>
                                <th scope="col" data-field="sequence_name" data-sortable="false">{{ __('Sequence') }}</th>
                                <th scope="col" data-field="teacher_status" data-formatter="statusFormatter" data-sortable="false">{{ __('Teacher Status') }}</th>
                                <th scope="col" data-field="student_status" data-formatter="statusFormatter" data-sortable="false">{{ __('Student Status') }}</th>
                                <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-sortable="false" data-events="sequentialExamEvents">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function createExamSuccess(response) {
            if (!response.error) {
                $('#class_section_id').val('').trigger('change');
            }
        }
    </script>
@endsection