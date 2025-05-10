<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransactionController;
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


Route::post('/midtrans/callback', [TransactionController::class, 'handleCallback']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/profile', function () {
        if (auth()->user()->role == 'admin') {
            return view('admin.profile');
        } else {
            return view('user.profile');
        }
    });

    Route::put('/update-profile', [AuthController::class, 'update']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('/get-detail-transaction/{id}', [TransactionController::class, 'getDetailTransaction']);
    Route::put('/pay-transaction', [TransactionController::class, 'pay_transaction'])->name('transaction.pay');
    Route::put('/cancel-transaction/{id}', [TransactionController::class, 'cancel_transaction'])->name('transaction.cancel');

    Route::get('/sales-chart-data', [DashboardController::class, 'getChartData']);
});

Route::group(['middleware' => 'role:admin'], function () {

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{id}', [MemberController::class, 'destroy'])->name('members.delete');

    Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('/get-member-by-number/{phone}', [MemberController::class, 'getMemberByNumber'])->name('get-member-by-number');
});

Route::group(['middleware' => 'role:user'], function () {});
