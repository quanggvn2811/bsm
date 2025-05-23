<?php

use App\Http\Controllers\Backend\ProductController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'products',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('stock/{stock}/add/{associated_session?}', [ProductController::class, 'create'])->name('products.create');
    Route::get('stock/{stock}/{associated_session?}', [ProductController::class, 'index'])->name('products.index');
    Route::post('stock/{stock}/add/{associated_session?}', [ProductController::class, 'store'])->name('products.store');
    Route::post('stock/{stock}/quick_add/{associated_session?}', [ProductController::class, 'quickCreate'])->name('products.quick_create');
    Route::post('stock/{stock}/edit/{product}{associated_session?}', [ProductController::class, 'update'])->name('products.update');
    Route::post('{product}/update_quantity/{associated_session?}', [ProductController::class, 'updateQuantity'])
        ->name('products.updateQuantity');
    Route::post('{product}/update_checked_date/{associated_session?}', [ProductController::class, 'updateCheckedDate'])
        ->name('products.update_checked_date');
    Route::post('{product}/update_sales_status/{associated_session?}', [ProductController::class, 'updateSalesStatus'])
        ->name('products.update_sales_status');
    Route::post('{product}/quick_update_cost/{associated_session?}', [ProductController::class, 'quickUpdateCost'])
        ->name('products.quick_update_cost');
    Route::post('{product}/update_supplier/{associated_session?}', [ProductController::class, 'updateSupplier'])
        ->name('products.update_supplier');
    Route::delete('delete/{product}/{associated_session?}', [ProductController::class, 'destroy'])
        ->name('products.destroy');
    Route::get('stock/{stock}/edit/{product}/{associated_session?}', [ProductController::class, 'edit'])->name('products.edit');
});
