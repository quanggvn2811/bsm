<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DefaultCategoryAndSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stockName = 'MAKE STOCK';
        $makeStock = Stock::firstOrCreate([
            'name' => $stockName,
            'unique_prefix' => 'MAKE_STOCK',
        ]);

        Category::create([
            'name' => 'DEFAULT_CATEGORY',
            'status' => 1,
            'sku' => 'DEFAULT_CATEGORY',
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'DEFAULT_SUPPLIER',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);
    }
}
