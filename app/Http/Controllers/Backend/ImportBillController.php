<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ImportBillController extends Controller
{
    public function create(Request $request, Stock $stock)
    {
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $products = Product::whereIn('category_id', $categoryInStock)->get();
        $productArray = $products->toArray();
        $productById = [];
        foreach ($productArray as $prod) {
            $productById[$prod['id']] = $prod;
        }

        $suppliers = Supplier::whereStockId($stock->id)->get();
        return view('backend.import_bill.add')
            ->withStock($stock)
            ->withSuppliers($suppliers)
            ->withProducts($products)
            ->withProductById($productById)
            ;
    }
}
