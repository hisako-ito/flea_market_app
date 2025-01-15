<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Stripe\Checkout\Session;

class ShippingAddressTest extends TestCase
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

    public function testChangedShippingAddress()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->patch(
                '/purchase/address/' . $item->id,
                [
                    'user_name' => $user->user_name,
                    'postal_code' => '111-1111',
                    'address' => '東京都新宿区',
                    'building' => '新宿ビル',
                ]
            );

        $response->assertStatus(302);

        $response = $this->actingAs($user)
            ->get('/purchase/' . $item->id,);

        $response->assertStatus(200);

        $response->assertSee('111-1111');
        $response->assertSee('東京都新宿区');
        $response->assertSee('新宿ビル');
    }

    // public function testItemChangedShippingAddress()
    // {
    //     $user = User::factory()->create();
    //     /** @var \App\Models\User $user */
    //     $item = Item::factory()->create();

    //     $this->actingAs($user)
    //         ->patch('/purchase/address/' . $item->id, [
    //             'user_name' => $user->user_name,
    //             'postal_code' => '111-1111',
    //             'address' => '東京都新宿区',
    //             'building' => '新宿ビル',
    //         ])
    //         ->assertStatus(302);

    //     $this->assertDatabaseHas('users', [
    //         'id' => $user->id,
    //         'postal_code' => '111-1111',
    //         'address' => '東京都新宿区',
    //         'building' => '新宿ビル',
    //     ]);

    //     session(['stripe_session_id' => 'cs_test_1234']);

    //     $this->actingAs($user)
    //         ->post('/purchase/' . $item->id, ['payment_method' => 'カード払い'])
    //         ->assertStatus(302);

    //     $this->assertDatabaseHas('orders', [
    //         'user_id' => $user->id,
    //         'item_id' => $item->id,
    //         'shipping_address' => '111-1111東京都新宿区新宿ビル',
    //         'payment_method' => 2,
    //         'status' => 'pending',
    //     ]);
    // }
}
