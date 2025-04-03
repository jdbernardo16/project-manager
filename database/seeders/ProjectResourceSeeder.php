<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProjectResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all projects and resources (specifically those with role 'resource')
        $projects = Project::all();
        $resources = Resource::whereHas('user', fn($q) => $q->where('role', 'resource'))->get();

        if ($projects->isEmpty() || $resources->isEmpty()) {
            $this->command->warn('No projects or resources found, skipping ProjectResource seeding.');
            return;
        }

        // Assign resources to projects - Example assignments
        $project1 = $projects->firstWhere('title', 'Website Redesign');
        $project2 = $projects->firstWhere('title', 'Mobile App Development');
        $project3 = $projects->firstWhere('title', 'Marketing Campaign Launch');
        $project4 = $projects->firstWhere('title', 'Internal Tool Upgrade');

        $resource1 = $resources->get(0); // First resource created by factory
        $resource2 = $resources->get(1); // Second resource
        $resource3 = $resources->get(2);
        $specificResource = Resource::whereHas('user', fn($q) => $q->where('email', 'dev1@example.com'))->first();

        // --- Assignments ---

        // Project 1: Website Redesign (Ongoing)
        if ($project1 && $resource1) {
            $project1->resources()->attach($resource1->id, [
                'assigned_hours' => 60,
                'start_date' => Carbon::now()->subWeek(),
                'end_date' => $project1->deadline, // Assign until project deadline
            ]);
             // Update resource status if not already assigned
            if($resource1->availability_status === 'available') {
                $resource1->update(['availability_status' => 'assigned']);
            }
        }
        if ($project1 && $specificResource) {
             $project1->resources()->attach($specificResource->id, [
                'assigned_hours' => 60,
                'start_date' => Carbon::now()->subWeek(),
                'end_date' => $project1->deadline,
            ]);
             if($specificResource->availability_status === 'available') {
                $specificResource->update(['availability_status' => 'assigned']);
            }
        }

        // Project 3: Marketing Campaign (Ongoing, Due Soon)
         if ($project3 && $resource2) {
            $project3->resources()->attach($resource2->id, [
                'assigned_hours' => 80, // Full assignment
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => $project3->deadline,
            ]);
             if($resource2->availability_status === 'available') {
                $resource2->update(['availability_status' => 'assigned']);
            }
        }

         // Project 4: Internal Tool Upgrade (Ongoing, Past Due)
         if ($project4 && $resource3) {
            $project4->resources()->attach($resource3->id, [
                'assigned_hours' => 60,
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => null, // Ongoing past deadline
            ]);
             if($resource3->availability_status === 'available') {
                $resource3->update(['availability_status' => 'assigned']);
            }
        }

        // Note: Project 2 (Upcoming) has no resources assigned yet.
        // Note: Some resources might remain 'available'.
    }
}
