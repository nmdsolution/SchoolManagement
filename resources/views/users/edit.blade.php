@extends('layout.master')

@section('title')
    {{ __('user') }}
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            {{ __('manage_user') }}
        </h3>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        {{ __('edit_user') }}
                    </h4>
                    {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id],'class' => 'validate-form']) !!}
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('name') }}</label>
                                {!! Form::text('name', $user->first_name, ['required','placeholder' => 'Name', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('email') }}</label>
                                {!! Form::text('email', null, ['placeholder' => 'Email', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('password') }}</label>
                                {!! Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('confirm_password') }}</label>
                                {!! Form::password('confirm-password', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-2 local-forms">
                            <div class="form-group">
                                <label for="">{{ __('role') }}</label>
                                {!! Form::select('roles', $roles, $userRole, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <button type="submit" class="btn btn-theme">{{ __('update') }}</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection