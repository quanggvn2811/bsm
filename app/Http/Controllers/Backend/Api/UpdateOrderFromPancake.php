<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateOrderFromPancake extends Controller
{
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

        $this->insertOrderFromPancake($bsmShopId, $orderData, $pancakeShopId);

        return response()->json(['status' => true, 'message' => 'Update from pancake successfully.']);
    }

    private function insertOrderFromPancake($bsmShopId, $pancakeOrderData, $pancakeShopId)
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
            })->orWhereHas('customer', function ($query) use ($pancakeOrderDate, $orderData) {
                $query->where('order_date', $pancakeOrderDate);
                $query->where('customers.phone', $orderData->customer->phone_numbers[0]);
            })->first();

            if ($order) {
                if ('SYSTEM' === $order->created_by) { // Has create by system
                    // Check and update phone
                    $customer = $order->customer;
                    if ($customer && $customer->phone !== $orderData->customer->phone_numbers[0]) {
                        $customer->phone = $orderData->customer->phone_numbers[0];
                        $customer->save();
                    }
                } else {
                    // Created by admin, now just skip, no update here
                }
            } else { // Create
                // Do create by system
                $this->systemCreateOrderFromPancake($pancakeShopId, $orderData, $bsmShopId);
            }
        }
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

                $order['status_id'] = Order::STATUS_WAITING;

                $order['total'] = 0;

                $order['ship_by_customer'] = 0;

                $order['ship_by_shop'] = 0;

                $order['cost'] = 0;

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
