@extends('layout.master')

@section('title')
    {{ __('Centers') }}
@endsection

@section('content')
    <div class="content-wrapper">
{{--        <div class="page-header">--}}
{{--            <h3 class="page-title">--}}
{{--                {{ __('clone_center') }}--}}
{{--            </h3>--}}
{{--        </div>--}}

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('clone_center') }}
                        </h4>
                        <form class="center-validate-form create-form pt-3" id="formdata"
                              data-success-function="successFunction" action="{{ route('centers.clone.store') }}" method="POST" novalidate="novalidate">
                            @csrf

                            <div class="separator mb-5"><span class="h5">{{ __('center_details') }}</span></div>

                            <div class="row">

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('From Center') }} <span class="text-danger">*</span></label>
                                    <select name="from_center_id" id="from_center" class="form-control">
                                        @foreach ($centers as $key => $center)
                                            <option value="{{ $key }}"> {{  $center }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('To Center') }} <span class="text-danger">*</span></label>
                                    <select name="to_center_id" id="to_center" class="form-control">
                                        @foreach ($centers as $key => $center)
                                            <option value="{{ $key }}"> {{  $center }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Medium') }} <span class="text-danger">*</span></label>
                                    <select  name="medium_id" id="medium_id" class="form-control">
                                        @foreach ($mediums as $key => $medium)
                                            <option value="{{ $key }}"> {{  $medium }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="d-flex flex-column ">
                                <div class="bg-danger w-50 rounded-3 py-2 d-flex align-items-center
                                justify-content-center text-white">
                                    You can only clone to an empty School.
                                </div>

                                @php

                                    $list = [
                                        "Classes and Their Subjects",
                                        "Sections and Class Subject",
                                        "Default timetable",
                                        "Streams",
                                        "Groups",
                                        "Roles and Permissions",
                                        "Exam Grades",
                                        "Report Card Settings",
                                        "Result Subject Group",
                                    ];

                                @endphp

                                <div class="row gap-md-1 my-4">
                                    @foreach($list as $item)
                                        <div class="col-md-4 text-primary">
                                            {{ $item }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <input class="btn btn-outline-primary px-4 py-2 mt-4" type="submit" value=" {{ __('clone_center_information') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function successFunction(response) {
            Swal.fire(
                'Center Cloned Successfully!'
            )
        }

        $('#from_center').on('change', function (){

        })
    </script>
@endsection