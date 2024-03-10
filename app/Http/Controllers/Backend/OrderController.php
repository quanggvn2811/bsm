<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ShippingUnit;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Supplier;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class OrderController extends Controller
{
    public function index(Request $request, Stock $stock)
    {
        $from = today()->subDays(7)->format('d/m/Y');
        $to = today()->format('d/m/Y');

        $orders = Order::where('order_date', '>=', $from)
            ->where('order_date', '<=', $to)
            ;

        $customerName = $request->get('customer_name');
        $customerPhone = $request->get('customer_phone');
        $orderNumber = $request->get('order_number');

        if ($customerName) {
            $orders = $orders->whereHas('customer', function ($query) use ($customerName) {
                $query->where('customers.name', 'like', '%' . $customerName . '%');
            });
        }

        if ($customerPhone) {
            $orders = $orders->whereHas('customer', function ($query) use ($customerPhone) {
                $query->where('customers.phone', 'like', '%' . $customerPhone . '%');
            });
        }

        if ($orderNumber) {
            $orders = $orders->where('orders.order_number', 'like', '%' . $orderNumber . '%');
        }

        $shopId = $request->get('shop_id');
        if (!$shopId) {
            $shopId = Shop::wherePrefix('MDS')->first()->id;
        }

        $orders = $orders->whereShopId($shopId);

        $priority = $request->get('priority');
        if ($priority) {
            $orders = $orders->wherePriority($priority);
        }

        $statusId = $request->get('status_id');
        if ($statusId) {
            $orders = $orders->whereStatusId($statusId);
        }

        $orders = $orders->with('customer');

        $orders = $orders->orderBy('orders.created_at', 'ASC')->paginate(10);

        return view('backend.order.index')
            ->withStock($stock)
            ->withOrders($orders)
            ->withShippingUnits(ShippingUnit::all())
            ->withShops(Shop::all())
            ;
    }

    public function create(Request $request, Stock $stock)
    {
        $shops = Shop::all();
        $suppliers = Supplier::whereStockId($stock->id)->get();
        $categories = Category::whereStockId($stock->id)->get();
        $categoryInStock = Category::whereStockId($stock->id)->pluck('id')->toArray();
        $products = Product::whereIn('category_id', $categoryInStock)->get();
        $productArray = $products->toArray();
        $productById = [];
        foreach ($productArray as $prod) {
            $productById[$prod['id']] = $prod;
        }

        return view('backend.order.add_order')
            ->withStock($stock)
            ->withSuppliers($suppliers)
            ->withCategories($categories)
            ->withShops($shops)
            ->withProducts($products)
            ->withProductById($productById)
            ;
    }

    public function store(Request $request, Stock $stock)
    {
        try {
            $data = $request->all();

            DB::transaction(function () use ($request, $data, $stock) {
                // Customer
                $customerData = [
                    'name' => $data['customer_name'],
                    'phone' => $data['customer_phone'],
                    'address' => $data['customer_address'],
                    'info_url' => $data['customer_url'],
                    'more_info' => $data['customer_more_info'],
                ];

                $customer = Customer::wherePhone($customerData['phone'])->first();

                if (!$customer) {
                    $customer = Customer::create($customerData);
                }

                // Store Order
                $order = $request->only([
                    //'order_date',
                    'priority',
                    'status_id',
                    'total',
                    'ship_by_customer',
                    'ship_by_shop',
                    'cost',
                    'notes',
                ]);

                $orderDate = $request->get('order_date');

                // $order['order_date'] = Carbon::parse($orderDate)->format('Y-m-d');

                $order['order_date'] = $orderDate;

                $order['order_address'] = $request->get('customer_address');

                $order['customer_id'] = $customer->id;

                $shop = Shop::find($request->get('shop_id'));

                $order['shop_id'] = $shop->id;

                $startOfMonth = Carbon::createFromFormat(config('app.date_format'), $orderDate)->startOfMonth()->format('d/m/Y');
                $endOfMonth = Carbon::createFromFormat(config('app.date_format'), $orderDate)->endOfMonth()->format('d/m/Y');
                $orderIndex = Order::whereShopId($shop->id)
                                    ->where('order_date', '<', $endOfMonth)
                                    ->where('order_date', '>', $startOfMonth)
                                    ->count()
                                    ;

                $orderNumber = $shop->prefix . '_' . date('ym') . sprintf("%03d", intval($orderIndex) + 1);

                $order['order_number'] = $orderNumber;

                $evd = [];
                if ($request->hasFile('evidence')) {
                    foreach($request->file('evidence') as $img)
                    {
                        $imgName = Date('YmdHis') . '_' . $img->getClientOriginalName();
                        $img->move(public_path(Order::ORDER_EVIDENCE_FOLDER), $imgName);
                        $evd[] = $imgName;
                    }
                }

                $order['evidence'] = json_encode($evd);

                $order = Order::create($order);

                $orderProductArr = explode('_', $request->get('order_products'));
                foreach ($orderProductArr as $orderProduct) {
                    list($prodId, $quantity, $costItem, $priceItem) = explode(',', $orderProduct);
                    OrderDetail::create([
                        'product_id' => $prodId,
                        'quantity' => $quantity,
                        'cost_item' => $costItem,
                        'price_item' => $priceItem,
                        'order_id' => $order->id,
                    ]);

                    $product = Product::find($prodId)->decrement('quantity', $quantity);

                }

            });

            return redirect()->route('admin.orders.index', ['stock' => $stock->id])->withFlashSuccess('Added an order!');

        } catch (\Exception $e) {
            return redirect()->back()->withFlashSuccess('Somethings went wrong!. Please content your admin.');
        }

    }

    public function updatePriority(Request $request, Order $order)
    {
        $order->update([
            'priority' => $request->get('priority')
        ]);

        return response()->json(['status' => 'success']);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $order->update([
            'status_id' => $request->get('status_id')
        ]);

        return response()->json(['status' => 'success']);
    }

    public function show(Request $request, Stock $stock, Order $order)
    {
        $order = Order::whereId($order->id)->with('order_detail')->with('order_detail.product')->first();
        return view('backend.order.view_order')
            ->withStock($stock)
            ->withOrder($order)
            ;
    }

    public function updateBoxSize(Request $request, Order $order)
    {
        $order->update([
            'box_size' => $request->get('box_size')
        ]);

        return response()->json(['status' => 'success']);
    }
    public function updateShippingUnit(Request $request, Order $order)
    {
        $order->update([
            'shipping_unit' => intval($request->get('shipping_unit'))
        ]);

        return response()->json(['status' => 'success']);
    }
}
