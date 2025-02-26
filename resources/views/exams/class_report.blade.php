@extends('layout.master')

@section('title')
    {{ __('Exam Report') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Class Report') }}
                        </h4>
                        <div class="row" id="toolbar">
                            <div class="row">
                                <div class="col-sm-12 col-md-3">
                                    <label for="class_section_id">{{ __('Exam Term') }}</label>
                                    {!! Form::select('exam_term_id', $exam_terms->prepend(__('annual'), 'annual')->reverse(), null, ['class' => 'form-control select', 'id' => 'exam_term_id']) !!}
                                </div>
                            </div>
                            <div class="row mt-3" id="global-report-row">
                            </div>
                        </div>

                        {{-- <table aria-describedby="mydesc" class='table table-striped table_list' data-toggle="table"
                            data-url="{{ url('class-section-list') }}" data-click-to-select="true"
                            data-side-pagination="server" data-maintain-meta-data="true" data-pagination="false"
                            data-page-list="[5, 10, 20, 50, 100, 200,All]" data-search="true" data-toolbar="#toolbar"
                            data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                            data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                            data-mobile-responsive="true" data-sort-name="rank" data-sort-order="asc"
                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                            data-export-options='{ "fileName": "list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                            data-show-export="true" data-query-params="classReportqueryParams"
                            >
                            <thead>
                                <tr>
                                    <th scope="col" data-field="no" data-sortable="true">{{ __('no') }}</th>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false"> {{ __('id') }}</th>
                                    <th scope="col" data-field="class_section" data-sortable="true"> {{ __('Class Section') }} </th>
                                    <th scope="col" data-field="operate" data-sortable="false" data-action="actionClassReport">{{ __('Action') }}</th>

                                </tr>
                            </thead>
                        </table> --}}

                        <div id="annual-master-sheet" class="d-none">
                            @php
                                $actionColumn = '';
                                $url = route('annual-report-list');
                                $columns = [
                                    trans('no') => ['data-field' => 'no'],
                                    trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                    trans('Class Section') => ['data-field' => 'class_section'],
                                    trans('Action') => ['data-field' => 'action'],
                                ];
                            @endphp
                            <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                                queryParams="classReportqueryParams" data-toolbar="false" id="annual-master-sheet-table">
                            </x-bootstrap-table>
                        </div>

                        <div id="term-master-sheet">
                            @php
                                $actionColumn = '';
                                $url = url('class-section-list');
                                $columns = [
                                    trans('no') => ['data-field' => 'no'],
                                    trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                    trans('Class Section') => ['data-field' => 'class_section'],
                                    trans('Action') => ['data-field' => 'action'],
                                ];
                            @endphp
                            <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                                queryParams="classReportqueryParams" data-toolbar="#toolbar" id="term-master-sheet-table">
                            </x-bootstrap-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const $global_row = $('#global-report-row');

            function toggleTable() {
                if ($('#exam_term_id').val() == 'annual') {
                    $('#term-master-sheet').addClass('d-none');
                    $('#annual-master-sheet').removeClass('d-none');
                    $('#global-report-row').addClass('d-none');
                } else {
                    $('#term-master-sheet').removeClass('d-none');
                    $('#annual-master-sheet').addClass('d-none');
                    $('#global-report-row').removeClass('d-none');
                }

            }

            $('#exam_term_id').on('change', function() {
                toggleTable();
                const exam_term_id = $(this).val();
                console.log('exam_term_id', exam_term_id);
                
                if (exam_term_id == 'annual') {
                    return;
                }
                if (exam_term_id) {
                    $.ajax({
                        url: route('class-report.global-data'),
                        type: 'GET',
                        data: {
                            exam_term_id: exam_term_id
                        },
                        success: function(data) {
                            $global_row.html(data);

                            const title = data.term.name;
                            const link = route('global-report.term', data.term.id);
                            $global_row.append(buildGlobalCard("{{ __('global_report') }}", link));
                            console.log('global_row', $global_row);
                            
                        }
                    });
                } else {
                    $global_row.html('');
                }
            });

            $('#exam_term_id').trigger('change');

            function buildGlobalCard(title, link) {
                const html = `<div class="col-xl-4 col-sm-6 col-12 d-flex">
            <a href="${link}" target="_blank" class="card shadow w-100">
                <div class="card-body" style="padding: 10px">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info text-black">
                            <h6 class="text-black text-bold font-bold"><strong>${title}</strong></h6>
                        </div>
                        <div class="db-icon">
                            <i class="fa fa-book-open"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>`;
                return html;
            }
        });
    </script>
@endsection
