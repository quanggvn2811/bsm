<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProductVariation;
use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Http\Request;

class ShopeeConnectionController extends Controller
{
    public function index (Request $request)
    {
        $shops = Shop::all();
        $stock = Stock::whereName('MAKE STOCK')->first();
        return view('backend.shopee_connection.index')
            ->with('shops', $shops)
            ->with('stock', $stock)
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
}
