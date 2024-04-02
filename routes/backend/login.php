<?php

use App\Http\Controllers\Backend\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'login',
], function () {
    Route::get('/{associated_session?}', [LoginController::class, 'index'])->name('login.index');
});
