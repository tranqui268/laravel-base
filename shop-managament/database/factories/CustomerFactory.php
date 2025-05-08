<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(), 
            'email' => fake()->unique()->safeEmail(), 
            'tel_num' => fake()->regexify('0[3|5|7|8|9][0-9]{8}'),
            'address' => fake()->address(), 
            'is_active' => fake()->boolean(90), 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
