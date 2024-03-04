<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MakeStockSupplierSeeder extends Seeder
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

        Supplier::create([
            'name' => 'Đức Minh',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'Hương Loan',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'Châu Anh',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'Vườn Sứ',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'Pine Homes',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'Shoptonghop 6699',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'Hương Thể',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);

        Supplier::create([
            'name' => 'Other',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);
    }
}
