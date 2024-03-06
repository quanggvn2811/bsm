<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Supplier;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {

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
        DB::transaction(function ($request) {
            $data = $request->all();

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
                'order_date',
                'priority',
                'status_id',
                'total',
                'ship_by_customer',
                'ship_by_shop',
                'cost',
                'notes',
            ]);

            $order['order_address'] = $request->get('customer_address');

            $order['customer_id'] = $customer->id;

            $shop = Shop::find($request->get('shop_id'));

            $order['shop_id'] = $shop->id;

            $todayOrderIndex = Order::whereOrderDate(date('d/m/Y'))->count();

            $orderNumber = $shop->prefix . '_' . date('ymd') . sprintf("%03d", intval($todayOrderIndex) + 1);

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

            // Todo Add order detail
        });

    }
}
