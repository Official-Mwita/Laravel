<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $fields = $request->validate([

            'fname' => 'required|string|max:100',
            'username' => 'required|string|unique:users,username|max:50',
            'email' => 'required|string|unique:users,email|max:200',
            'password' => 'required|string|confirmed|min:8',

        ]);

        if(AuthController::passwordStrength($fields['password'])) {
            return response([
                'password' => 'Weak password'
            ], 500);
        }

        $user = User::create([
            'name' => $fields['fname'],
            'username' => $fields['username'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'isAdmin' => 0,
        ]);

        //Sign in on successful user account creation
        auth()->attempt($request->only('email', 'password'));
        $token = $user->createToken('Onlyme!@3')->plainTextToken;

        $response = [
            'user' => $user,
            'success' => true,
            'token' => $token,
        ];

        return response($response, 201);

    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        //Auth::logout();

        return [
            'message' => 'Logout successful',
            'success' => true
        ];

    }

    public function login(Request $request)
    {
        $fields = $request->validate([

            'email' => 'required|string',
            'password' => 'required|string',

        ]);


        //Try to change this to a ternary request with
        //$user ? User::where('username', $fields['email'])->first()
    //    $user = User::where('email', $fields['email'])->first();
       
    //    if (!$user || !Hash::check($fields['password'], $user->password)){
    //         return response([
    //             'message' => 'Invalid password or email',
    //             'success' => false
    //         ], 401);
    //    }


    if(!auth()->attempt($request->only('email', 'password'), $request->remember)){
        return response([
            'message' => 'Invalid password or email',
            'success' => false
        ], 401);
    }

    $user = auth()->user();

    $token = $user->createToken('Onlyme!@3')->plainTextToken;

       $response = [
        'user' => $user,
        'success' => true,
        'token' => $token,
    ];

    return response($response, 201);

    }

    //Used to check and prevent bruteforce
    private function bFChecker()
    {
        //
    }

    //Checks for password strength
    private function passwordStrength(String $pass)
    {
        $weak = false;

        return $weak;
    }

}
