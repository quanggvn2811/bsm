<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index (Request $request, Stock $stock)
    {
        $categories = Category::whereStockId($stock->id)->orderBy('name')->get();

        return view('backend.category.index')
            ->withCategories($categories)
            ->withStock($stock)
            ;
    }

    public function store(Request $request, Stock $stock)
    {
        $arrData = $request->only([
            'name',
            'description',
            'status',
            'sku',
        ]);

        $arrData['stock_id'] = $stock->id;

        Category::create($arrData);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function update(Request $request, Stock $stock, Category $category)
    {
        $arrData = $request->only([
            'name',
            'description',
            'status',
            'sku',
        ]);

        $category->update($arrData);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
