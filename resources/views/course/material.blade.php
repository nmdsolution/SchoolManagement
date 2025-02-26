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
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Course Material') }}
                        </h4>
                        {!! Form::model($course, [
                            'route' => ['course.update', $course->id],
                            'method' => 'PUT',
                            'enctype' => 'multipart/form-data',
                        ]) !!}
                        <div class="separator mb-5"><span class="h5">Course</span></div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Course name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('Name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Price') }} <span class="text-danger">*</span></label>
                                {!! Form::number('price', null, [
                                    'required',
                                    'placeholder' => __('Price'),
                                    'class' => 'form-control',
                                    'min' => 1,
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Duration') }} (In Hours)<span class="text-danger">*</span></label>
                                {!! Form::number('duration', null, [
                                    'required',
                                    'placeholder' => __('Duration'),
                                    'class' => 'form-control',
                                    'min' => 1,
                                ]) !!}
                            </div>

                            @if (Auth::user()->hasRole('Super Admin'))
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Super Teacher') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('super_teacher_ids[]', $super_teachers, $course_teacher, [
                                        'multiple',
                                        'required',
                                        'class' => 'form-control js-example-basic-single select2-hidden-accessible',
                                        'style' => 'width:100%',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Course Category') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('course_category_id', $categories, null, ['class' => 'form-control','style' => 'width:100%']) !!}
                                </div>
                            @endif

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label for="">{{__("image")}}</label>
                                {!! Form::file('thumbnail', ['class' => 'form-control']) !!}
                                <img src="{{ $course->thumbnail }}" class="mt-2" height="120" alt="">
                            </div>

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('description', null, [
                                    'required',
                                    'placeholder' => __('Description'),
                                    'class' => 'form-control',
                                ]) !!}
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
                            <div class="separator mb-5"><span class="h5">{{ __('Section') }}</span></div>
                        </div>

                        <div id="old_files">
                            <!--begin::Form group-->
                            @php
                                $i = 0;
                                $j = 0;
                            @endphp
                            <div class="form-group">
                                <div data-repeater-list="old_files">
                                    @foreach ($course->course_section as $section)
                                        <div data-repeater-item>
                                            {!! Form::hidden('section_id', $section->id) !!}
                                            <div class="form-group row">
                                                <div class="col-md-6 col-sm-12">
                                                    <label class="form-label">{{ __('Name') }} <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" name="title" required
                                                           value="{{ $section->title }}"
                                                           class="form-control mb-2 mb-md-0"
                                                           placeholder="Enter full name"/>
                                                </div>
                                                <div class="form-gorup col-sm-12 col-md-6">
                                                    <label for="">{{ __('Description') }}</label>
                                                    {!! Form::textarea('section_description', $section->description, ['class' => 'form-control']) !!}
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="inner-repeater">
                                                        <div data-repeater-list="course_files"
                                                             class="mb-5 course_files">
                                                            @foreach ($section->file as $file)
                                                                <div data-repeater-item>
                                                                    <div class="row">
                                                                        <div class="col-sm-12 col-md-2">
                                                                            {!! Form::hidden('file_id', $file->id) !!}
                                                                            <label for="">{{ __('File Name') }} <span
                                                                                        class="text-danger">*</span></label>
                                                                            {!! Form::text('file_name', $file->file_name, [
                                                                                'class' => 'form-control',
                                                                                'required',
                                                                            ]) !!}
                                                                        </div>

                                                                        <div class="col-sm-12 col-md-2">
                                                                            <label for="">{{ __('File Type') }} <span
                                                                                        class="text-danger">*</span></label>
                                                                            {!! Form::select('file_type', ['1' => 'File', '3' => 'Video'], $file->type, [
                                                                                'required',
                                                                                'class' => 'form-control',
                                                                            ]) !!}
                                                                        </div>

                                                                        <div class="col-sm-12 col-md-4">
                                                                            <label for="">{{ __('File') }} </label>
                                                                            {!! Form::file('file', ['class' => 'form-control']) !!}
                                                                            <div class="image-preview">
                                                                                @if (
                                                                                    $file->file_extension == 'png' ||
                                                                                        $file->file_extension == 'jpg' ||
                                                                                        $file->file_extension == 'jpeg' ||
                                                                                        $file->file_extension == 'ico')
                                                                                    <a href="{{ $file->file_url }}"
                                                                                       target="_blank"> <img
                                                                                                src="{{ $file->file_url }}"
                                                                                                class="mt-2"
                                                                                                height="120"
                                                                                                alt="File"> </a>
                                                                                @else
                                                                                    <a href="{{ $file->file_url }}"
                                                                                       target="_blank">{{ $file->file_name }}</a>
                                                                                @endif
                                                                            </div>

                                                                        </div>

                                                                        <div class="col-sm-12 col-md-2 mt-4">
                                                                            <div class="input-group">
                                                                                <span class="input-group-text">
                                                                                    {!! Form::checkbox('downloadable', 1, $file->downloadable ? true : false, ['']) !!}
                                                                                </span>
                                                                                <input type="text" disabled
                                                                                       value="{{ __('Downloadable') }}"
                                                                                       class="form-control permission">
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                            <button
                                                                                    class="border  btn btn-icon btn-flex mt-4 btn-danger"
                                                                                    data-repeater-delete type="button">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if (count($section->file) == 0)
                                                                <div data-repeater-item>
                                                                    <div class="row">
                                                                        <div class="col-sm-12 col-md-2">
                                                                            {!! Form::hidden('file_id', null) !!}
                                                                            <label for="">{{ __('File Name') }} <span
                                                                                        class="text-danger">*</span></label>
                                                                            {!! Form::text('file_name', null, [
                                                                                'class' => 'form-control',
                                                                                'required',
                                                                            ]) !!}
                                                                        </div>

                                                                        <div class="col-sm-12 col-md-2">
                                                                            <label for="">{{ __('File Type') }} <span
                                                                                        class="text-danger">*</span></label>
                                                                            {!! Form::select('file_type', ['1' => 'File', '3' => 'Video'], null, ['required', 'class' => 'form-control']) !!}
                                                                        </div>

                                                                        <div class="col-sm-12 col-md-4">
                                                                            <label for="">{{ __('File') }} </label>
                                                                            {!! Form::file('file', ['class' => 'form-control']) !!}
                                                                        </div>

                                                                        <div class="col-sm-12 col-md-2 mt-4">
                                                                            <div class="input-group">
                                                                                <span class="input-group-text">
                                                                                    {!! Form::checkbox('downloadable', 1, false, ['']) !!}
                                                                                </span>
                                                                                <input type="text" disabled
                                                                                       value="{{ __('Downloadable') }}"
                                                                                       class="form-control permission">
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                            <button
                                                                                    class="border  btn btn-icon btn-flex mt-4 btn-danger"
                                                                                    data-repeater-delete type="button">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                        </div>

                                                        <button class="btn btn-sm btn-success btn-flex btn-light-primary"
                                                                data-repeater-create type="button">
                                                            <i class="fa fa-plus"></i>
                                                            {{ __('Add Files') }}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2 col-md-2">
                                                    <a href="javascript:;" data-repeater-delete
                                                       class="btn btn-sm btn-flex btn-danger mt-3 mt-md-9">
                                                        <i class="fa fa-trash-alt"><span class="path1"></span><span
                                                                    class="path2"></span><span
                                                                    class="path3"></span><span
                                                                    class="path4"></span><span class="path5"></span></i>
                                                        {{ __('Delete Row') }}
                                                    </a>
                                                </div>
                                            </div>
                                            <hr class="mt-3">
                                            @php
                                                $i++;
                                            @endphp
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!--end::Form group-->

                        </div>


                        <!--begin::Repeater-->
                        <div id="new_files">
                            <!--begin::Form group-->
                            <div class="form-group">
                                <div data-repeater-list="course_section">
                                    <div data-repeater-item>
                                        {!! Form::hidden('section_id', null) !!}
                                        <div class="form-group row">
                                            <div class="col-md-6 col-sm-12">
                                                <label class="form-label">{{ __('Name') }} <span
                                                            class="text-danger">*</span></label>
                                                <input type="text" name="title" required
                                                       class="form-control mb-2 mb-md-0" placeholder="Enter full name"/>
                                            </div>
                                            <div class="form-gorup col-sm-12 col-md-6">
                                                <label for="">{{ __('Description') }}</label>
                                                {!! Form::textarea('section_description', null, ['class' => 'form-control']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                <div class="inner-repeater">
                                                    <div data-repeater-list="course_files" class="mb-5">
                                                        <div data-repeater-item>

                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-2">
                                                                    <label for="">{{ __('File Name') }} <span
                                                                                class="text-danger">*</span></label>
                                                                    {!! Form::text('file_name', null, ['class' => 'form-control', 'required']) !!}
                                                                </div>

                                                                <div class="col-sm-12 col-md-2">
                                                                    <label for="">{{ __('File Type') }} <span
                                                                                class="text-danger">*</span></label>
                                                                    {!! Form::select('file_type', ['1' => 'File', '3' => 'Video'], null, [
                                                                        'required',
                                                                        'class' => 'form-control file_type',
                                                                    ]) !!}
                                                                </div>

                                                                <div class="col-sm-12 col-md-4">
                                                                    <label for="">{{ __('File') }} <span
                                                                                class="text-danger">*</span></label>
                                                                    {!! Form::file('file', ['required', 'class' => 'form-control']) !!}
                                                                </div>

                                                                <div class="col-sm-12 col-md-2 mt-4">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">
                                                                            {!! Form::checkbox('downloadable', 1, false, ['']) !!}
                                                                        </span>
                                                                        <input type="text" disabled
                                                                               value="{{ __('Downloadable') }}"
                                                                               class="form-control permission">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button
                                                                            class="border  btn btn-icon btn-flex mt-4 btn-danger"
                                                                            data-repeater-delete type="button">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <button class="btn btn-sm btn-success btn-flex btn-light-primary"
                                                            data-repeater-create type="button">
                                                        <i class="fa fa-plus"></i>
                                                        {{ __('Add Files') }}
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="col-sm-2 col-md-2">
                                                <a href="javascript:;" data-repeater-delete
                                                   class="btn btn-sm btn-flex btn-danger mt-3 mt-md-9">
                                                    <i class="fa fa-trash-alt"><span class="path1"></span><span
                                                                class="path2"></span><span class="path3"></span><span
                                                                class="path4"></span><span class="path5"></span></i>
                                                    {{ __('Delete Row') }}
                                                </a>
                                            </div>
                                        </div>
                                        <hr class="mt-3">
                                    </div>
                                </div>
                            </div>
                            <!--end::Form group-->

                            <!--begin::Form group-->
                            <div class="form-group">
                                <a href="javascript:;" data-repeater-create class="btn btn-flex btn-success">
                                    <i class="fa fa-plus"></i>
                                    {{ __('Add Row') }}
                                </a>
                            </div>
                            <!--end::Form group-->
                        </div>
                        <!--end::Repeater-->

                        <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('#new_files').repeater({
            repeaters: [{
                selector: '.inner-repeater',
                show: function () {
                    $(this).slideDown();

                },

                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            }],

            show: function () {
                $(this).slideDown();
            },

            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });

        // OLD FILES
        $('#old_files').repeater({
            repeaters: [{
                selector: '.inner-repeater',
                show: function () {
                    $(this).find('.image-preview').hide();
                    $(this).slideDown();

                },

                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            }],

            show: function () {
                $(this).slideDown();
            },

            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    </script>

    <script src="{{ asset('/assets/js/custom/form-repeater.js') }}"></script>
    <script>
        // $('.file_type').change(function(e) {
        //     if ($(this).val() == 1) {
        //         $('#downloadable').attr('disabled', false);
        //     } else {
        //         $('#downloadable').attr('disabled', true);
        //     }

        //     let $select = $(e.target);
        //     let id = $select.data("id");
        //     alert(id);


        // });
    </script>
@endsection
