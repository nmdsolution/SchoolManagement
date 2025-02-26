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
                        <h1>Forgot Password</h1>
                        {{--                        <p class="account-subtitle">Need an account? <a href="{{url('register')}}">Sign Up</a></p>--}}
                        {{--                        <h2>Sign in</h2>--}}

                        <!-- Form -->
                        <form method="POST" class="create-form mt-3" action="{{ url('password/reset') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email <span class="login-danger">*</span></label>
                                <input type="text" placeholder="Email" id="Email" class="form-control" name="email">
                                <span class="profile-views"><i class="fa fa-envelope"></i></span>
                            </div>
                            
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit">Send Password Reset Link</button>
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
