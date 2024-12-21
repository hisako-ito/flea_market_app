<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            [
                'user_id' => 1,
                'item_name' => '腕時計',
                'brand' => 'Emporio Armani',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image' => 'storage/item_images/Armani+Mens+Clock.jpg',
                'condition' => 1,
            ],
            [
                'user_id' => 2,
                'item_name' => 'HDD',
                'brand' => 'BUFFALO',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'item_image' => 'storage/item_images/HDD+Hard+Disk.jpg',
                'condition' => 2,
            ],
            [
                'user_id' => 3,
                'item_name' => '玉ねぎ3束',
                'brand' => '北海道産',
                'price' => '300',
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image' => 'storage/item_images/iLoveIMG+d.jpg',
                'condition' => 3,
            ],
            [
                'user_id' => 1,
                'item_name' => '革靴',
                'brand' => 'COLE HAAN',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'item_image' => 'storage/item_images/Leather+Shoes+Product+Photo.jpg',
                'condition' => 4,
            ],
            [
                'user_id' => 2,
                'item_name' => 'ノートPC',
                'brand' => 'Apple',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'item_image' => 'storage/item_images/Living+Room+Laptop.jpg',
                'condition' => 1,
            ],
            [
                'user_id' => 3,
                'item_name' => 'マイク',
                'brand' => 'audio-technica',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'item_image' => 'storage/item_images/Music+Mic+4632231.jpg',
                'condition' => 2,
            ],
            [
                'user_id' => 1,
                'item_name' => 'ショルダーバッグ',
                'brand' => 'Vivienne Westwood',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'item_image' => 'storage/item_images/Purse+fashion+pocket.jpg',
                'condition' => 3,
            ],
            [
                'user_id' => 2,
                'item_name' => 'タンブラー',
                'brand' => 'THERMOS',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'item_image' => 'storage/item_images/Tumbler+souvenir.jpg',
                'condition' => 4,
            ],
            [
                'user_id' => 3,
                'item_name' => 'コーヒーミル',
                'brand' => 'Kalita',
                'price' => '4000',
                'item_image' => 'storage/item_images/Waitress+with+Coffee+Grinder.jpg',
                'description' => '手動のコーヒーミル',
                'condition' => 1,
            ],
            [
                'user_id' => 1,
                'item_name' => 'メイクセット',
                'brand' => 'M・A・C',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'item_image' => 'storage/item_images/外出メイクアップセット.jpg',
                'condition' => 2,
            ],
        ]);
    }
}
