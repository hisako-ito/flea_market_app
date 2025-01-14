<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_name' => $this->faker->word(),
            'brand' => $this->faker->word(),
            'price' => $this->faker->numberBetween(1, 10000),
            'description' => $this->faker->text(),
            'item_image' => 'storage/item_images/' . $this->faker->unique()->word() . '.jpg',
            'condition' => $this->faker->numberBetween(1, 4),
            'is_sold' => $this->faker->boolean()
        ];
    }
}
