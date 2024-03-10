<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class AddMasterDecorSupplier extends Seeder
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
            'name' => 'Master Decor',
            'status' => 1,
            'stock_id' => $makeStock->id ?? 1,
        ]);
    }
}
