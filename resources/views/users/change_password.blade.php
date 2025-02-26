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
                    {!! Form::open(['url' => 'change-password', 'method' => 'POST','class' => 'create-form','novalidate' => 'novalidate']) !!}
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                                {!! Form::password('current_password', ['required','placeholder' => __('Current Password'), 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('New Password') }} <span class="text-danger">*</span></label>
                                {!! Form::password('password', ['required','placeholder' => __('New password'), 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-4 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                                {!! Form::password('confirm_password', ['required','placeholder' => __('Confirm Password'), 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        

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