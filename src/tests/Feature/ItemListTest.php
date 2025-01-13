<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testAllItemsDisplayedInItemList()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        Item::factory()->count(10)->create();

        $response = $this->get('/');
        $response->assertStatus(200);

        $response = $this->get('/no_route');
        $response->assertStatus(404);

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function testSoldLabelIsDisplayedInItemList()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        Item::factory()->create(['is_sold' => true]);

        $response = $this->get('/');

        $response->assertSee('sold');
    }

    public function testOwnItemsAreNotDisplayedInItemList()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        Item::factory()->create(['user_id' => $user->id]);

        $otherItem = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertDontSee($user->id);

        $response->assertSee($otherItem->id);
    }
}
