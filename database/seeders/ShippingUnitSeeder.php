<?php

namespace Database\Seeders;

use App\Models\ShippingUnit;
use Illuminate\Database\Seeder;

class ShippingUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShippingUnit::create([
            'name' => 'Giao Hàng Nhanh',
            'acronym' => 'GHN'
        ]);

        ShippingUnit::create([
            'name' => 'Viettel Post',
            'acronym' => 'VTP'
        ]);

        ShippingUnit::create([
            'name' => 'BEST Express',
            'acronym' => 'BEST'
        ]);

        ShippingUnit::create([
            'name' => 'Giao Hàng Tiết Kiệm',
            'acronym' => 'GHTK'
        ]);

        ShippingUnit::create([
            'name' => 'Others',
            'acronym' => 'OTHERS'
        ]);
    }
}
