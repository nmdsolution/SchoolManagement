@extends('layout.master')

@section('title')
    {{ __('Exam Report') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <form id="generate-report-form" action="{{ route('exam-report.store') }}" method="POST">
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('Exam Report') }}
                            </h4>
                            <small class="text-danger">
                                * {{ __('To Generate Report Select Class & Term. And then Click on Generate Button') }} </small>
                            <div class="row">
                                <div class="col">
                                    <label for="payment_status">{{ __('Payment') }}</label>
                                    <select name="payment_status" id="payment_status" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="2">{{ __('Unpaid') }}</option>
                                        <option value="1">{{ __('Fully Paid') }}</option>
                                        <option value="3">{{ __('Partially Paid') }}</option>

                                    </select>
                                </div>
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
                                <div class="col">
                                    <label for="term_id">{{ __('Term') }}</label>
                                    <select name="term_id" id="term_id" class="form-control">
                                        <option value="">--{{ __('Select') }}--</option>
                                        @foreach ($terms as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                        <option value="annual">{{ __('annual_report') }}</option>
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
                                   data-url="{{ route('list_exam-report.show', 1) }}" data-click-to-select="true"
                                   data-side-pagination="server" data-pagination="true"
                                   data-page-list="[5, 10, 20, 50, 100, 200, All]" data-search="true"
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
                                    <th scope="col" data-field="student_name" data-sortable="true">{{ __('student_id') }}</th>
                                    <th scope="col" data-field="total_fees" data-sortable="false" data-visible="false">{{ __('Total Fees') }}</th>
                                    <th scope="col" data-field="amount_paid" data-sortable="false" data-visible="false">{{ __('Amount Paid') }}</th>
                                    <th scope="col" data-field="payment_status" data-sortable="true" data-formatter="paymentStatusColumnFormatter">{{ __('Payment Status') }}</th>
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
        function paymentStatusColumnFormatter(value, row) {
            if (row.amount_paid == 0 && row.total_fees == 0) {
                return '<span class="badge badge-danger">{{ __("Unpaid") }}</span>';
            } else if (row.amount_paid < row.total_fees) {
                return '<span class="badge badge-warning">{{ __("Partially Paid") }}</span>';
            } else if (row.amount_paid == row.total_fees && row.total_fees > 0) {
                return '<span class="badge badge-success">{{ __("Fully Paid") }}</span>';
            } else {
                return '<span class="badge badge-success">{{ __("Fully Paid") }}</span>';// Handle edge cases
            }
        }
        // Payment status formatter

        function paymentStatusFormatter(value) {
            const statusClasses = {
                0: 'badge bg-danger',
                1: 'badge bg-success',
                2: 'badge bg-warning'
            };
            const statusLabels = {
                3: '{{ __("Partially Paid ") }}',
                1: '{{ __("Fully Paid") }}',
                2: '{{ __("Unpaid") }}'
            };
            return value === null || value === '' ?
                `<span class="badge bg-secondary">{{ __("All") }}</span>` :
                `<span class="${statusClasses[value] || 'badge bg-secondary'}">${statusLabels[value] || '{{ __("Unknown") }}'}</span>`;
        }

        function examReportqueryParams(params) {
            if ($('#payment_status').val() !== '') {
                params.payment_status = $('#payment_status').val();
            } else {
                delete params.payment_status;
            }

            if ($('#class_section_id').val() !== '') {
                params.class_section_id = $('#class_section_id').val();
            } else {
                delete params.class_section_id;
            }

            if ($('#term_id').val() !== '') {
                params.term_id = $('#term_id').val();
            } else {
                delete params.term_id;
            }

            return params;
        }

        $('#payment_status').on('change', function () {
            $('.table_list').bootstrapTable('refresh');
        });

        $('#class_section_id, #term_id').on('change', function () {
            $('.table_list').bootstrapTable('refresh');
        });

        $('#download-btn').on('click', function() {
            let classSection = $('#class_section_id').val();
            let term = $('#term_id').val();
            let paymentStatus = $('#payment_status').val(); // Get the payment status

            if (classSection === '' || term === '') {
                return;
            }

            let url = "{{ route('exam-report-bulk-dowload', [-1, -2, -3]) }}"; // Add a placeholder for payment status
            url = url.replace('-2', classSection);
            url = url.replace('-1', term);
            url = url.replace('-3', paymentStatus); // Replace the payment status placeholder

            window.open(url, '_blank');
        });

        $('#generate-report-form').on('submit', function(e) {
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
        });
    </script>
@endsection