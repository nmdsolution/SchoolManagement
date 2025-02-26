@extends('layout.master')

@section('title')
    {{ __('Exam Report') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <form id="generate-report-form" action="{{ route('annual-report.store') }}" method="POST">
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('annual_report') }}
                            </h4>
                            <small class="text-danger">
                                * {{ __('To Generate Report Select Class & Term. And then Click on Generate Button') }} </small>
                            <div class="row">
                                <div class="col">
                                    <label for="class_section_id">{{ __('class') }}</label>
                                    <select name="class_section_id" id="class_section_id" class="form-control">
                                        <option value="">--{{ __('Select') }}--</option>

                                        @foreach ($class_sections as $class_section)
                                            <option value="{{ $class_section->id }}">
                                                {{ $class_section->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row justify-content-center mt-4">
                                    <div class="form-group col-md-2 d-flex justify-content-center">
                                        <input type="submit" class="btn btn-primary" value={{ __('Generate') }} />
                                    </div>
                                    <div class="form-group col-md-2 d-flex justify-content-center">
                                        <button type="button" id="download-btn" class="btn btn-success text-white">
                                            {{ __('bulk_download') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <table aria-describedby="mydesc" class='table table-striped table_list' data-toggle="table"
                                   data-url="{{ route('annual-report.show', 1) }}" data-click-to-select="true"
                                   data-side-pagination="server" data-maintain-meta-data="true" data-pagination="true"
                                   data-page-list="[5, 10, 20, 50, 100, 200,All]" data-search="true"
                                   data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                   data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1"
                                   data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="rank"
                                   data-sort-order="asc" data-maintain-selected="true"
                                   data-export-types='["txt","excel"]'
                                   data-export-options='{ "fileName": "list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                                   data-show-export="true" data-query-params="examReportqueryParams">
                                <thead>
                                <tr>
                                    <th scope="col" data-field="rank" data-sortable="true">{{ __('Rank') }}</th>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                    <th scope="col" data-field="student_name" data-sortable="true" >{{ __('student_id') }}</th>
                                    <th scope="col" data-field="avg" data-sortable="true">{{ __('Avg') }}</th>
                                    <th scope="col" data-field="created_at" data-visible="false" data-sortable="false">{{ __('created_at') }}</th>
                                    <th scope="col" data-field="updated_at" data-visible="false" data-sortable="false">{{ __('updated_at') }}</th>
                                    <th scope="col" data-field="operate" data-sortable="false" data-action="examReportEvent">{{ __('Action') }}</th>
                                </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script>
        $('#class_section_id').on('change', function () {
            $('.table_list').bootstrapTable('refresh');
        })

        $('#download-btn').on('click', function(){
            if($('#class_section_id').val()=='') return;

            let url = "{{ route('annual-report-bulk-dowload', [-2]) }}"

            url = url.replace('-2', $('#class_section_id').val())

            window.open(url, '_blank');
        });

        $('#generate-report-form').on('submit', function (e) {
            e.preventDefault();
            let formElement = $(this);
            let submitButtonElement = $(this).find('#submit-btn');
            let url = $(this).attr('action');
            let data = new FormData(this);
            $('#download-btn').attr('disabled', true);

            function successCallback(response) {
                $('.table_list').bootstrapTable('refresh');
                $('#download-btn').attr('disabled', false);
            }

            formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
        })
    </script>
@endsection