<?php

use App\Models\Whisper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::put('/whispers/{id}', 'WhisperController@update');

Route::get('/whispers/noauth/', function () {
    $whispers = Whisper::with('user')->latest()->paginate(10);
    $imgPath = [];
    foreach ($whispers as $whisper) {
        $imgPath[$whisper->user->id] = Storage::cloud()->temporaryUrl(
            $whisper->user->thumbnail,
            now()->addHour()
        );
    };
    return array("whispers" => $whispers, "imgPath" => $imgPath);
});

Route::delete('/users/{id}', 'UserController@destroy');
