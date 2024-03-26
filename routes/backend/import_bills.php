<?php

use App\Http\Controllers\Backend\ImportBillController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'import_bills',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('stock/{stock}/add/{associated_session?}', [ImportBillController::class, 'create'])->name('import_bills.create');
    Route::get('stock/{stock}/{associated_session?}', [ImportBillController::class, 'index'])->name('import_bills.index');
    Route::post('stock/{stock}/add/{associated_session?}', [ImportBillController::class, 'store'])->name('import_bills.store');
    Route::get('stock/{stock}/edit/{importBill}/{associated_session?}', [ImportBillController::class, 'edit'])->name('import_bills.edit');
    Route::post('stock/{stock}/edit/{importBill}/{associated_session?}', [ImportBillController::class, 'update'])->name('import_bills.update');
    Route::delete('stock/{stock}/delete/{importBill}/{associated_session?}', [ImportBillController::class, 'destroy'])->name('import_bills.destroy');
});
