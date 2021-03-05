<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Whisper;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::delete('/whispers/{id}', 'WhisperController@destroy');

Route::get('/whispers/noauth/', function(){
    return Whisper::with('user')->get();
});

Route::get('/users/{id}', 'UserController@show');

Route::delete('/users/{id}', 'UserController@destroy');
