<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\SuppliersProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Type;

class ProductController extends Controller
{
    public function index(Request $request, Stock $stock)
    {
        $suppliers = Supplier::whereStockId($stock->id)->get();
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $categories = Category::whereStockId($stock->id)->get();

        $category = $request->get('prod_category');
        if ($category) {
            $products = Product::where('category_id', $category);
        } else {
            $products = Product::whereIn('category_id', $categoryInStock);
        }

        $name = $request->get('prod_name');
        if ($name) {
            $products = $products->where('name', 'like', '%' . $name . '%');
        }

        $sku = $request->get('prod_sku');
        if ($sku) {
            $products = $products->where('sku', 'like', '%' . $sku . '%');
        }

        $supplier = $request->get('prod_supplier');

        if ($supplier) {
            $products = $products->whereHas('product_supplier', function ($query) use ($supplier) {
                $query->whereSupplierId($supplier);
            });
        }

        $quantity = $request->get('prod_quantity');
        if ($quantity) {
            $calculation = $request->get('prod_calculation');
            $products = $products->where('quantity', $calculation, $quantity);
        }

        $salesStatus = $request->get('sales_product_status');

        if ($salesStatus) {
            $products = $products->where('sales_status', $salesStatus);
        } else {
            $products = $products->where('sales_status', Product::SALES_PRODUCT_STATUS_SELLING);
        }

        $products = $products->with('category')->paginate(config('app.page_count'));

        return view('backend.product.index')
            ->withStock($stock)
            ->withProducts($products)
            ->withCategories($categories)
            ->withSuppliers($suppliers)
            ->withSalesStatus(Product::SALES_PRODUCT_STATUS)
            ->withPancakeShopId(config('pancake.pancake_shop_id'))
            ->withShops(Shop::all())
            ;
    }

    public function create(Request $request, Stock $stock)
    {
        $suppliers = Supplier::whereStockId($stock->id)->get();
        $categories = Category::whereStockId($stock->id)->get();
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $skus = Product::whereIn('category_id', $categoryInStock)->whereNotNull('sku')->pluck('sku')->toArray();

        return view('backend.product.add_edit_product')
            ->withStock($stock)
            ->withSuppliers($suppliers)
            ->withCategories($categories)
            ->withSkus($skus)
            ;
    }

