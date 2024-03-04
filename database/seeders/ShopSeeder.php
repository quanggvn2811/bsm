<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shop::create([
            'name' => 'Nhà Xinh 365',
            'prefix' => 'NX365'
        ]);

        Shop::create([
            'name' => 'Gia Dụng Đẹp',
            'prefix' => 'GDD'
        ]);

        Shop::create([
            'name' => 'Maker Decor & Smart',
            'prefix' => 'MDS'
        ]);

        Shop::create([
            'name' => 'Maker Home Decor',
            'prefix' => 'MHD'
        ]);

        Shop::create([
            'name' => 'Homelux Decor',
            'prefix' => 'HLUX'
        ]);

        Shop::create([
            'name' => 'Giá Tốt Mua Ngay',
            'prefix' => 'GTMN'
        ]);

        Shop::create([
            'name' => 'Tiktok GDQA',
            'prefix' => 'TikGDQA'
        ]);
    }
}
