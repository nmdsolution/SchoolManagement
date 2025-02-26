@extends('layout.master')
@section('title')
    {{ __('Course') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Course') }}
            </h3>
        </div>
        @if (Auth::user()->hasRole('Super Admin'))
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('Edit Courses') }}
                            </h4>
                            <div class="separator mb-5"><span class="h5">Course</span></div>
                            {!! Form::model($course, [
                                'route' => ['course.update', $course->id],
                                'method' => 'PUT',
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Course name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('Name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Price') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('price', null, ['required', 'placeholder' => __('Price'), 'class' => 'form-control','min'=>1]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Duration') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('duration', null, ['required', 'placeholder' => __('Duration'), 'class' => 'form-control','min'=>1]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Super Teacher') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('super_teacher_ids[]', $super_teachers, $course->course_teacher->pluck('user_id'), [
                                        'multiple',
                                        'class' => 'form-control js-example-basic-single select2-hidden-accessible','style' => 'width:100%'
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('description', null, [
                                        'required',
                                        'placeholder' => __('Description'),
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <label for="">{{__("image")}}</label>
                                    {!! Form::file('thumbnail', ['class' => 'form-control']) !!}
                                    <img src="{{ $course->thumbnail }}" class="mt-2" height="120" alt="">
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Tags') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('tags', null, [
                                        'placeholder' => __('tags'),
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>

                            </div>

                            <div class="row mt-3">
                                <div class="separator mb-5"><span class="h5">Section</span></div>
                            </div>

                            <div class="old_files mb-3">
                                @php
                                    $i = 0;
                                @endphp

                                @foreach ($course->course_section as $section)
                                    <div class="row mb-3">
                                        {!! Form::hidden('old_file[' . $i . '][section_id]', $section->id) !!}
                                        <div class="col-sm-12 col-md-6">
                                            <label for="">Title <span class="text-danger">*</span></label>
                                            {!! Form::text('old_file[' . $i . '][title]', $section->title, ['required','class' => 'form-control']) !!}
                                        </div>

                                        <div class="col-sm-12 col-md-6">
                                            <label for="">Description</label>
                                            {!! Form::textarea('old_file[' . $i . '][section_description]', $section->description, [
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>

                                        {!! Form::hidden('old_file[' . $i . '][id]', $section->file->id) !!}
                                        <div class="col-sm-12 col-md-4">
                                            <label for="">File Name</label>
                                            {!! Form::text('old_file[' . $i . '][file_name]', $section->file->file_name, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="col-sm-12 col-md-2">
                                            <label for="">File Type</label>
                                            {!! Form::select('old_file[' . $i . '][file_type]', ['1' => 'File', '3' => 'Video'], $section->file->file_type, [
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="">File</label>
                                            {!! Form::file('old_file[' . $i . '][files]', ['class' => 'form-control']) !!}
                                            @if (
                                                $section->file->file_extension == 'png' ||
                                                    $section->file->file_extension == 'jpg' ||
                                                    $section->file->file_extension == 'jpeg' ||
                                                    $section->file->file_extension == 'ico')
                                                <a href="{{ $section->file->file_url }}" target="_blank">
                                                    <img src="{{ $section->file->file_url }}" class="mt-2" height="120"
                                                         alt="File">
                                                </a>
                                            @else
                                                <a href="{{ $section->file->file_url }}"
                                                   target="_blank">{{ $section->file->file_name }}</a>
                                            @endif
                                        </div>
                                        <div class="col-sm-12 col-md-2 mt-4">
                                            <button data-id="{{ $section->id }}"
                                                    class="btn btn-danger delete-course-section">Delete
                                            </button>
                                        </div>
                                        @php
                                            $i++;
                                        @endphp
                                        <hr class="mt-4 divider">
                                    </div>
                                @endforeach
                            </div>

                            <div class="materials mb-3 repeater">
                                <div class="repeater mb-3" data-repeater-list="material">
                                    <div class="row mb-3" data-repeater-item>
                                        {!! Form::hidden('section_id', null) !!}
                                        <div class="col-sm-12 col-md-6">
                                            <label for="">Title <span class="text-danger">*</span></label>
                                            {!! Form::text('title', null, ['required','class' => 'form-control']) !!}
                                        </div>

                                        <div class="col-sm-12 col-md-6">
                                            <label for="">Description</label>
                                            {!! Form::textarea('section_description', null, ['class' => 'form-control']) !!}
                                        </div>

                                        <div class="col-sm-12 col-md-4">
                                            <label for="">File Name <span class="text-danger">*</span></label>
                                            {!! Form::text('file_name', null, ['required', 'class' => 'form-control']) !!}
                                        </div>
                                        <div class="col-sm-12 col-md-2">
                                            <label for="">File Type <span class="text-danger">*</span></label>
                                            {!! Form::select('file_type', ['1' => 'File', '3' => 'Video'], 1, ['required', 'class' => 'form-control']) !!}
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="">File <span class="text-danger">*</span></label>
                                            {!! Form::file('files', ['required', 'class' => 'form-control']) !!}
                                        </div>
                                        <div class="col-sm-12 col-md-2 mt-4">
                                            <input data-repeater-delete class="btn btn-danger" type="button"
                                                   value="Delete"/>
                                        </div>
                                        <hr class="mt-4 divider">
                                    </div>
                                </div>
                                <input data-repeater-create class="btn btn-success" type="button" value="Add"/>
                            </div>

                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('js')
    <script src="{{ asset('/assets/js/custom/form-repeater.js') }}"></script>
@endsection
