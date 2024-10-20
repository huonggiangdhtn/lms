<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Thêm dòng này
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('setting_details')->insert([
            [
                'company_name' => "Tên công ty",
                'web_title' => "Tên công ty",
                'phone' => '0500363732',
                'address' => 'Ywang Buôn Ma Thuột, Đăk Lăk',
            ],
        ]);

        DB::table('users')->insert([
            [
                'full_name' => "admin",
                "username" => "admin",
                "email" => "admin@gmail.com",
                "password" => Hash::make('12345678'),
                "role" => "admin",
                "phone" => "111111111",
                'status' => 'active',
            ],
            // Các bản ghi khác...
        ]);

        DB::table('roles')->insert([
            [
                'alias' => 'admin',
                'title' => "Quản trị viên",
                'status' => 'active',
            ],
            // Các bản ghi khác...
        ]);
    }
}
