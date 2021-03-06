<?php

namespace Olorin\Auth;

//use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
//use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class LoginLogoutController extends Controller
{
    //use AuthenticatesAndRegistersUsers, ThrottlesLogins;
    use AuthenticatesUsers;

    protected $login_path = '/login';

    protected $redirectTo = '/mgmt';

    public function loginForm(Request $request)
    {
        return view('mgmt::login', [
            'email' => ($request->session()->has('email') ? $request->session()->get('email') : '')
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
