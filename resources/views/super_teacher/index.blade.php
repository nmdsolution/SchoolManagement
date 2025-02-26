@extends('layout.master')

@section('title')
    {{ __('Super Teacher') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Super Teacher') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create Super Teacher') }}
                        </h4>
                        <form class="super-teacher-validate-form create-form pt-3" id="formdata"
                              action="{{route('super.teacher.add')}}"
                              enctype="multipart/form-data" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="separator mb-5"><span class="h5">Super Teacher</span></div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, ['required', 'placeholder' => __('first_name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', null, ['required', 'placeholder' => __('last_name'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('email', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Mobile No.') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('mobile', null, ['required', 'placeholder' => __('mobile no'), 'class' => 'form-control remove-number-increment','min'=>1]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('dob', null, ['required', 'placeholder' => __('dob'), 'class' => 'dob-date form-control']) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'male') !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'female') !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-3">
                                    <div class="form-group files">
                                        <label>{{ __('image') }}</label> <span class="text-danger">*</span>
                                        <input name="image" type="file" class="form-control">

                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('current_address', null, ['required','placeholder' => __('current_address'),'class' => 'form-control','rows' => 3,]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('permanent_address', null, [ 'required', 'placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3, ]) !!}
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
                            {{ __('List Super Teacher') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('super-teacher-list');
                                    $columns = [
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('first_name') => ['data-field' => 'first_name'],
                                        trans('last_name') => ['data-field' => 'last_name'],
                                        trans('gender') => ['data-field' => 'gender'],
                                        trans('email') => ['data-field' => 'email'],
                                        trans('mobile') => ['data-field' => 'mobile'],
                                        trans('dob') => ['data-field' => 'dob'],
                                        trans('image') => ['data-field' => 'image', 'data-formatter' => 'imageFormatter'],
                                        trans('current_address') => ['data-field' => 'current_address'],
                                        trans('permanent_address') => ['data-field' => 'permanent_address'],
                                    ];
                                    $actionColumn = [
                                        'editButton' => ['url' => url('/super-teacher')],
                                        'deleteButton' => ['url' => url('/super-teacher-delete')],
                                        'data-events' => 'superTeacherEvents',
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
    </div>


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Edit Super Teacher') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form class="editform" action="{{url('super-teacher-update')}}" novalidate="novalidate"
                      enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="separator mb-5"><span class="h5">{{__('Super Teacher Details')}}</span></div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', null, ['id'=>'first_name','required', 'placeholder' => __('first_name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', null, ['id'=>'last_name','required', 'placeholder' => __('last_name'), 'class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                {!! Form::text('email', null, ['id'=>'email','required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Mobile No.') }} <span class="text-danger">*</span></label>
                                {!! Form::text('mobile', null, ['id'=>'mobile','required', 'placeholder' => __('mobile no'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                {!! Form::text('dob', null, ['id'=>'dob','required', 'placeholder' => __('dob'), 'class' => 'datetimepicker form-control']) !!}
                                <span class="input-group-addon input-group-append">
                                </span>
                            </div>
                            <div class="form-group col-sm-12 col-md-3">
                                <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                <br>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'male','gender' == 'male', ['class' => 'edit']) !!}   {{ __('male') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'female','gender' == 'female', ['class' => 'edit']) !!}  {{ __('female') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3">
                                <div class="form-group files">
                                    <label>{{ __('image') }}</label> <span class="text-danger">*</span>
                                    <input name="image" type="file" class="form-control">
                                    <div class="image">
                                        <img src="" id="image-show" class="img-fluid w-25" alt="image"
                                             onerror="onErrorImage(event)">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('current_address', null, ['id'=>'current_address','required','placeholder' => __('current_address'),'class' => 'form-control','rows' => 3,]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('permanent_address', null, ['id'=>'permanent_address','required','placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
