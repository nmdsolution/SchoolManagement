<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rawilk\Settings\Support\Context;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (is_numeric($credentials['email'])) {
            //if email is numeric value then it's the mobile number of the user
            $attempt = Auth::attempt(['mobile' => $request->email, 'password' => $request->password]);
        } else {
            $attempt = Auth::attempt($credentials);
        }
        if ($attempt) {
            if (Auth::user()->hasRole('Teacher')) {
                Auth::user()->createToken(Auth::user()->first_name)->plainTextToken;
            }

            settings()->context(new Context(['user_id' => Auth::user()->id]))->set('last_login', date('Y-m-d H:i:s'));
            $medium_id = settings()->context(new Context(['user_id' => Auth::user()->id]))->get('prefered_medium');
            $locale = settings()->context(new Context(['user_id' => Auth::user()->id]))->get('prefered_lacale', app()->getLocale());

            set_active_locale($locale);
            if ($medium_id) {
                set_active_medium($medium_id);
            }

            return redirect('/')->withSuccess('You have successfully logged in');
        }

        return redirect("login")->withErrors('Opps! You have entered invalid credentials');
    }
}
