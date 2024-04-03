<?php

use App\Http\Controllers\Backend\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'login',
], function () {
    Route::get('/{associated_session?}', [LoginController::class, 'index'])->name('login.index');
    Route::post('/{associated_session?}', [LoginController::class, 'login'])->name('login.login');
});

Route::group([
    'prefix' => 'logout',
], function () {
    Route::get('/', [LoginController::class, 'logout'])->name('logout');
});
