@extends('layout.master')

@section('title')
    {{ __('documents') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
{{--                <div class="card">--}}
{{--                    <div class="card-body">--}}
{{--                        <h4 class="card-title">--}}
{{--                            {{ __('statistics') }}--}}
{{--                        </h4>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('class_grouped_statistics') }}
                        </h4>

                        <div class="w-50 py-3 fw-bold">
                            <span class="fw-bolder">NB:</span class="fw-bolder"> {{__('grouping_info') }}
                        </div>

                        @php
                            $actionColumn = '';
                            $url = route('class-group-list');
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('Class Group Name') => ['data-field' => 'class_group_name'],
                                trans('stats_links') => ['data-field' => 'action'],
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                                               queryParams="statisticsReport">
                        </x-bootstrap-table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
@endsection
@section('script')
    <script>

        function examTermDocsqueryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
                exam_term_id: $('#exam_term_id').val()
            };
        }

        function statisticsReport(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
            };
        }
    </script>
@endsection
