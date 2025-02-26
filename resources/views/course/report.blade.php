@extends('layout.master')
@section('title')
    {{ __('Course Report') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Course Report') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Course Report') }}
                        </h4>
                        <div class="row">
                            <div class="row" id="toolbar">

                                <div class="col-sm-12 col-md-2 mb-3">
                                    {!! Form::select(
                                        'filter',
                                        ['1' => __('Today'), '2' => __('Weekly'), '3' => __('Monthly'), '4' => __('Yearly'), '0' => __('All')],
                                        null,
                                        ['class' => 'form-control select', 'id' => 'filter_table_data'],
                                    ) !!}
                                </div>

                                <div class="col-sm-12 col-md-2 mb-3">
                                    {!! Form::text('start_date', null, ['class' => 'form-control datetimepicker', 'placeholder' => __('Start Date'), 'id' => 'start_date']) !!}
                                </div>
                                <div class="col-sm-12 col-md-2 mb-3">
                                    {!! Form::text('end_date', null, [
                                        'class' => 'form-control datetimepicker',
                                        'placeholder' => __('End Date'),
                                        'id' => 'end_date',
                                    ]) !!}
                                </div>

                                <div class="col-sm-12 col-md-2 mb-3">
                                    <button class="btn btn-primary" id="search">{{ __('Search') }}</button>
                                    <button class="btn btn-secondary" id="reset">{{ __('Reset') }}</button>
                                </div>


                            </div>

                            <div class="col-12">
                                @php
                                    $url = url('/course/report/detail');
                                    $columns = [
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('Course Name') => ['data-field' => 'course_name'],
                                        trans('Price') => ['data-field' => 'price'],
                                        trans('Date') => ['data-field' => 'date'],
                                        trans('Student Name') => ['data-field' => 'student_name'],
                                        trans('Center Name') => ['data-field' => 'center_name'],
                                    ];
                                    $actionColumn = false;
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                                    queryParams="courseReportParam">
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
        $('#search').click(function (e) { 
            e.preventDefault();
            $('#table_list').bootstrapTable('refresh');
        });

        $('#reset').click(function (e) { 
            $('#start_date').val('');
            $('#end_date').val('');
            $('#filter_table_data').val(1).trigger('change');
            $('#table_list').bootstrapTable('refresh');
        });
    </script>
@endsection
