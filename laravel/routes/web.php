<?php

use App\Models\Whisper;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

Route::get('/', function () {
    return view('/home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/myprofile', function () {
    if (Auth::check()) {
        return view('/myprofile');
    } else {
        return view('/home');
    }
});

Route::get('/profile/{userId}', 'ViewController@profile');

// api
// whisper
Route::get('/api/whispers/profile/{id}', 'WhisperController@showUser');

Route::delete('/api/whispers/{id}', 'WhisperController@destroy');

Route::put('/api/whispers/{id}', 'WhisperController@update');

Route::get('/api/whispers/noauth/', function () {
    $whispers = Whisper::with('user')->latest()->paginate(10);
    $imgPath = [];
    foreach ($whispers as $whisper) {
        $imgPath[$whisper->user->id] = Storage::disk('minio1')->temporaryUrl(
            $whisper->user->thumbnail,
            now()->addSecond(1)
        );
    };
    return array("whispers" => $whispers, "imgPath" => $imgPath);
});

Route::middleware('auth')->get('/api/whispers/', 'WhisperController@index');

Route::middleware('auth')->get('/api/whispers/myprofile/', 'WhisperController@show');

Route::middleware('auth')->post('/api/whispers/', 'WhisperController@store');

// user
Route::delete('/api/users/{id}', 'UserController@destroy');

Route::middleware('auth')->put('/api/users/{id}', 'UserController@update');

// good
Route::middleware('auth')->get('api/good/', 'GoodController@index');

Route::middleware('auth')->post('api/good/p/{id}', 'GoodController@store');

Route::middleware('auth')->post('api/good/m/{id}', 'GoodController@deStore');
