<?php

use App\Http\Controllers\Backend\StockController;
use Illuminate\Support\Facades\Route;

Route::get('/{associated_session?}', function () {
    return view('backend.dashboard');
})->name('admin.dashboard');