    public function store(Request $request, Stock $stock)
    {
        // Todo:: add stock prefix to sku to make sku unique in all
        $data = $request->only([
            'name',
            // 'slug',
            'description',
            'images',
            'status',
            'sku',
            'supplier_sku',
            'cost',
            'price',
            'category_id',
            //'supplier_id',
            'quantity',
            'type',
            'sales_status',
        ]);

        $data['supplier_id'] = $request->get('prod_suppliers')[0]['id'] ?? 1;

        if ($data['type'] && Product::TYPE_MULTIPLE === intval($data['type']) && $request->get('sub_product_sku')) {
            $subProdSkus = explode(';', $request->get('sub_product_sku'));
            $subProdSkuArr = array_map('trim', $subProdSkus);
            $subProdIds = Product::whereIn('sku', $subProdSkuArr)->pluck('id')->toArray();
            if (count($subProdIds)) {
                $data['sub_product_id'] = json_encode($subProdIds);
            }
        }

        $prodImages = [];

        if ($request->hasFile('images')) {
            foreach($request->file('images') as $img)
            {
                $imgName = Date('YmdHis') . '_' . $img->getClientOriginalName();
                $img->move(public_path(Product::PUBLIC_PROD_IMAGE_FOLDER), $imgName);
                $prodImages[] = $imgName;
            }
        } elseif ($request->get('quick_prod_avatar_src')) {
            $prodImages[] = $request->get('quick_prod_avatar_src');
        }

        $data['images'] = json_encode($prodImages);

        $data['slug'] = Str::slug($data['name']);

        $product = Product::create($data);

        if ($product) {
            $productSuppliers = $request->get('prod_suppliers');
            if (count($productSuppliers)) {
                foreach ($productSuppliers as $pSupplier) {
                    SuppliersProduct::create([
                        'supplier_id' => $pSupplier['id'],
                        'product_id' => $product->id,
                        's_cost' => $pSupplier['cost'],
                        's_sku' => $pSupplier['sku'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index', ['stock' => $stock->id]);
    }

    public function edit(Request $request, Stock $stock, Product $product)
    {
        $suppliers = Supplier::whereStockId($stock->id)->get();
        $categories = Category::whereStockId($stock->id)->get();
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $skus = Product::whereIn('category_id', $categoryInStock)->whereNotNull('sku')->pluck('sku')->toArray();
        $subProductSku = '';
        if (Product::TYPE_MULTIPLE === $product->type) {
            $subProductIds = json_decode($product->sub_product_id);
            if (!empty($subProductIds)) {
                $subProducts = Product::whereIn('id', $subProductIds)->pluck('sku')->toArray();
                $subProductSku = implode('; ', $subProducts);
            }
        }

        $productSuppliers = SuppliersProduct::whereProductId($product->id)->get();

        return view('backend.product.add_edit_product')
            ->withStock($stock)
            ->withSuppliers($suppliers)
            ->withCategories($categories)
            ->withProduct($product)
            ->withSubProductSku($subProductSku)
            ->withProductSuppliers($productSuppliers)
            ->withSkus($skus)
            ;
    }

    public function update(Request $request, Stock $stock, Product $product)
    {
        $data = $request->only([
            'name',
            // 'slug',
            'description',
            'images',
            'status',
            'sku',
            'supplier_sku',
            'cost',
            'price',
            'category_id',
            // 'supplier_id',
            'quantity',
            'type',
            'sales_status',
        ]);

        $data['supplier_id'] = $request->get('prod_suppliers')[0]['id'] ?? 1;

        if ($data['type'] && Product::TYPE_MULTIPLE === intval($data['type']) && $request->get('sub_product_sku')) {
            $subProdSkus = explode(';', $request->get('sub_product_sku'));
            $subProdSkuArr = array_map('trim', $subProdSkus);
            $subProdIds = Product::whereIn('sku', $subProdSkuArr)->pluck('id')->toArray();
            if (count($subProdIds)) {
                $data['sub_product_id'] = json_encode($subProdIds);
            }
        }

        $prodImages = [];

        if ($request->hasFile('images')) {
            foreach($request->file('images') as $img)
            {
                $imgName = Date('YmdHis') . '_' . $img->getClientOriginalName();
                $img->move(public_path(Product::PUBLIC_PROD_IMAGE_FOLDER), $imgName);
                $prodImages[] = $imgName;
            }
        } elseif(!empty(json_decode($product->images))) {
            $prodImages = json_decode($product->images);
        }

        $data['images'] = json_encode($prodImages);

        $data['slug'] = Str::slug($data['name']);

        $product->update($data);

        $productSuppliers = $request->get('prod_suppliers');
        if (count($productSuppliers)) {
            SuppliersProduct::whereProductId($product->id)->delete();

            foreach ($productSuppliers as $pSupplier) {
                SuppliersProduct::create([
                    'supplier_id' => $pSupplier['id'],
                    'product_id' => $product->id,
                    's_cost' => $pSupplier['cost'],
                    's_sku' => $pSupplier['sku'],
                ]);
            }
        }

        return redirect()->route('admin.products.index', ['stock' => $stock->id]);
    }

    public function updateQuantity(Request $request, Product $product)
    {
        $plusVal = $request->get('plus_value');
        $product->update([
            'quantity' => $product->quantity + $plusVal,
        ]);

        return response()->json([
            'status' => 'Update quantity successfully!',
            'product_quantity' => $product->quantity,
        ]);
    }

    public function updateCheckedDate(Request $request, Product $product)
    {
        $product->update([
            'checked_date' => $request->get('checked_date'),
        ]);

        return response()->json([
            'status' => 'Update checked date successfully!',
        ]);
    }

    public function updateSalesStatus(Request $request, Product $product)
    {
        $product->update([
            'sales_status' => $request->get('sales_status'),
        ]);

        return response()->json([
            'status' => 'Update sales status successfully!',
        ]);
    }

    public function updateSupplier(Request $request, Product $product)
    {
        $product->first_product_supplier()->update(['supplier_id' => $request->get('supplier_id')]);

        return response()->json([
            'status' => 'Update supplier successfully!',
        ]);
    }

    public function quickUpdateCost(Request $request, Product $product)
    {
        $product->update(['cost' => $request->get('cost')]);

        return response()->json([
            'status' => 'Update cost successfully!',
        ]);
    }

    public function destroy(Product $product)
    {
        if (!$product->delete()) {
            redirect()->back()->withFlashDanger('Something went wrong!');
        }

        return redirect()->back()->withFlashSuccess('Deleted "' . $product->name . '"');
    }

    public function quickCreate(Request $request, Stock $stock)
    {
        $productData = $request->only([
            'name',
            'cost',
            'price',
            'quantity',
        ]);


        $defaultCategory = Category::firstOrCreate([
            'name' => Category::DEFAULT_CATEGORY_NAME,
            'stock_id' => $stock->id,
            'sku' => Category::DEFAULT_CATEGORY_NAME,
        ]);

        $defaultSupplier = Supplier::firstOrCreate([
            'name' => Supplier::DEFAULT_SUPPLIER_NAME,
            'stock_id' => $stock->id,
        ]);

        $productData['category_id'] = $defaultCategory->id;

        $productData['supplier_id'] = $defaultSupplier->id;

        $productData['images'] = json_encode([]);

        $product = Product::create($productData);

        if ($product) {
            SuppliersProduct::create([
                'supplier_id' => $defaultSupplier->id,
                'product_id' => $product->id,
                's_cost' => $productData['cost'],
            ]);
        }

        return response()->json(['status' => 'true', 'message' => 'Product created successfully!', 'product' => $product]);
    }
}
