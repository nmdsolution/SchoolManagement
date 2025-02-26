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
                    {!! Form::model($user, ['route' => ['profile',$user->id],'method' => 'POST','novalidate' => 'novalidate','class' => 'profile']) !!}
                    @method('PUT')
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 @if(!Auth::user()->HasRole('Teacher')) col-md-6 @else col-md-12 @endif local-forms">
                            <div class="form-group">
                                <label for="">{{ Auth::user()->hasRole('Teacher') ? __('Full Name') : __('first_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', null, ['required','placeholder' => __('First Name'), 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        @if (!Auth::user()->hasRole("Teacher"))
                        <div class="col-xs-12 col-sm-12 col-md-6 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', $user->getRawOriginal('last_name'), ['required','placeholder' => __('Last Name'), 'class' => 'form-control']) !!}
                            </div>
                        </div>    
                        @endif
                        

                        <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('email') }} <span class="text-danger">*</span></label>
                                {!! Form::text('email', null, ['required','placeholder' => __('Email'), 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('Mobile No.') }} <span class="text-danger">*</span></label>
                                {!! Form::text('mobile', null, ['required','placeholder' => __('Mobile Number'), 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('Date of Birth') }} <span class="text-danger">*</span></label>
                                {!! Form::text('birth_date', null, ['required','placeholder' => __('Date of Birth'), 'class' => 'form-control datetimepicker']) !!}
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

                        @if (Auth::user()->teacher)
                        <div class="form-group col-sm-12 col-md-4 mt-4">
                            <div class="input-group mb-3">
                                <div class="input-group">
                                <span class="input-group-text">
                                    {!! Form::checkbox('teacher[contact_status]', 1, null, ['id' => 'contcat_status']) !!}
                                </span>
                                    <input type="text" disabled value="{{ __('Visiable contact to student & parents') }}" class="form-control permission">
                                </div>
                            </div>
                        </div>    
                        @endif
                        

                        <div class="col-xs-12 col-sm-12 col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection