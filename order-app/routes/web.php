<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Admin\AdminAuthLoginController;
use App\Http\Controllers\Admin\AdminHomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/tokens', function (Request $request) {

    session();

    $token = csrf_token();
    $session_token = '';
    $success = false;

    if(auth()->user()){
        
        auth()->user()->tokens()->delete();
        $session_token = $request->user()->createToken('Onlyme!@3')->plainTextToken;
        $success = true;

        return response([
            'username' => auth()->user()->username,
            'api_key' => $session_token,
            'success' => $success,
            '_token' => $token
        ], 200);
    }
   
    //$user = $request->user();

    return response([
        'success' => $success,
        '_token' => $token
    ], 200);

});


Route::get('/csrf-token', function (Request $request) {

    $token = csrf_token();

    return response([
        '_token' => $token
    ], 200);

});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [LogoutController::class, 'store']);

//Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'store']);

//Redirects to react app
//Ensures that cookies are set

Route::get('/', function () {
    session();
    return response([
        'message' => "Being redirect",
        'to' => "user",
    ], 301)->header('Location', 'user');
})->name('home');

// Route::post('/login', function (Request $request) {
    
//     //$request->user()->tokens()->delete(); //delete all tokens
//     //Create new one
//     //$session_token = $request->$user->createToken('Onlyme!@3')->plainTextToken;

//     if(!auth()->attempt($request->only('email', 'password'), $request->remember)){
//         return response([
//             'message' => 'Invalid password or email',
//             'success' => false
//         ], 401);
//     }


// });

//Admin setup
// Route::get('/M4Z6vYbzwnkJhFL/create', function () {
//     $admin = User::create([
//         'name' => 'toPnoPch',
//         'username' => 'topnopch',
//         'email' => 'topnopch@topnopchwriters.com',
//         'password' =>  bcrypt('M4?6vYbzwn!@hFL'),
//         'isadmin' => 1,
//     ]);

//     return response("Success");
// });

Route::get('/M4Z6vYbzwnkJhFL/home', [AdminHomeController::class, 'index'])->name('admin_home');

Route::get('/M4Z6vYbzwnkJhFL/login', [AdminAuthLoginController::class, 'index'])->name('admin_login');
Route::post('/M4Z6vYbzwnkJhFL/login', [AdminAuthLoginController::class, 'store']);
Route::post('/M4Z6vYbzwnkJhFL/logout', [AdminAuthLoginController::class, 'logout'])->name('admin_logout');
