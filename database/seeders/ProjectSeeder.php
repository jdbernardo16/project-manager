<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon; // For date manipulation

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory()->create([
            'title' => 'Website Redesign',
            'description' => 'Complete overhaul of the main company website.',
            'estimate_hours' => 120,
            'deadline' => Carbon::now()->addWeeks(6),
            'status' => 'ongoing',
        ]);

        Project::factory()->create([
            'title' => 'Mobile App Development',
            'description' => 'Create a new mobile application for iOS and Android.',
            'estimate_hours' => 250,
            'deadline' => Carbon::now()->addMonths(3),
            'status' => 'upcoming',
        ]);

        Project::factory()->create([
            'title' => 'Marketing Campaign Launch',
            'description' => 'Plan and execute the Q3 marketing campaign.',
            'estimate_hours' => 80,
            'deadline' => Carbon::now()->addWeeks(3), // Due soon
            'status' => 'ongoing',
        ]);

         Project::factory()->create([
            'title' => 'Internal Tool Upgrade',
            'description' => 'Upgrade the internal CRM system.',
            'estimate_hours' => 60,
            'deadline' => Carbon::now()->subWeek(), // Past due
            'status' => 'ongoing',
        ]);

        Project::factory()->create([
            'title' => 'Client Portal Setup',
            'description' => 'Initial setup and configuration for a new client portal.',
            'estimate_hours' => 40,
            'deadline' => Carbon::now()->addMonth(),
            'status' => 'upcoming',
        ]);

         Project::factory()->create([
            'title' => 'Documentation Update',
            'description' => 'Update all user-facing documentation.',
            'estimate_hours' => 30,
            'deadline' => Carbon::now()->subMonth(), // Completed some time ago
            'status' => 'completed',
        ]);
    }
}
