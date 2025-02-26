@extends('layout.master')

@section('title')
    {{ __('Specific Exam') }}
@endsection

@section('content')
    <div class="content-wrapper">
        @can('create-specific-exam')
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                {{ __('Specific Exam') }}
                            </h4>
                            <form class="pt-3 mt-6 add-exam-form create-form" data-success-function="createExamSuccess" method="POST" action="{{ url('exams') }}">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12 local-forms">
                                        <label>{{ __('exam_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" placeholder="{{ __('exam_name') }}" class="form-control"/>
                                    </div>

                                    @if (isset($class_sections))
                                        <div class="form-group col-sm-12 col-md-12">
                                            <label>{{ __('class') }}<span class="text-danger">*</span></label>
                                            <br>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="select-all-class-section"/>
                                                    {{ __('Select All') }}
                                                </label>
                                            </div>
                                            <select name="class_section_id[]" id="class_section_id" class="form-control select" multiple>
                                                @foreach ($class_sections as $class_section)
                                                    <option value="{{$class_section['id']}}">
                                                        {{$class_section['full_name']}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="form-group col local-forms">
                                        <label>{{ __('exam_description') }}</label>
                                        <textarea id="description" name="description" placeholder="{{ __('exam_description') }}" class="form-control"></textarea>
                                    </div>
                                </div>

                                <input class="btn btn-primary" id="add-exam-btn" type="submit" value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    @endcan
    @can('list-specific-exam')
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{__("List Exams") }}
                        </h4>
                        <div id="toolbar">
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
                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table table-striped table_list' id="table_list" data-toggle="table" data-url="{{ route('exams.show', 1) }}"
                               data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc" data-maintain-selected="true" data-export-types='["txt","excel"]' data-export-options='{ "fileName": "exam-list-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}' data-show-export="true"
                               data-detail-formatter="examListFormatter" data-query-params="examsQueryParams"
                               data-toolbar="#toolbar">
                            <thead>
                            <tr>

                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no" data-sortable="false">{{ __('no') }}</th>
                                <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>
                                <th scope="col" data-field="description" data-sortable="true">{{ __('description') }}</th>
                                <th scope="col" data-field="class_name" data-sortable="false">{{ __('class') }}</th>
                                <th scope="col" data-field="publish" data-sortable="true" data-formatter="examPublishFormatter">{{ __('publish') }}</th>
                                <th scope="col" data-field="session_year_name" data-sortable="false">{{ __('session_years') }}</th>
                                <th scope="col" data-field="timetable" data-formatter="marksUploadStatus" data-sortable="false">{{ __('Mark Upload Status') }}</th>
                                <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>
                                @role('Center')
                                <th scope="col" data-field="operate" data-sortable="false" data-events="examEvents">{{ __('action') }}</th>
                                @endrole
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan
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