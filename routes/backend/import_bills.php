<?php

use App\Http\Controllers\Backend\ImportBillController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'import_bills',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('stock/{stock}/add/{associated_session?}', [ImportBillController::class, 'create'])->name('import_bills.create');
});
