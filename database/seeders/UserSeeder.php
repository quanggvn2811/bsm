<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('gHarjHdmaPmQX8UOnVc=quanggvn123'),
        ]);

        User::create([
            'name' => 'staff',
            'email' => 'staff@linhgv.com',
            'password' => bcrypt('linhgvgHarjHdmaPmQX8UOnVc='),
        ]);
    }
}
