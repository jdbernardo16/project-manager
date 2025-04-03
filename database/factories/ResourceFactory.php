<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User; // Import User model if needed for relationships

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' will typically be provided when calling the factory
            // 'user_id' => User::factory(), // Or create a new user if not provided
            'capacity' => $this->faker->randomFloat(1, 4, 8), // Random capacity between 4.0 and 8.0
            'availability_status' => $this->faker->randomElement(['available', 'unavailable']), // Default to available or unavailable
            'notes' => $this->faker->optional()->sentence(), // Optionally add some notes
        ];
    }
}
