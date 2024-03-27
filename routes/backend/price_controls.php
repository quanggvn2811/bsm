<?php

use App\Http\Controllers\Backend\PriceControlController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'price_controls',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('stock/{stock}/{associated_session?}', [PriceControlController::class, 'index'])->name('price_controls.index');
    Route::post('save_shop_price_control/{associated_session?}', [PriceControlController::class, 'saveShopPriceControl'])->name('price_controls.save_shop_price_control');
});
