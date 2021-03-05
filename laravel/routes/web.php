<?php

use Illuminate\Support\Facades\Route;

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
    return view('/myprofile');
});

Route::middleware('auth')->post('/api/whispers/', 'WhisperController@store');

Route::middleware('auth')->get('/api/whispers/', 'WhisperController@index');

Route::middleware('auth')->put('/api/users/{id}', 'UserController@update');
