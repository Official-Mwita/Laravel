<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupportFileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Proctected routes
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/place-order', [OrderController::class, 'store']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::delete('/order/{trackId}/cancel', [OrderController::class, 'cancel']);
    Route::get('/order/{trackId}/enact', [OrderController::class, 're_place']);
    Route::get('/orders/all/files', [SupportFileController::class, 'index']);
});


//Public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
//Route::get('/login', [AuthController::class, 'login'])->name('login');

// Route::get('/tokens', function (Request $request) {
    
//     //$request->user()->tokens()->delete(); //delete all tokens
//     //Create new one
//     //$session_token = $request->$user->createToken('Onlyme!@3')->plainTextToken;

//     $token = csrf_token();
//     $user = auth()->user();

//     return response([
//         'user' => $user,
//         //'session' => $session_token,
//         '_token' => $token,
//     ], 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000')
//     ->header('Access-Control-Allow-Methods', 'GET');

// });



// Route::post('/order', function(){
//     return Order::create([
//         'price' => '30.50',
//         'academic_level' => 'College',
//         'service' => 'Writing',
//         'type_of_paper' => 'Essay',
//         'description' => 'Descriptions goes here',
//         'reference_style'  => 'APA',
//         'pages' => '3',
//         'hours' => '72',
//         'spacing' => 'Double'
//     ]);
// });
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/tokens', function (Request $request) {

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
