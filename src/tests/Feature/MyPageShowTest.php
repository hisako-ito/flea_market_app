<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;

class MyPageShowTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testMyPageShow()
    {
        $user = User::factory()->create([
            'user_name' => '山田太郎',
            'user_image' => 'storage/user_images/dog.jpg',
        ]);
        /** @var \App\Models\User $user */

        $item1 = Item::factory()->create([
            'item_name' => '腕時計',
            'user_id' => $user->id,
            'item_image' => 'storage/item_images/Armani+Mens+Clock.jpg',
        ]);

        $item2 = Item::factory()->create([
            'item_name' => 'HDD',
            'brand' => 'BUFFALO',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'item_image' => 'storage/item_images/HDD+Hard+Disk.jpg',
            'condition' => 2,
        ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
            'stripe_session_id' => 'cs_test_1234',
            'price' => 1000,
            'payment_method' => 2,
            'shipping_address' => '111-1111東京都新宿区新宿ビル',
            'status' => 'paid',
        ]);

        $this->actingAs($user);

        // マイページ表示の確認
        $response = $this->get('/mypage');
        $response->assertStatus(200);

        $response->assertSee('<img src="' . urlencode(asset($user->user_image)) . '"', false);
        $response->assertSee('山田太郎');

        // 出品タブの確認
        $response = $this->get('/mypage?tab=sell');
        $response->assertStatus(200);

        $response->assertSee('src="http://localhost/storage/item_images/Armani+Mens+Clock.jpg"', false);
        $response->assertSee($item1->item_name);

        // 購入タブの確認
        $response = $this->get('/mypage?tab=buy');
        $response->assertStatus(200);

        $response->assertSee('src="http://localhost/storage/item_images/HDD+Hard+Disk.jpg"', false);
        $response->assertSee($item2->item_name);
    }
}
