<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            [
                'item_id' => 1,
                'seller_id' => 1,
                'buyer_id' => 2,
            ],
            [
                'item_id' => 2,
                'seller_id' => 1,
                'buyer_id' => 3,
            ],
            [
                'item_id' => 3,
                'seller_id' => 1,
                'buyer_id' => 2,
            ],
            [
                'item_id' => 6,
                'seller_id' => 2,
                'buyer_id' => 1,
            ],
            [
                'item_id' => 7,
                'seller_id' => 2,
                'buyer_id' => 3,
            ],
            [
                'item_id' => 8,
                'seller_id' => 2,
                'buyer_id' => 1,
            ],
        ]);
    }
}
