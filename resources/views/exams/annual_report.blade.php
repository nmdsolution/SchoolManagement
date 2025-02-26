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
                            {{ __('annual_master_sheet') }}
                        </h4>

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
                            queryParams="classReportqueryParams">
                        </x-bootstrap-table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
@endsection
@section('script')
    
@endsection
