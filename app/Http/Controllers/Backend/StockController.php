<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index ()
    {
        $stocks = Stock::orderBy('name')->get();

        return view('backend.stock.index')
            ->withStocks($stocks)
            ;
    }

    public function store(Request $request)
    {
        $arrData = $request->only([
            'name',
            'description',
            'unique_prefix',
            'status'
        ]);

        Stock::create($arrData);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
