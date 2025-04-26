<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register.get');

Route::post('/register', [AuthController::class, 'store'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/', function () {
    if (auth()->user()->role == 'admin') {
        return view('admin.dashboard');
    } else {
        return view('user.dashboard');
    }
})->middleware('auth');

Route::get('/profile', function () {
    if (auth()->user()->role == 'admin') {
        return view('admin.profile');
    } else {
        return view('user.profile');
    }
})->middleware('auth');

Route::put('/update-profile', [AuthController::class, 'update'])->middleware('auth');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::group(['middleware' => 'role:admin'], function () {

});

Route::group(['middleware' => 'role:user'], function () {});
