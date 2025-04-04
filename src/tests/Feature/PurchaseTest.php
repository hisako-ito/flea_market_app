<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Stripe\Checkout\Session;
use Mockery;

class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(Session::class, function ($mock) {
            $mock->shouldReceive('create')
                ->andReturn((object)[
                    'id' => 'cs_test_1234',
                    'payment_status' => 'paid',
                ]);

            $mock->shouldReceive('retrieve')
                ->andReturn((object)[
                    'payment_status' => 'paid',
                ]);
        });

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function testPurchase()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(
                '/purchase/' . $item->id,
                [
                    'payment_method' => 'カード払い',
                    'shipping_address' => '東京都新宿区',
                ]
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function testPurchaseSoldDisplayedInItemList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(
                '/purchase/' . $item->id,
                [
                    'payment_method' => 'カード払い',
                    'shipping_address' => '東京都新宿区',
                ]
            );

        $response->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->update(['is_sold' => true]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    public function testPurchaseItemDisplayedInMyPage()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $item = Item::factory()->create(
            ['item_name' => 'テスト商品']
        );

        $response = $this->actingAs($user)
            ->post(
                '/purchase/' . $item->id,
                [
                    'payment_method' => 'カード払い',
                    'shipping_address' => '東京都新宿区',
                ]
            );

        $response->assertStatus(302);

        session(['stripe_session_id' => 'cs_test_1234']);

        $response = $this->actingAs($user)
            ->get('/stripe/waiting-for-payment?session_id=cs_test_1234');


        $response->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);

        $response = $this->get('/mypage?tab=buy');
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }
}
