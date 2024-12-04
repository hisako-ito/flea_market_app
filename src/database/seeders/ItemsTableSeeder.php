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
                'name' => '腕時計',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'storage/images/Armani+Mens+Clock.jpg',
                'condition' => 1,
            ],
            [
                'name' => 'HDD',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'storage/images/HDD+Hard+Disk.jpg',
                'condition' => 2,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => '300',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'storage/images/iLoveIMG+d.jpg',
                'condition' => 3,
            ],
            [
                'name' => '革靴',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'storage/images/Leather+Shoes+Product+Photo.jpg',
                'condition' => 4,
            ],
            [
                'name' => 'ノートPC',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'image' => 'storage/images/Living+Room+Laptop.jpg',
                'condition' => 1,
            ],
            [
                'name' => 'マイク',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'storage/images/Music+Mic+4632231.jpg',
                'condition' => 2,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'storage/images/Purse+fashion+pocket.jpg',
                'condition' => 3,
            ],
            [
                'name' => 'タンブラー',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'image' => 'storage/images/Tumbler+souvenir.jpg',
                'condition' => 4,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => '4000',
                'image' => 'storage/images/Waitress+with+Coffee+Grinder.jpg',
                'description' => '手動のコーヒーミル',
                'condition' => 1,
            ],
            [
                'name' => 'メイクセット',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'image' => 'storage/images/外出メイクアップセット.jpg',
                'condition' => 2,
            ],
        ]);
    }
}
