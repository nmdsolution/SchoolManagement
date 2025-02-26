@extends('layout.master')

@section('title')
    {{ __('timetable') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{  __('timetable_defaults') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="repeater">
                            <div class="row">
{{--                                <div class="col-sm-12 col-md-2">--}}
{{--                                    Period <span class="text-danger"> *</span>--}}
{{--                                </div>--}}
                                <div class="col-sm-12 col-md-2">
                                    Start time <span class="text-danger">*</span>
                                </div>
                                <div class="col-sm-12 col-md-2">
                                    End time <span class="text-danger">*</span>
                                </div>
                                <div class="col-sm-12 col-md-2">
                                    <button data-repeater-create type="button" id="addmore"
                                            class="addmore btn btn-gradient-info btn-sm icon-btn ml-2 mb-2">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <form class="pt-3" action="{{url('store-template')}}" id="formdata" method="POST"
                                  novalidate="novalidate">
                                @csrf

                                <div id="timetable-list" class="set_periods hidden" data-repeater-list="list">

                                    @foreach($list as $item)
                                        <div data-repeater-item id="period-item" class="row mb-2 period-item">

{{--                                            <div class="col-sm-12 col-md-2">--}}
{{--                                                <input type="number" disabled name="id" id="id" class="form-control"--}}
{{--                                                       style="width:100%;" tabindex="-1" aria-hidden="true"--}}
{{--                                                       value="{{ isset($item['id']) ? $item['id'] : " " }}"--}}
{{--                                                >--}}
{{--                                            </div>--}}

                                            <div class="col-sm-12 col-md-2">
                                                <input required type="time" name="start_time"
                                                       class="timetable_start_time form-control"
                                                       placeholder="Start time"
                                                       value="{{ $item['start_time'] }}"
                                                >
                                            </div>

                                            <div class="col-sm-12 col-md-2">
                                                <input required type="time" name="end_time"
                                                       class="timetable_end_time form-control" placeholder="End time"
                                                       value="{{ $item['end_time'] }}"
                                                >
                                            </div>

                                            <div class="col-sm-12 col-md-2">
                                                <button data-repeater-delete type="button"
                                                        class="mt-2 row_remove btn btn-gradient-danger btn-sm icon-btn ml-2">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>


                                <input class="mt-3 btn btn-primary" id="create-btn" type="submit"
                                       value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {

            $('.repeater').repeater({});

        })
    </script>
@endsection
