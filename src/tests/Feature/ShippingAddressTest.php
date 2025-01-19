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
}
