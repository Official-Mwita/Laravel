<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function store()
    {
        auth()->user()->tokens()->delete();
        auth()->logout();


        return [
            'message' => 'Logout successful',
            'success' => true
        ];

    }
}
