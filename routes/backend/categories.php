<?php

use App\Http\Controllers\Backend\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'categories',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('stock/{stock}/{associated_session?}', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('stock/{stock}/add/{associated_session?}', [CategoryController::class, 'store'])->name('categories.store');
    Route::post('stock/{stock}/edit/{category}{associated_session?}', [CategoryController::class, 'update'])->name('categories.update');
});
