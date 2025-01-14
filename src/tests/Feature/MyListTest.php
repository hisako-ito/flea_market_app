<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class MyListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testFavoriteItemDisplayedInMyList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create();

        $user->favorites()->attach($favoriteItem->id);

        $this->actingAs($user);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee($favoriteItem->item_name);
    }

    public function testSoldLabelIsDisplayedInMyList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $soldItem = Item::factory()->create(['is_sold' => true]);

        $user->favorites()->attach($soldItem->id);

        $this->actingAs($user);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee('sold');
    }


    public function testOwnItemsAreNotDisplayedInMyList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create(['is_sold' => false]);
        $ownItem = Item::factory()->create(['user_id' => $user->id]);

        $user->favorites()->attach($favoriteItem->id);

        $this->actingAs($user);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee($favoriteItem->item_name);

        $response->assertDontSee($ownItem->item_name);
    }

    public function testGuestFavoriteItemNotDisplayedInMyList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create();

        $user->favorites()->attach($favoriteItem->id);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertDontSee($favoriteItem->item_name);
    }
}
