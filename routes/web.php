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

//Dashboard
Route::get("/", [\App\Http\Controllers\DashBoardController::class, "indexSetup"])->middleware('auth')->name("dashboard.index");
Route::redirect("/home", "/");
Route::get("/updateDB", [\App\Http\Controllers\DashBoardController::class, "updateDB"])->middleware('auth')->name("dashboard.updateDB");
Route::get("/refreshZohoToken", [\App\Http\Controllers\DashBoardController::class, "refreshZohoToken"])->middleware('auth')->name("dashboard.refreshZohoToken");
Route::get("/sendDataZoho", [\App\Http\Controllers\DashBoardController::class, "sendDataZoho"])->middleware('auth')->name("dashboard.sendDataZoho");
Route::get("/transferDoliOcto", [\App\Http\Controllers\DashBoardController::class, "transferDoliOcto"])->middleware('auth')->name("dashboard.transferDoliOcto");
Route::get("/resetDatabase", [\App\Http\Controllers\DashBoardController::class, "resetDatabase"])->middleware('auth')->name("dashboard.resetDatabase");

//Bookings
Route::get("/bookings", [\App\Http\Controllers\BookingController::class, "index"])->middleware('auth')->name("bookings.index");

//Relations
Route::get("/relations", [\App\Http\Controllers\RelationController::class, "index"])->middleware('auth')->name("relations.index");

//Users
Route::get("/users", [\App\Http\Controllers\UserController::class, "index"])->middleware('auth')->name("users.index");
Route::get("/user/{id}", [\App\Http\Controllers\UserController::class, "show"])->middleware('auth')->name("user.show");
Route::patch("/user/{id}", [\App\Http\Controllers\UserController::class, "update"])->middleware('auth')->name("user.update");
Route::delete("/users/{id}", [\App\Http\Controllers\UserController::class, "delete"])->middleware('auth')->name("users.delete");

//Paramètres
Route::get("/params", [\App\Http\Controllers\ParamsController::class, "index"])->middleware('auth')->name("params.index");
Route::patch("/params", [\App\Http\Controllers\ParamsController::class, "update"])->middleware('auth')->name("params.update");

//Authentification
Route::get('/register', [\App\Http\Controllers\RegisterController::class, 'show'])->middleware('auth')->name('register');
Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'register'])->middleware('auth')->name('register');

Route::get('/login', [\App\Http\Controllers\LoginController::class, 'show'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');

Route::post('/logout', [\App\Http\Controllers\LogoutController::class, 'logout'])->name('logout');
