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
    return redirect()->route('login');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Для авторизованных пользователей
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::resource('documents', App\Http\Controllers\DocumentController::class);

    Route::resource('document-templates', App\Http\Controllers\DocumentTemplateController::class);

    Route::resource('users', \App\Http\Controllers\UsersController::class);

});
