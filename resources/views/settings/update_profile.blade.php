@extends('layout.master')

@section('title')
    {{ __('update_profile') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('update_profile') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" id="admin-profile-update" enctype="multipart/form-data" action="{{ route('update-profile') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                {!! Form::hidden('id', "$admin_data->id", ['class' => 'form-control']) !!}

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', "$admin_data->first_name", ['placeholder' => __('first_name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', "$admin_data->last_name", ['placeholder' => __('last_name'), 'class' => 'form-control']) !!}

                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mobile') }}</label>
                                    {!! Form::text('mobile', "$admin_data->mobile", ['placeholder' => __('mobile'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender','male',Str::lower($admin_data->gender) == 'male' ? 'checked':'') !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender','female', Str::lower($admin_data->gender) == 'female' ? 'checked':'') !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('image') }} <span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="file-upload-default form-control"/>
                                    <div style="width: 40px;">
                                        <img src="{{$admin_data->image}}" id="edit-user-image-tag" class="img-fluid w-100"/>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    @php
                                        $date = date("m/d/Y", strtotime($admin_data->dob));
                                    @endphp
                                    {!! Form::text('dob', $date  , ['placeholder' => __('dob'), 'class' => 'datetimepicker form-control']) !!}
                                    <span class="input-group-addon input-group-append">
                                </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('email', "$admin_data->email", ['placeholder' => __('email'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('current_address', $admin_data->current_address, ['placeholder' => __('current_address'), 'class' => 'form-control', 'id' => 'current_address','rows'=>2]) !!}
                                </div>
                                <div class="form-group col-12">
                                    <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('permanent_address', $admin_data->permanent_address, ['placeholder' => __('permanent_address'), 'class' => 'form-control', 'id' => 'permanent_address','rows'=>2]) !!}
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
