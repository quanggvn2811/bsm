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
        $from = $request->get('order_date_from', today()->subDays(5)->format('d/m/Y'));
        $to = $request->get('order_date_to', today()->format('d/m/Y'));

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

        $productName = $request->get('product_name');
        if ($productName) {
            $orders = $orders->whereHas('order_detail.product', function ($query) use ($productName) {
                $query->where('products.name', 'like', '%' . $productName . '%');
            });
        }

        $orders = $orders->with('customer');

        $orders = $orders->orderBy('orders.created_at', 'ASC')->paginate(config('app.page_count'));

        $isAdmin = 'admin@admin.com' === auth()->user()->email;

        return view('backend.order.index')
            ->withStock($stock)
            ->withOrders($orders)
            ->withShippingUnits(ShippingUnit::all())
            ->withShops(Shop::all())
            ->withIsAdmin($isAdmin)
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
        if (!$request->get('order_products')) {
            return redirect()->back()->withFlashDanger('Add a product item pls.');
        }
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

    public function edit(Request $request, Stock $stock, Order $order)
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

        return view('backend.order.edit_order')
            ->withStock($stock)
            ->withOrder($order)
            ->withSuppliers($suppliers)
            ->withCategories($categories)
            ->withShops($shops)
            ->withProducts($products)
            ->withProductById($productById)
            ;
    }

    public function update(Request $request, Stock $stock, Order $order)
    {
        if (!$request->get('order_products')) {
            return redirect()->back()->withFlashDanger('Add a product item pls.');
        }

        try {
            $data = $request->all();

            DB::transaction(function () use ($request, $data, $stock, $order) {
                // Customer
                $customerData = [
                    'name' => $data['customer_name'],
                    'address' => $data['customer_address'],
                    'info_url' => $data['customer_url'],
                    'more_info' => $data['customer_more_info'],
                ];

                $customer = Customer::wherePhone($data['customer_phone'])->first();

                if ($customer) {
                    $customer->update($customerData);
                }

                // Store Order
                $orderData = $request->only([
                    'order_date',
                    'priority',
                    'status_id',
                    'total',
                    'ship_by_customer',
                    'ship_by_shop',
                    'cost',
                    'notes',
                ]);

                $orderData['ship_by_customer'] = $orderData['ship_by_customer'] ?? 0;
                $orderData['ship_by_shop'] = $orderData['ship_by_shop'] ?? 0;
                $orderData['cost'] = $orderData['cost'] ?? 0;
                $orderData['total'] = $orderData['total'] ?? 0;

                $orderData['order_address'] = $data['customer_address'];

                $shop = Shop::find($request->get('shop_id'));

                $orderData['shop_id'] = $shop->id;

                if ($request->hasFile('evidence')) {
                    $evd = [];
                    foreach ($request->file('evidence') as $img) {
                        $imgName = Date('YmdHis') . '_' . $img->getClientOriginalName();
                        $img->move(public_path(Order::ORDER_EVIDENCE_FOLDER), $imgName);
                        $evd[] = $imgName;
                    }

                    $orderData['evidence'] = json_encode($evd);
                }

                $order->update($orderData);

                if ($request->get('order_products')) {
                    $oldOderDetail = OrderDetail::whereOrderId($order->id);
                    foreach ($oldOderDetail->get() as $oldDetail) {
                        Product::find($oldDetail->product_id)->increment('quantity', $oldDetail->quantity);
                    }

                    $oldOderDetail->delete();

                    // Add new
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

                        Product::find($prodId)->decrement('quantity', $quantity);
                    }
                }

            });

            return redirect()->route('admin.orders.index', ['stock' => $stock->id])->withFlashSuccess('Updated order: ' . $order->order_number);


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

    public function destroy(Request $request, Stock $stock, Order $order)
    {
        $oderDetail = OrderDetail::whereOrderId($order->id);
        foreach ($oderDetail->get() as $detail) {
            Product::find($detail->product_id)->increment('quantity', $detail->quantity);
        }

        $oderDetail->delete();

        if (!$order->delete()) {
            return redirect()->back()->withFlashDanger('Something went wrong!, Pls contact your administrator.');
        }

        return redirect()->route('admin.orders.index', ['stock' => $stock->id]);
    }
}
