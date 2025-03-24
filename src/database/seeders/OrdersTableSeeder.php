<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orders')->insert([
            [
                'buyer_id' => 2,
                'item_id' => 1,
                'stripe_session_id' => 'cs_test_' . Str::random(24),
                'price' => 15000,
                'payment_method' => 2,
                'shipping_address' => '222-2222東京都渋谷区渋谷ビル',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_id' => 3,
                'item_id' => 2,
                'stripe_session_id' => 'cs_test_' . Str::random(24),
                'price' => 5000,
                'payment_method' => 2,
                'shipping_address' => '333-3333東京都港区港ビル',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_id' => 2,
                'item_id' => 3,
                'stripe_session_id' => 'cs_test_' . Str::random(24),
                'price' => 300,
                'payment_method' => 2,
                'shipping_address' => '222-2222東京都渋谷区渋谷ビル',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_id' => 1,
                'item_id' => 6,
                'stripe_session_id' => 'cs_test_' . Str::random(24),
                'price' => 8000,
                'payment_method' => 2,
                'shipping_address' => '111-1111東京都新宿区新宿ビル',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_id' => 3,
                'item_id' => 7,
                'stripe_session_id' => 'cs_test_' . Str::random(24),
                'price' => 3500,
                'payment_method' => 2,
                'shipping_address' => '333-3333東京都港区港ビル',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_id' => 1,
                'item_id' => 8,
                'stripe_session_id' => 'cs_test_' . Str::random(24),
                'price' => 500,
                'payment_method' => 2,
                'shipping_address' => '222-2222東京都渋谷区渋谷ビル',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
