<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'user_name' => '山田太郎',
                'email' => 'taro@example.com',
                'user_image' => 'storage/user_images/dog.jpg',
                'postal_code' => '111-1111',
                'address' => '東京都新宿区',
                'building' => '新宿ビル',
                'password' => Hash::make('password123'),
            ],
            [
                'user_name' => '山田花子',
                'email' => 'hanako@example.com',
                'user_image' => 'storage/user_images/cat.jp',
                'postal_code' => '222-2222',
                'address' => '東京都渋谷区',
                'building' => '渋谷ビル',
                'password' => Hash::make('password123'),
            ],
            [
                'user_name' => '山田一郎',
                'email' => 'ichiro@example.com',
                'user_image' => 'storage/user_images/turtle.jpg',
                'postal_code' => '333-3333',
                'address' => '東京都港区',
                'building' => '港ビル',
                'password' => Hash::make('password123'),
            ],
        ]);
    }
}
