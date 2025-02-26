@extends('layout.master')

@section('title')
    {{ __('add_bulk_data') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('students') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" id="create-form-bulk-data" enctype="multipart/form-data"
                              action="{{ route('students.store-bulk-data') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('class') . ' ' . __('section') }}
                                        <span class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section" class="form-control select">
                                        <option value="">{{ __('select') . ' ' . __('class') . ' ' . __('section') }}
                                        </option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-12 col-sm-12 col-md-6">
                                    <div class="form-group files">

                                        <label>{{ __('file_upload') }}</label> <span class="text-danger">*</span>
                                        <input type="file" name="file" class="form-control">
                                        {{-- <div class="uplod">
                                            <label class="file-upload image-upbtn mb-0">
                                                Choose File <input name="file" type="file">
                                            </label>
                                        </div> --}}

                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-xs-12">
                                    <input class="btn btn-primary submit_bulk_file" type="submit" value="{{ __('submit') }}"
                                           name="submit" id="submit_bulk_file">
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="form-group col-12 col-md-4 mt-5">
                            <a class="btn btn-primary col-md-8 col-sm-12" href="{{ url('download/student/sample/file') }}">
                                <strong>{{ __('download_dummy_file') }}</strong>
                            </a>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <span style="font-size: 14px"> <b>{{ __('Note') }} :-
                                </b>{{ __('first_download_dummy_file_and_convert_to_csv_file_then_upload_it') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title mb-4">
                            <h6 class="card-title text-danger">{{__('NOTE : REQUIRED FIELDS')}}</h6>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="row">
                                @foreach ($fixed_fields as $field)
                                    <div class="col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                        <label class="label label-success">{{ $field }}</label>
                                    </div>
                                @endforeach

                                @foreach ($form_fields as $field)
                                    <div class="col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                        <label class="label label-success">{{ str_replace("_"," ",$field->name) }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script></script>
@endsection
