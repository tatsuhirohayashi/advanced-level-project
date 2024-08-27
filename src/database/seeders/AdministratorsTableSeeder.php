<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdministratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrators = [
            [
                'id' => 1,
                'name' => 'admin1',
                'email' => 'admin@admin1.com',
                'password' => Hash::make('admin1'),
            ],
            [
                'id' => 2,
                'name' => 'admin2',
                'email' => 'admin@admin2.com',
                'password' => Hash::make('admin2'),
            ],
        ];

        DB::table('administrators')->insert($administrators);
    }
}
