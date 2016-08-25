<?php

namespace Olorin\Auth;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class LoginLogoutController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $login_path = '/login';

    protected $redirectTo = '/mgmt';

    public function loginForm(Request $request)
    {
        return view('mgmt::login', [
            'email' => ($request->session()->has('email') ? $request->session()->get('email') : '')
        ]);
    }

    public function loginPost(Request $request)
    {
        return $this->postLogin($request);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}