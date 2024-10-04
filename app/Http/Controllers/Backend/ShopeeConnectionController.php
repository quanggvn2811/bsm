<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopeeConnectionController extends Controller
{
    public function index (Request $request)
    {
        $shops = Shop::all();
        return view('backend.shopee_connection.index')
            ->with('shops', $shops);
            ;
    }
}
