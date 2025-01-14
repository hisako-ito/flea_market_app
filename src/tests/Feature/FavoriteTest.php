<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testFavoriteAdd()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create();

        $this->actingAs($user)
            ->post('/item/' . $favoriteItem->id . '/favorite')
            ->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $favoriteItem->id,
        ]);
    }

    public function testFavoriteIconChangesWhenAdded()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create();

        $user->favorites()->attach($favoriteItem->id);

        $response = $this->actingAs($user)
            ->get('/item/' . $favoriteItem->id);

        $decodedContent = html_entity_decode($response->getContent());

        $this->assertStringContainsString('class="fas fa-star filled"', $decodedContent);
    }

    public function testFavoriteRemoved()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson(route('favorite', $favoriteItem->id));
        $response->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $favoriteItem->id,
        ]);

        $response = $this->postJson(route('favorite', $favoriteItem->id));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $favoriteItem->id,
        ]);
    }
}
