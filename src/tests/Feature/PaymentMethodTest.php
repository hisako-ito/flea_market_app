<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class PaymentMethodTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testPaymentMethodSelect()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->get('/purchase/' . $item->id);

        $response->assertSee('カード払い');
    }
}
