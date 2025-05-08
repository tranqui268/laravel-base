<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            $productId = 'P' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        return [
            'product_id' => $productId,
            'product_name' => fake()->words(3, true),
            'product_price' => fake()->randomFloat(2, 10000, 1000000),
            'description' => fake()->sentence(),
            'is_sales' => fake()->boolean(80), 
            'product_image' => 'https://res.cloudinary.com/dhis8yzem/image/upload/v1746676264/image_1024_wjel2b.png',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
    }
}
