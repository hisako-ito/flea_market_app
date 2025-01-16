<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testSell()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $categories = Category::factory()->count(2)->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('Armani+Mens+Clock.png');

        $response = $this->actingAs($user)
            ->post(
                '/sell',
                [
                    'user_id' => $user->id,
                    'item_name' => '腕時計',
                    'brand' => 'Emporio Armani',
                    'price' => 15000,
                    'description' => 'スタイリッシュなデザインのメンズ腕時計',
                    'item_image' => $file,
                    'condition' => 1,
                    'categories' => $categories->pluck('id')->toArray(),
                ]
            );

        $response->assertStatus(302);

        Storage::disk('public')->assertExists('item_images/' . $file->hashName());

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'item_name' => '腕時計',
            'brand' => 'Emporio Armani',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'item_image' => 'storage/item_images/' . $file->hashName(),
            'condition' => 1,
        ]);

        $item = Item::where('item_name', '腕時計')->first();

        $this->assertNotNull($item);
        $this->assertCount(2, $item->categories);

        foreach ($categories as $category) {
            $this->assertTrue($item->categories->contains($category));
        }
    }
}
