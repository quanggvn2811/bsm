<?php

use App\Http\Controllers\Backend\ShopeeConnectionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'shopee_connection',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('/{associated_session?}', [ShopeeConnectionController::class, 'index'])->name('shopee_connection.index');
});
