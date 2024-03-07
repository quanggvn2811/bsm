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
    Route::post('{order}/update_priority/{associated_session?}', [OrderController::class, 'updatePriority'])->name('orders.update_priority');
    Route::post('{order}/update_status/{associated_session?}', [OrderController::class, 'updateStatus'])->name('orders.update_priority');
});
