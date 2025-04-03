<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['upcoming', 'ongoing', 'completed']);
        $deadline = null;
        if ($status !== 'completed') {
            $deadline = Carbon::instance($this->faker->dateTimeBetween('now', '+6 months'));
        } else {
             $deadline = Carbon::instance($this->faker->dateTimeBetween('-3 months', '-1 week'));
        }


        return [
            'title' => $this->faker->bs(), // Generate business-like project titles
            'description' => $this->faker->paragraph(),
            'estimate_hours' => $this->faker->numberBetween(20, 300),
            'deadline' => $deadline->toDateString(),
            'status' => $status,
        ];
    }
}
