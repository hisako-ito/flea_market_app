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

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('cache:clear');
        $this->artisan('config:clear');
        $this->artisan('route:clear');
        $this->artisan('view:clear');
    }

    public function testFavoriteItemDisplayedInMyList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItems = Item::factory()->count(3)->create();
        // $nonFavoriteItems = Item::factory()->count(3)->create();

        $user->favorites()->attach($favoriteItems->pluck('id')->toArray());


        foreach ($favoriteItems as $item) {
            $this->assertDatabaseHas('favorites', [
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
        }

        $this->actingAs($user);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        foreach ($favoriteItems as $item) {
            $response->assertSee($item->item_name);
        }
        // foreach ($nonFavoriteItems as $item) {
        //     $response->assertDontSee($item->item_name);
        // }
    }

    public function testSoldLabelIsDisplayedInMyList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $item = Item::factory()->create(['is_sold' => true]);

        $user->favorites()->attach($item->pluck('id')->toArray());

        $this->actingAs($user);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee('sold');
    }


    public function testOwnItemsAreNotDisplayedInMyList()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id]);

        $user->favorites()->attach($favoriteItem->pluck('id'));

        $this->actingAs($user);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee($favoriteItem->item_name);

        $response->assertDontSee($ownItem->item_name);
    }
}
