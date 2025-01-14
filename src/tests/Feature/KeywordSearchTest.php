<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class KeywordSearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testKeywordSearchInRecommendPage()
    {
        $item = Item::factory()->create(['item_name' => 'テスト商品']);
        $keyword = 'テスト';
        $response =
            $this->get('/?page=recommend&keyword=' . $keyword);

        $response->assertStatus(200);

        $response->assertSee($item->item_name);
    }

    public function testKeywordSearchInMylistPage()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $favoriteItem = Item::factory()->create(['item_name' => 'テスト商品']);

        $keyword = 'テスト';

        $user->favorites()->attach($favoriteItem->id);

        $this->actingAs($user);

        $response =
            $this->get('/?page=mylist&keyword=' . $keyword);

        $response->assertStatus(200);

        $response->assertSee($favoriteItem->item_name);
    }
}
