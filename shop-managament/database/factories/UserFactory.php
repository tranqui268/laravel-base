<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(), 
            'email' => fake()->unique()->safeEmail(), 
            'verify_email' => fake()->optional()->dateTimeBetween('-1 year', 'now'), 
            'password' => '$2y$12$Ns3m4MUuM01IMaeTKNSICee0Bvojod/.0/J/n1iLnkMQopIMb3xiu', 
            'is_active' => fake()->boolean(90), 
            'is_delete' => fake()->boolean(10), 
            'group_role' => fake()->randomElement(['admin', 'editor', 'user', 'moderator']), 
            'last_login_at' => fake()->optional()->dateTimeBetween('-6 months', 'now'), 
            'last_login_ip' => fake()->optional()->ipv4(), 
            'remember_token' => Str::random(10), 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
