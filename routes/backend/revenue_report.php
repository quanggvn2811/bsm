<?php

use App\Http\Controllers\Backend\RevenueReport;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'revenue_report',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('stock/{stock}/{associated_session?}', [RevenueReport::class, 'index'])->name('revenue_report.index');
});
