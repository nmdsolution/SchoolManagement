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
                                {{ __('Create Courses') }}
                            </h4>
                            <form class="create-form pt-3" id="formdata" action="{{url('course')}}" method="POST"
                                  novalidate="novalidate">
                                @csrf
                                <div class="separator mb-5"><span class="h5">Course</span></div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Course Name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', null, ['required', 'placeholder' => __('Name'), 'class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Price') }} <span class="text-danger">*</span></label>
                                        {!! Form::number('price', null, ['required', 'placeholder' => __('Price'), 'class' => 'form-control','min'=>1]) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Duration') }} <small>(In Hours)</small><span
                                                    class="text-danger">*</span></label>
                                        {!! Form::number('duration', null, ['required', 'placeholder' => __('Duration'), 'class' => 'form-control','min'=>1]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Super Teacher') }} <span class="text-danger">*</span></label>
                                        {!! Form::select('super_teacher_ids[]', $super_teachers, null, ['multiple','class' => 'form-control js-example-basic-single select2-hidden-accessible','style' => 'width:100%']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Course Category') }} <span class="text-danger">*</span></label>
                                        {!! Form::select('course_category_id', $categories, null, ['class' => 'form-control','style' => 'width:100%']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label for="">{{__("image")}} <span class="text-danger">*</span></label>
                                        {!! Form::file('thumbnail', ['required','class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('description', null, ['required','placeholder' => __('Description'),'class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Tags') }} </label>
                                        {!! Form::textarea('tags', null, ['placeholder' => __('Tags'),'class' => 'form-control']) !!}
                                    </div>

                                </div>
                                <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('List Courses') }}
                            </h4>
                            <div class="row">
                                <div class="col-12">
                                    @php
                                        $url = url('course/show');
                                        $columns = [
                                            trans('no') => ['data-field' => 'no'],
                                            trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                            trans('Super Teacher ID') => ['data-field' => 'super_teacher_id', 'data-visible' => false],
                                            trans('Name') => ['data-field' => 'name'],
                                            trans('image') => ['data-field' => 'image','data-formatter' => 'imageFormatter'],
                                            trans('Description') => ['data-field' => 'description'],
                                            trans('Tags') => ['data-field' => 'tags'],
                                            trans('Super Teacher Name') => ['data-field' => 'super_teachers_name'],
                                            trans('Price') => ['data-field' => 'price'],
                                            trans('Duration') => ['data-field' => 'duration'],
                                            trans('Files') => ['data-field' => 'notes','data-formatter' => 'notesFormatter'],
                                        ];
                                        $actionColumn = [
                                            'editButton' => false,
                                            'deleteButton' => ['url' => url('/course')],
                                            'data-events' => 'courseEvents',
                                            'customButton' => [
                                                // ['iconClass'=>'feather-edit','url'=>url('course/edit'),'title'=>'Edit Course','customClass'=>'edit-course'],
                                                ['iconClass'=>'feather-edit','url'=>url('course/material'),'title'=>'Edit Course','customClass'=>'edit-course'],
                                            ]
                                        ];
                                    @endphp
                                    <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn>
                                    </x-bootstrap-table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (Auth::user()->hasRole('Super Teacher'))
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('List Courses') }}

                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('/superteachercourses');
                                    $columns = [
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('Name') => ['data-field' => 'name'],
                                        trans('image') => ['data-field' => 'image','data-formatter' => 'imageFormatter'],
                                        trans('Description') => ['data-field' => 'description'],
                                        trans('Tags') => ['data-field' => 'tags'],
                                        trans('Price') => ['data-field' => 'price'],
                                        trans('Duration') => ['data-field' => 'duration'],
                                        trans('Files') => ['data-field' => 'notes','data-formatter' => 'notesFormatter'],
                                    ];
                                    $actionColumn = [
                                        'editButton' => false,
                                        'deleteButton' => ['url' => url('/course')],
                                        'data-events' => 'courseEvents',
                                        'customButton' => [
                                            ['iconClass'=>'feather-edit','url'=>url('course/material'),'title'=>'Edit Course','customClass'=>''],
                                        ]
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn>
                                </x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection            
