<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {

    }

    public function create(Request $request, Stock $stock)
    {
        $shops = Shop::all();
        $suppliers = Supplier::whereStockId($stock->id)->get();
        $categories = Category::whereStockId($stock->id)->get();
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $products = Product::whereIn('category_id', $categoryInStock)->get();
        return view('backend.order.add_order')
            ->withStock($stock)
            ->withSuppliers($suppliers)
            ->withCategories($categories)
            ->withShops($shops)
            ->withProducts($products)
            ;
    }
}
