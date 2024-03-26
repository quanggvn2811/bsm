<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ImportBill;
use App\Models\ImportBillProduct;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request, Stock $stock)
    {
        if (!$request->get('import_bill_products')) {
            return redirect()->back()->withFlashDanger('Add a product item pls.');
        }

        try {
            DB::transaction(function () use ($request, $stock) {
                $importBillData['supplier_id'] = $request->get('supplier_id');
                $importBillData['date'] = $request->get('order_date');
                $importBillData['total'] = $request->get('total_bill');
                $importBillData['notes'] = $request->get('notes');

                $importBill = ImportBill::create($importBillData);

                $importProductArr = explode('_', $request->get('import_bill_products'));
                foreach ($importProductArr as $importProduct) {
                    list($prodId, $quantity, $costItem) = explode(',', $importProduct);
                    ImportBillProduct::create([
                        'product_id' => $prodId,
                        'quantity' => $quantity,
                        'price_item' => $costItem,
                        'import_bill_id' => $importBill->id,
                    ]);

                    Product::find($prodId)->increment('quantity', $quantity);

                }
            });

            return redirect()->back()->withFlashSuccess('Added a bill!');
            //return redirect()->route('admin.orders.index', ['stock' => $stock->id])->withFlashSuccess('Added an order!');

        } catch (\Exception $e) {
            return redirect()->back()->withFlashDanger('Somethings went wrong!. Please content your admin.');
        }
    }

    public function edit(Request $request, Stock $stock, ImportBill $importBill)
    {
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $products = Product::whereIn('category_id', $categoryInStock)->get();
        $productArray = $products->toArray();
        $productById = [];
        foreach ($productArray as $prod) {
            $productById[$prod['id']] = $prod;
        }

        $suppliers = Supplier::whereStockId($stock->id)->get();
        return view('backend.import_bill.edit')
            ->withStock($stock)
            ->withSuppliers($suppliers)
            ->withProducts($products)
            ->withProductById($productById)
            ->withImportBill($importBill)
            ;
    }

    public function update(Request $request, Stock $stock, ImportBill $importBill)
    {
        if (!$request->get('import_bill_products')) {
            return redirect()->back()->withFlashDanger('Add a product item pls.');
        }

        try {
            DB::transaction(function () use ($request, $stock, $importBill) {
                $importBillData['supplier_id'] = $request->get('supplier_id');
                $importBillData['date'] = $request->get('order_date');
                $importBillData['total'] = $request->get('total_bill');
                $importBillData['notes'] = $request->get('notes');

                $importBill->update($importBillData);

                if ($request->get('import_bill_products')) {
                    $oldImportBillProducts = ImportBillProduct::whereImportBillId($importBill->id);
                    foreach ($oldImportBillProducts->get() as $oldProduct) {
                        Product::find($oldProduct->product_id)->decrement('quantity', $oldProduct->quantity);
                    }

                    $oldImportBillProducts->delete();

                    // Add new
                    $importProductArr = explode('_', $request->get('import_bill_products'));
                    foreach ($importProductArr as $importProduct) {
                        list($prodId, $quantity, $costItem) = explode(',', $importProduct);
                        ImportBillProduct::create([
                            'product_id' => $prodId,
                            'quantity' => $quantity,
                            'price_item' => $costItem,
                            'import_bill_id' => $importBill->id,
                        ]);

                        Product::find($prodId)->increment('quantity', $quantity);

                    }
                }
            });

            return redirect()->back()->withFlashSuccess('Edited a bill!');
            //return redirect()->route('admin.orders.index', ['stock' => $stock->id])->withFlashSuccess('Added an order!');

        } catch (\Exception $e) {
            return redirect()->back()->withFlashDanger('Somethings went wrong!. Please content your admin.');
        }
    }
}
