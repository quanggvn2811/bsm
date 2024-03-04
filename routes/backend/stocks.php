<?php

use App\Http\Controllers\Backend\StockController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'stocks',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('/{associated_session?}', [StockController::class, 'index'])->name('stock.index');
    Route::post('add/{associated_session?}', [StockController::class, 'store'])->name('stock.store');
});
