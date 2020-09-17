<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');

    }
    public function index()
    {
        return view('admin.login');
    }
    public function login(Request $request){
        // validate form data
        $this->validate($request,[
            'email' => 'required',
            'password' => 'required',
        ]);
        //
        if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password],$request->remember)){
            return redirect()->intended(route('home'));

        }
        return redirect()->back()->withInput($request->only('email','remember'));

    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/');
    }

}
