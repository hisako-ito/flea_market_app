<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testItemDetailShow()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $item = Item::factory()->create([
            'item_name' => '腕時計',
            'brand' => 'Emporio Armani',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'item_image' => 'storage/item_images/Armani+Mens+Clock.jpg',
            'condition' => 1,
            'is_sold' => true,
        ]);

        $this->actingAs($user);

        $user->favorites()->attach($item->id);
        $favoriteCount = $user->favorites()->count();

        $comment = $user->comments()->create([
            'item_id' => $item->id,
            'content' => 'かっこいい時計です。'
        ]);
        $commentCount = $user->comments()->count();
        $commentUser = $comment->user->user_name;
        $commentContent = $comment->content;

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);

        $decodedContent = html_entity_decode($response->getContent());
        $this->assertStringContainsString('src="http://localhost/storage/item_images/Armani+Mens+Clock.jpg"', $decodedContent);

        $response->assertSee('腕時計');
        $response->assertSee('Emporio Armani');
        $response->assertSee('15,000');
        $response->assertSee('スタイリッシュなデザインのメンズ腕時計');
        $response->assertSee('良好');
        $response->assertSee('sold');
        $response->assertSee($favoriteCount);
        $response->assertSee($commentUser);
        $response->assertSee($commentCount);
        $response->assertSee($commentContent);
    }

    public function testItemDetailCategoryShow()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $this->actingAs($user);

        $item = Item::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $item->categories()->attach($categories->pluck('id')->toArray());

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
