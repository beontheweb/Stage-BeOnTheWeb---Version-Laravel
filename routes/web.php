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

Route::get("/", [\App\Http\Controllers\DashBoardController::class, "index"])->middleware('auth')->name("index.dashboard");
Route::redirect("/home", "/");

Route::get('/register', [\App\Http\Controllers\RegisterController::class, 'show'])->name('register');
Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'register'])->name('register');

Route::get('/login', [\App\Http\Controllers\LoginController::class, 'show'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');

Route::post('/logout', [\App\Http\Controllers\LogoutController::class, 'logout'])->name('logout');
