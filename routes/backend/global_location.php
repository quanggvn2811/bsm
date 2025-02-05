<?php

use App\Http\Controllers\Backend\GlobalLocation;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'global_location',
    // 'middleware' => 'role:administrator'
], function () {
    Route::get('province/{associated_session?}', [GlobalLocation::class, 'province'])->name('global_location.province');
    Route::get('district/{code}/{associated_session?}', [GlobalLocation::class, 'getDistrictByCode'])->name('global_location.district_by_code');
});
