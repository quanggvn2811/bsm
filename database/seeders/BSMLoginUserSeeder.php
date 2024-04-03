<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BSMLoginUserSeeder extends Seeder
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
            'email' => 'admin@bsm.com',
            'password' => bcrypt('quanggvn@bsm#'),
        ]);

        User::create([
            'name' => 'LinhGV',
            'email' => 'linhgv@bsm.com',
            'password' => bcrypt('linhgv@bsm#'),
        ]);
    }
}
