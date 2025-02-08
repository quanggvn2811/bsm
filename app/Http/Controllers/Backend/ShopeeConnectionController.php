<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Http\Request;

class ShopeeConnectionController extends Controller
{
    public function index (Request $request)
    {
        $shops = Shop::all();
        $shopByIds = [];
        foreach ($shops as $shop) {
            $shopByIds[$shop->id] = $shop;
        }

        $stock = Stock::whereName('MAKE STOCK')->first();

        // Request
        $productName = $request->get('product_name');
        $shopId = $request->get('shop_id');
        $linkedStatus = $request->get('linked_status');

        if (!$productName && !$shopId) {
            $productVariations = ProductVariation::with('product');

            if ($linkedStatus == 1) {
                $productVariations = $productVariations->whereNotNull('product_id');
            } elseif ($linkedStatus == 2) {
                $productVariations = $productVariations->whereNull('product_id');
            }
            $productVariations = $productVariations->paginate(config('app.page_count'));
        }

        if ($shopId) {
            $productVariations = ProductVariation::whereShopId($shopId);
            if ($productName) {
                $productVariations = $productVariations->where('variation_name', 'LIKE', '%' . $productName . '%');
                /*$productVariations = $productVariations->orWhere('fields', 'LIKE', '%' . $productName . '%');*/
            }

            if ($linkedStatus == 1) {
                $productVariations = $productVariations->whereNotNull('product_id');
            } elseif ($linkedStatus == 2) {
                $productVariations = $productVariations->whereNull('product_id');
            }

            $productVariations = $productVariations->with('product')->paginate(config('app.page_count'));
        } else if($productName) {
            $productVariations = ProductVariation::where('variation_name', 'LIKE', '%' . $productName . '%');
            if ($linkedStatus == 1) {
                $productVariations = $productVariations->whereNotNull('product_id');
            } elseif ($linkedStatus == 2) {
                $productVariations = $productVariations->whereNull('product_id');
            }
                /*->orWhere('fields', 'LIKE', '%' . json_encode($productName) . '%')*/
            $productVariations = $productVariations->with('product')
                ->paginate(config('app.page_count'));
        }
        return view('backend.shopee_connection.index')
            ->with('shops', $shops)
            ->with('stock', $stock)
            ->withProductVariations($productVariations)
            ->withShopByIds($shopByIds)
            ->withProducts(Product::all())
            ;
    }

    public function updateProductFromPancake (Request $request, Stock $stock)
    {
        $shopId = $request->get('shop_id');
        $shop = Shop::find($shopId);
        $posCakeShopId = config('pancake.pancake_shop_id')[$shop->prefix];
        $posCakeApiKey = config('pancake.pancake_shop_api_key')[$posCakeShopId];

        if (!$posCakeApiKey | !$posCakeShopId) {
            return response()->json(['status' => false, 'message' => 'PosCakeShopId & PosCake Api key is required.']);
        }

        // Pos cake base url api
        $url = 'https://pos.pages.fm/api/v1/';

        $url .= 'shops/' . $posCakeShopId . '/products/variations/';

        $url .= '?api_key=' . $posCakeApiKey;
        $pageNumber = 1;
        $pageSize = 30;
        $products = [];

        do {
            $url .= '&page_number=' . $pageNumber;
            $url .= '&page_size=' . $pageSize;

            $response = json_decode(file_get_contents($url));
            $products = array_merge($products, $response->data);
            $pageNumber++;
        } while ($pageNumber <= $response->total_pages);

        // Insert into product variations tbl
        foreach ($products as $variation) {
            try {
                $data = [
                    /*'variation_id' => $variation->id,*/
                    'variation_name' => $variation->product->name ?? '',
                    'last_imported_price' => $variation->last_imported_price ?? 0,
                    'images' => json_encode($variation->images),
                    'fields' => json_encode($variation->fields),
                    'shop_id' => $shopId,
                ];

                $productVariation = ProductVariation::whereVariationId($variation->id)->first();
                if (!$productVariation) {
                    $data['variation_id'] = $variation->id;
                    ProductVariation::create($data);
                } else {
                    $productVariation->update($data);
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }

        return response()->json(['status' => true]);
    }

    public function updateBsmConnection(Request $request, Stock $stock)
    {
        $variationId = $request->get('variation_id');
        $productId = $request->get('product_id');
        $productQuantity = $request->get('product_quantity');

        ProductVariation::find($variationId)->update([
            'product_id' => $productId,
            'product_quantity' => $request->get('product_quantity') ?? 1,
        ]);

        return response()->json(['status' => true, 'product_name' => Product::find($productId)->name ?? '', 'product_quantity' => $productQuantity ?? 1]);
    }
}
