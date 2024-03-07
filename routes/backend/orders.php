<?php

use App\Http\Controllers\Backend\OrderController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'orders',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('stock/{stock}/add_order/{associated_session?}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('stock/{stock}/add_order/{associated_session?}', [OrderController::class, 'store'])->name('orders.store');
    Route::get('stock/{stock}/{associated_session?}', [OrderController::class, 'index'])->name('orders.index');
});
