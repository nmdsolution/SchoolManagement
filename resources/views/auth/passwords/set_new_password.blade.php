@extends('layout.master-without-nav')
@section('content')
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-left">
                    <img class="img-fluid" src="{{ URL::asset('assets/img/login.png')}}" alt="Logo">
                </div>
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>New Password</h1>
                        
                        <form method="POST" class="reset-password mt-3" action="{{ url('set-new-password') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group">
                                <label>Email <span class="login-danger">*</span></label>
                                <input type="text" placeholder="Email" id="Email" class="form-control" name="email">
                                <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                            </div>
                            <div class="form-group pass-group">
                                <label>Password <span class="login-danger">*</span></label>
                                <input type="password" placeholder="Password" id="password" class="form-control pass-input " name="password">
                                <span class="profile-views feather-eye toggle-password"></span>
                            </div>

                            <div class="form-group pass-group">
                                <label>Confirm Password <span class="login-danger">*</span></label>
                                <input type="password" placeholder="Confirm Password" id="password" class="form-control pass-input-1 " name="confirm_password">
                                <span class="profile-views feather-eye toggle-password-1"></span>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit">Submit</button>
                            </div>
                        </form>
                        
                        <div class="col-sm-12 col-md-12 text-end">
                            <a href="{{ url('/') }}" class="">Back to Login</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
