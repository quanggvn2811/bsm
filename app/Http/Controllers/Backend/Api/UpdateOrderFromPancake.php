<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class UpdateOrderFromPancake extends Controller
{

    const SHOPEE_STATUS_FAILED = ['canceled', 'CANCELED', 'returning', 'returned'];

    const SHOPEE_STATUS_COMPLETED = ['COMPLETED', 'completed', 'delivered', 'DELIVERED'];
    const POS_STATUS_SHIPPED = ['SHIPPED', 'shipped'];
    public function getPancakeOrders (Request $request)
    {
        $pancakeShopId = $request->get('pancake_shop_id');

        $posCakeApiKey = config('pancake.pancake_shop_api_key')[$pancakeShopId];

        if (!$posCakeApiKey | !$pancakeShopId) {
            return response()->json(['status' => false, 'message' => 'PancakeShopId & Pos cake Api key is required.']);
        }

        $bsmPrefix = null;
        foreach (config('pancake.pancake_shop_id') as $prefix => $id) {
            if ($pancakeShopId === $id) {
                $bsmPrefix = $prefix;
                break;
            }
        }

        $bsmShopId = Shop::wherePrefix($bsmPrefix)->first()->id;

        if (!$bsmPrefix || !$bsmShopId) {
            return response()->json(['status' => false, 'message' => 'BSM shop id not found.']);
        }

        $dateFrom = Carbon::createFromFormat(config('app.date_format'), $request->get('date_from'))->startOfDay()->timestamp;
        $dateTo = Carbon::createFromFormat(config('app.date_format'), $request->get('date_to'))->endOfDay()->timestamp;

        // Pos cake base url api
        $url = 'https://pos.pages.fm/api/v1/';

        $url .= 'shops/' . $pancakeShopId . '/orders/';

        $url .= '?api_key=' . $posCakeApiKey;

        if ($dateFrom) {
            $url .= '&startDateTime=' . $dateFrom;
        }

        if ($dateTo) {
            $url .= '&endDateTime=' . $dateTo;
        }

        $url .= '&page_size=1000'; // max limit

        // $response = $this->pancakeCurl($url);

        $response = file_get_contents($url);

        $orderData = json_decode($response)->data;

        $orderData = array_reverse($orderData);

        $isShopee = str_starts_with($bsmPrefix, 'SP_');

        $this->insertOrderFromPancake($bsmShopId, $orderData, $pancakeShopId, $isShopee);

        return response()->json(['status' => true, 'message' => 'Update from pancake successfully.']);
    }

    private function insertOrderFromPancake($bsmShopId, $pancakeOrderData, $pancakeShopId, $isShopee = false)
    {
        foreach ($pancakeOrderData as $orderData) {
            $pancakeOrderDate = Carbon::parse($orderData->inserted_at)->addHours(7)->format(config('app.date_format'));

            // Now, just detect that order is created by system
            /*$order = Order::whereOrderDate($pancakeOrderDate)
                            ->wherePancakeShopId($pancakeShopId)
                            ->wherePancakeShopOrderId($orderData->system_id)
                            ->first()
                        ;*/

            $order = Order::where(function ($query) use ($pancakeOrderDate, $pancakeShopId, $orderData) {
                $query->where('order_date', $pancakeOrderDate);
                $query->where('pancake_shop_id', $pancakeShopId);
                $query->where('pancake_shop_order_id', $orderData->system_id);
            })->orWhereHas('customer', function ($query) use ($pancakeOrderDate, $orderData, $bsmShopId) {
                $query->where('shop_id', $bsmShopId);
                $query->where('order_date', $pancakeOrderDate);
                $query->where('customers.phone', $orderData->customer->phone_numbers[0]);
            })->first();

            // Is update
            if ($order) {
                if ('SYSTEM' === $order->created_by && 'SYSTEM' === $order->last_updated_by) { // Has create by system
                    // Check and update phone
                    $customer = $order->customer;
                    // Zalo order Facebook
                    if (!$isShopee) {
                        if ($customer && $customer->phone !== $orderData->customer->phone_numbers[0]) {
                            $customer->phone = $orderData->customer->phone_numbers[0];
                            $customer->save();
                        }

                        // Update order data
                        $order->total = $orderData->total_price;
                        $order->ship_by_shop = $orderData->partner_fee;
                        $order->ship_by_customer = $orderData->shipping_fee;
                        // $order->save();
                    }

                    // Update order for Shopee (status, money to collect)
                    if ($isShopee) {
                        if ($orderData->money_to_collect) {
                            // If something change
                            $order->total = $orderData->money_to_collect;
                        }
                    }

                    $order->cost = $this->getPosCakeCostItems($orderData);

                    // Update status
                    // Auto = process, canceled = failed,
                    $autoStatus = $isShopee ? Order::STATUS_PROCESS : Order::STATUS_WAITING;
                    if (in_array($orderData->status_name, self::SHOPEE_STATUS_FAILED)) {
                        $autoStatus = Order::STATUS_FAILED;
                    }

                    if (in_array($orderData->status_name, self::POS_STATUS_SHIPPED)) {
                        $autoStatus = Order::STATUS_SHIPPED;
                    }

                    if (in_array($orderData->status_name, self::SHOPEE_STATUS_COMPLETED)) {
                        $autoStatus = Order::STATUS_COMPLETED;
                    }

                    $canChangeQuantity = !(Order::STATUS_FAILED == $autoStatus && $order->status_id == Order::STATUS_FAILED);
                    if ($canChangeQuantity) {
                        // Auto remove old
                        $oldOderDetail = OrderDetail::whereOrderId($order->id);
                        foreach ($oldOderDetail->get() as $oldDetail) {
                            $product = Product::find($oldDetail->product_id);
                            if ($product) {
                                $product->quantity = $product->quantity + $oldDetail->quantity;
                                $product->save();
                            }
                        }
                        $oldOderDetail->delete();

                        // Add new
                        foreach ($orderData->items as $item) {
                            $variation = ProductVariation::whereVariationId($item->variation_id)->first();
                            if (isnull($variation)) {
                                continue;
                            }
                            $product = Product::find($variation->product_id);
                            if ($variation && $variation->product_id && $product) {
                                $price = $item->variation_info->retail_price - $item->discount_each_product;
                                $cost = $item->variation_info->last_imported_price;
                                $quantity = $item->quantity * $variation->product_quantity;

                                /*if (Order::STATUS_FAILED == $order->status_id) {
                                    $quantity = 0;
                                }*/

                                OrderDetail::create([
                                    'product_id' => $product->id,
                                    'quantity' => $quantity,
                                    'cost_item' => $cost,
                                    'price_item' => $price,
                                    'order_id' => $order->id,
                                ]);

                                if (Order::STATUS_FAILED == $order->status_id) {
                                    $quantity = 0;
                                }

                                Product::find($product->id)->decrement('quantity', $quantity);

                            }
                        }
                    }

                    $order->status_id = $autoStatus;

                    $order->save();

                } else {
                    // Created by admin, now just skip, no update here
                }
            } else { // Create
                // Do create by system
                $isShopee ? $this->systemCreateShopeeOrderFromPancake($pancakeShopId, $orderData, $bsmShopId)
                    : $this->systemCreateOrderFromPancake($pancakeShopId, $orderData, $bsmShopId)
                ;
            }
        }
    }

    protected function getPosCakeCostItems($posCakeOrderData)
    {
        $items = $posCakeOrderData->items;
        $cost = 0;

        foreach ($items as $item) {
            $cost += $item->variation_info->last_imported_price * $item->quantity;
        }

        return $cost;
    }

    protected function systemCreateOrderFromPancake($pancakeShopId, $pancakeOrderData, $bsmShopId)
    {
        try {
            DB::transaction(function () use ($pancakeShopId, $pancakeOrderData, $bsmShopId) {
                $pancakeCustomerPhone = $pancakeOrderData->customer->phone_numbers[0];
                $pancakeCustomerName = $pancakeOrderData->customer->name;
                // Customer
                $customer = Customer::wherePhone($pancakeCustomerPhone)->first();

                if (!$customer) {
                    $customer = Customer::create([
                        'name' => $pancakeCustomerName,
                        'phone' => $pancakeCustomerPhone,
                        'address' => $pancakeOrderData->customer->shop_customer_addresses[0]->address ?? '',
                        'info_url' => '',
                        'more_info' => '',
                    ]);
                }

                // Store Order
                $order['priority'] = Order::PRIORITY_NORMAL;

                $status = Order::STATUS_WAITING;

                if (in_array($pancakeOrderData->status_name, self::POS_STATUS_SHIPPED)) {
                    $status = Order::STATUS_SHIPPED;
                }

                if (in_array($pancakeOrderData->status_name, self::SHOPEE_STATUS_FAILED)) {
                    $status = Order::STATUS_FAILED;
                }

                if (in_array($pancakeOrderData->status_name, self::SHOPEE_STATUS_COMPLETED)) {
                    $status = Order::STATUS_COMPLETED;
                }

                $order['status_id'] = $status;

                $order['total'] = $pancakeOrderData->total_price;

                $order['ship_by_customer'] = $pancakeOrderData->shipping_fee;

                $order['ship_by_shop'] = $pancakeOrderData->partner_fee;

                $order['cost'] = $this->getPosCakeCostItems($pancakeOrderData);

                $order['notes'] = '';

                $orderDate = Carbon::parse($pancakeOrderData->inserted_at)->addHours(7)->format(config('app.date_format'));
                $order['order_date'] = $orderDate;

                $order['order_address'] = $pancakeOrderData->customer->shop_customer_addresses[0]->address ?? '';

                $order['customer_id'] = $customer->id;

                $shop = Shop::find($bsmShopId);

                $order['shop_id'] = $shop->id;

                $orderNumber = $shop->prefix . '_' . date('ymdHis');

                $order['order_number'] = $orderNumber;

                // Store pancake data
                $order['pancake_shop_id'] = $pancakeShopId;

                $order['pancake_shop_order_id'] = $pancakeOrderData->system_id;

                $order['created_by'] = 'SYSTEM';

                $order['pancake_shop_order_link'] = $pancakeOrderData->order_link;

                $order['last_updated_by'] = 'SYSTEM';

                $order = Order::create($order);

                // Update order_number
                $orderNumber = $shop->prefix . '_' . date('ym') . sprintf("%04d", substr($order->id, -4));

                $order->update(['order_number' => $orderNumber]);

                foreach ($pancakeOrderData->items as $item) {
                    $variation = ProductVariation::whereVariationId($item->variation_id)->first();
                    if ($variation && $variation->product_id) {
                        $product = Product::find($variation->product_id);
                        $price = $item->variation_info->retail_price - $item->discount_each_product;
                        $cost = $item->variation_info->last_imported_price;
                        $quantity = $item->quantity * $variation->product_quantity;

                        /*if (Order::STATUS_FAILED == $status) {
                            $quantity = 0;
                        }*/

                        OrderDetail::create([
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'cost_item' => $cost,
                            'price_item' => $price,
                            'order_id' => $order->id,
                        ]);

                        if (Order::STATUS_FAILED == $status) {
                            $quantity = 0;
                        }

                        Product::find($product->id)->decrement('quantity', $quantity);

                    }
                }
            });
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    protected function systemCreateShopeeOrderFromPancake($pancakeShopId, $pancakeOrderData, $bsmShopId)
    {
        try {
            DB::transaction(function () use ($pancakeShopId, $pancakeOrderData, $bsmShopId) {
                $pancakeCustomerPhone = $pancakeOrderData->customer->phone_numbers[0];
                $pancakeCustomerUsername = $pancakeOrderData->customer->username;

                // Replace ****** = first 7 characters in username
                $first7Characters = substr($pancakeCustomerUsername, 0, 7);
                $pancakeCustomerPhone = substr($pancakeCustomerPhone, 0, 1) . $first7Characters . substr($pancakeCustomerPhone, -2);
                // Customer
                $customer = Customer::wherePhone($pancakeCustomerPhone)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'name' => $pancakeCustomerUsername,
                        'phone' => $pancakeCustomerPhone,
                        'address' => $pancakeOrderData->customer->shop_customer_addresses[0]->address ?? '',
                        'info_url' => '',
                        'more_info' => '',
                    ]);
                }

                // Store Order
                $order['priority'] = Order::PRIORITY_NORMAL;


                $status = Order::STATUS_PROCESS;

                if (in_array($pancakeOrderData->status_name, self::POS_STATUS_SHIPPED)) {
                    $status = Order::STATUS_SHIPPED;
                }

                if (in_array($pancakeOrderData->status_name, self::SHOPEE_STATUS_FAILED)) {
                    $status = Order::STATUS_FAILED;
                }

                if (in_array($pancakeOrderData->status_name, self::SHOPEE_STATUS_COMPLETED)) {
                    $status = Order::STATUS_COMPLETED;
                }

                $order['status_id'] = $status;

                $order['total'] = $pancakeOrderData->money_to_collect ?? 0;

                $order['ship_by_customer'] = 0;

                $order['ship_by_shop'] = 0;

                $items = $pancakeOrderData->items;
                $cost = 0;

                foreach ($items as $item) {
                    $cost += $item->variation_info->last_imported_price * $item->quantity;
                }

                $order['cost'] = $cost;

                $order['notes'] = '';

                $orderDate = Carbon::parse($pancakeOrderData->inserted_at)->addHours(7)->format(config('app.date_format'));
                $order['order_date'] = $orderDate;

                $order['order_address'] = $pancakeOrderData->customer->shop_customer_addresses[0]->address ?? '';

                $order['customer_id'] = $customer->id;

                $shop = Shop::find($bsmShopId);

                $order['shop_id'] = $shop->id;

                $orderNumber = $shop->prefix . '_' . date('ymdHis');

                $order['order_number'] = $orderNumber;

                // Store pancake data
                $order['pancake_shop_id'] = $pancakeShopId;

                $order['pancake_shop_order_id'] = $pancakeOrderData->system_id;

                $order['created_by'] = 'SYSTEM';

                $order['pancake_shop_order_link'] = $pancakeOrderData->order_link;

                $order['last_updated_by'] = 'SYSTEM';

                $order = Order::create($order);

                // Update order_number
                $orderNumber = $shop->prefix . '_' . date('ym') . sprintf("%04d", substr($order->id, -4));

                $order->update(['order_number' => $orderNumber]);

                foreach ($items as $item) {
                    $variation = ProductVariation::whereVariationId($item->variation_id)->first();
                    if ($variation && $variation->product_id) {
                        $product = Product::find($variation->product_id);
                        $price = $item->variation_info->retail_price - $item->discount_each_product;
                        $cost = $item->variation_info->last_imported_price;
                        $quantity = $item->quantity * $variation->product_quantity;

                        /*if (Order::STATUS_FAILED == $status) {
                            $quantity = 0;
                        }*/

                        OrderDetail::create([
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'cost_item' => $cost,
                            'price_item' => $price,
                            'order_id' => $order->id,
                        ]);

                        if (Order::STATUS_FAILED == $status) {
                            $quantity = 0;
                        }

                        Product::find($product->id)->decrement('quantity', $quantity);

                    }
                }

            });
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    private function pancakeCurl($url, $method = 'GET')
    {
        //building headers for the request
        $headers = array(
            'Authorization: key=',
            'Content-Type: application/json'
        );

        // Initializing curl to open a connection
        $ch = curl_init();

        // Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);

        // setting the method as post
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        //adding headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        try {
            // finally executing the curl request
            $result = curl_exec($ch);

            // Now close the connection
            curl_close($ch);

            // and return the result
            return $result;

        } catch (Exception $e) {
            curl_close($ch);
            return false;
        }
    }
}
