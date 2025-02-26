@extends('layout.master-without-nav')
@section('content')
    <div class="login-wrapper">
        <div class="d-flex justify-content-end">
            <div class="loginbox me-5 ">
                <div class="login-left">
                    <img class="img-fluid" src="{{ URL::asset('assets/img/login.png')}}" alt="Logo">
                </div>
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>Welcome to Yadiko</h1>
                        {{--                        <p class="account-subtitle">Need an account? <a href="{{url('register')}}">Sign Up</a></p>--}}
                        {{--                        <h2>Sign in</h2>--}}

                        <!-- Form -->
                        <form method="POST" class="mt-3" action="{{ route('post.login') }}">
                            @csrf
                            <div class="form-group">
                                <label>Username <span class="login-danger">*</span></label>
                                <input type="text" placeholder="Email" id="Email" class="form-control" name="email">
                                <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                            </div>
                            <div class="form-group pass-group">
                                <label>Password <span class="login-danger">*</span></label>
                                <input type="password" placeholder="Password" id="password" class="form-control pass-input " name="password">
                                <span class="profile-views feather-eye toggle-password"></span>
                            </div>
                            <div class="forgotpass">
                                <div class="remember-me">
                                    <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> Remember me
                                        <input type="checkbox" name="radio">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <a href="{{url('password/reset')}}">Forgot Password?</a>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit">Login</button>
                            </div>
                        </form>
                        <!-- /Form -->

                        {{--                        <div class="login-or">--}}
                        {{--                            <span class="or-line"></span>--}}
                        {{--                            <span class="span-or">or</span>--}}
                        {{--                        </div>--}}
                        <!-- Social Login -->
                        {{--                        <div class="social-login">--}}
                        {{--                            <a href="#"><i class="fab fa-google-plus-g"></i></a>--}}
                        {{--                            <a href="#"><i class="fab fa-facebook-f"></i></a>--}}
                        {{--                            <a href="#"><i class="fab fa-twitter"></i></a>--}}
                        {{--                            <a href="#"><i class="fab fa-linkedin-in"></i></a>--}}
                        {{--                        </div>--}}
                        <!-- /Social Login -->

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
