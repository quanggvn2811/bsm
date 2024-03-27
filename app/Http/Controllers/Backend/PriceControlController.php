<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\PriceControl;
use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Http\Request;

class PriceControlController extends Controller
{
    public function index(Stock $stock, Request $request)
    {
        $categories = Category::whereStockId($stock->id)->get();
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $products = Product::whereIn('category_id', $categoryInStock);
        $shops = Shop::whereIsActive(1)->orderBy('order')->get();

        $products = $products->orderBy('name')->paginate(config('app.page_count'));

        return view('backend.price_control.index')
            ->withStock($stock)
            ->withShops($shops)
            ->withCategories($categories)
            ->withProducts($products)
            ;
    }

    public function saveShopPriceControl(Request $request, Product $product, PriceControl $shop)
    {
        $productShop = $request->only([
            'shop_id',
            'product_id',
        ]);

        PriceControl::updateOrCreate($productShop, ['price' => $request->get('price')]);

        return response()->json(['status' => 'success']);
    }
}
