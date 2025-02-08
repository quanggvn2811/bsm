<?php

use App\Http\Controllers\Backend\ShopeeConnectionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'shopee_connection',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('/{associated_session?}', [ShopeeConnectionController::class, 'index'])->name('shopee_connection.index');
    Route::post('/{stock}/update-product-from-pancake/{associated_session?}', [ShopeeConnectionController::class, 'updateProductFromPancake'])->name('shopee_connection.update_product_from_pancake');
    Route::post('/{stock}/update-bsm-connection/{associated_session?}', [ShopeeConnectionController::class, 'updateBsmConnection'])
        ->name('shopee_connection.update_bsm_connection');
});
