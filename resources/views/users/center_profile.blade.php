@extends('layout.master')

@section('title')
    {{ __('Profile') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Profile') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{-- {{ __('Pro') }} --}}
                        </h4>
                        {{-- {!! Form::open(['route' => 'profile', 'method' => 'POST','class' => 'create-form','novalidate' => 'novalidate']) !!} --}}
                        {!! Form::model($user, [
                            'route' => ['profile', $user->id],
                            'method' => 'POST',
                            'novalidate' => 'novalidate',
                            'class' => 'profile',
                        ]) !!}


                        @method('PUT')
                        <div class="separator mb-5"><span class="h5">{{ __('User Details') }}</span></div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 local-forms">
                                <div class="form-group">
                                    <label for="">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, [ 'placeholder' => __('First Name'), 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-6 local-forms">
                                <div class="form-group">
                                    <label for="">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', $user->getRawOriginal('last_name'), ['required', 'placeholder' => __('Last Name'), 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                                <div class="form-group">
                                    <label for="">{{ __('email') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('email', null, ['required', 'placeholder' => __('Email'), 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                                <div class="form-group">
                                    <label for="">{{ __('Mobile No.') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mobile', null, ['required', 'placeholder' => 'Mobile Number', 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                                <div class="form-group">
                                    <label for="">{{ __('Date of Birth') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::text('birth_date', null, [
                                        'required',
                                        'placeholder' => __('Date of Birth'),
                                        'class' => 'form-control datetimepicker',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Profile') }}</label>
                                    {!! Form::file('image', ['class' => 'form-control']) !!}
                                </div>
                                <div class="avatar avatar-xxl">
                                    <img src="{{ url($user->image) }}" class="avatar-img rounded" alt="No profile found">
                                </div>
                            </div>
                        </div>
                        <div class="separator mb-5"><span class="h5">{{ __('Center Details') }}</span></div>

                        <div class="row">

                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('center[name]', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                {!! Form::text('center[support_email]', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4 local-forms">
                                <label>{{ __('Contact') }} <span class="text-danger">*</span></label>
                                {!! Form::number('center[support_contact]', null, ['required', 'placeholder' => __('contact'), 'class' => 'remove-number-increment form-control ','min'=>1]) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Tagline') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('center[tagline]', null, [ 'required', 'placeholder' => __('Tagline'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('center[address]', null, [ 'required', 'placeholder' => __('Address'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                            </div>

                            <div class="col-12 col-sm-12 col-md-6">
                                <div class="form-group files">
                                    <label>{{ __('Logo') }}</label>
                                    <input name="logo" type="file" class="form-control">
                                </div>
                                <div class="avatar avatar-xxl">
                                    <img src="{{ url($user->center->logo) }}" class="avatar-img full-image rounded" alt="No profile found">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <button type="submit" class="btn mt-3 btn-primary">{{ __('submit') }}</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
