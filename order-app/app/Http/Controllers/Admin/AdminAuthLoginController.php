<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthLoginController extends Controller
{
    public function __construct ()
    {
        //$this->middleware(['guest']);
    }
    
    public function index()
    {
        
        if (Auth::check()) return redirect()->route('admin_home');
        return view('login');

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if(!auth()->attempt($request->only('username', 'password'), $request->remember)){
            return back()->with('status', 'Invalid login details');
        }

        return redirect()->route('admin_home');
        
    }

    public function logout()
    {
        if(Auth::check()){
            auth()->logout();
            return redirect()->route('admin_login');
        }

        else return redirect()->route('home');

    }
}
