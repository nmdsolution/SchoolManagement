@extends('layout.master')
@section('title')
    {{ __('Course Category') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Course Category') }}
            </h3>
        </div>
        @if (Auth::user()->hasRole('Super Admin'))
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('Create Course Category') }}
                            </h4>
                            <form class="create-form pt-3" id="formdata" action="{{url('course_category')}}" method="POST"
                                  novalidate="novalidate">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Course Name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', null, ['required', 'placeholder' => __('Name'), 'class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('description', null, ['required','placeholder' => __('Description'),'class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label for="">{{__("image")}} <span class="text-danger">*</span></label>
                                        {!! Form::file('thumbnail', ['required','class' => 'form-control']) !!}
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
                                {{ __('List Course Categories') }}
                            </h4>
                            <div class="row">
                                <div class="col-12">
                                    @php
                                        $url = url('course_category/show');
                                        $columns = [
                                            trans('no') => ['data-field' => 'no'],
                                            trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                            trans('Name') => ['data-field' => 'name'],
                                            trans('Image') => ['data-field' => 'thumbnail','data-formatter' => 'imageFormatter'],
                                            trans('Description') => ['data-field' => 'description'],
                                        ];
                                        $actionColumn = [
                                            'editButton' => false,
                                            'deleteButton' => ['url' => url('/course_category')],
                                            'data-events' => 'courseEvents',
                                            'customButton' => [
                                                ['iconClass'=>'feather-edit','url'=>url('course_category/edit'),'title'=>'Edit Course Category','customClass'=>'edit-course'],
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
    </div>
@endsection            
