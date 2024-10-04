<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueReport extends Controller
{
    public function index(Request $request, Stock $stock)
    {
        $dateFrom = $request->get('bill_date_from');
        $dateTo = $request->get('bill_date_to');

        if (!$dateFrom && !$dateTo) {
            $month = $request->get('month') ?? now()->format('m/Y');
            $month = '01/' .$month;
            $from = Carbon::createFromFormat(config('app.date_format'), $month)->format('Y-m-d');
            $to = Carbon::createFromFormat(config('app.date_format'), $month)->endOfMonth()->format('Y-m-d');
        } else {
            $from = Carbon::createFromFormat(config('app.date_format'), $dateFrom)->format('Y-m-d');
            $to = Carbon::createFromFormat(config('app.date_format'), $dateTo)->format('Y-m-d');
        }

        // STR_TO_DATE(orders.order_date,'%d/%m/%Y')) convert string %d/%m/%Y (08/03/2024) to Y-m-d
        $orders = Order::whereBetween(DB::raw("(STR_TO_DATE(orders.order_date,'%d/%m/%Y'))"), [$from, $to]);

        $shopId = $request->get('shop_id');
        if ($shopId !== null && !in_array('0', $shopId)) {
            $orders = $orders->whereShopId($shopId);
        }

        $orders = $orders->with('customer');

        $orders = $orders->orderBy('orders.created_at', 'ASC')->get();

        $isAdmin = 'admin@admin.com' === auth()->user()->email || 'admin@bsm.com' === auth()->user()->email;
        return view('backend.revenue_report.index')
            ->withStock($stock)
            ->withOrders($orders)
            ->withIsAdmin($isAdmin)
            ->withShops(Shop::all())
            ;
    }
}
