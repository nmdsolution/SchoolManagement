@extends('layout.master')

@section('title')
    {{ __('et_documents') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('et_documents') }}
                        </h4>
                        <div class="row" id="toolbar">
                            <div class="col-sm-12 col-md-3">
                                <label for="class_section_id">{{ __('Exam Term') }}</label>
                                {!! Form::select('exam_term_id', $exam_terms, null, ['class' => 'form-control select', 'id' => 'exam_term_id']) !!}
                            </div>
                        </div>

                        @php
                            $actionColumn = '';
                            $url = url('class-doc-list');
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('Class Section') => ['data-field' => 'class_section'],
                                trans('Action') => ['data-field' => 'action'],
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                            queryParams="examTermDocsqueryParams">
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
    </script>
@endsection
