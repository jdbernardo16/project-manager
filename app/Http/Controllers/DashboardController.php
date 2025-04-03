<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Resource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Date; // For date comparisons

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Fetch projects (e.g., ongoing and upcoming)
        $ongoingProjects = Project::where('status', 'ongoing')
                                ->with('resources.user') // Eager load for display
                                ->latest('deadline') // Show nearest deadline first
                                ->limit(5) // Limit for dashboard view
                                ->get();

        $upcomingProjects = Project::where('status', 'upcoming')
                                ->with('resources.user')
                                ->oldest('deadline') // Show furthest deadline first? Or nearest start? Let's use deadline for now.
                                ->limit(5)
                                ->get();

        // Fetch resources with availability
        $resources = Resource::with('user', 'projects') // Eager load user and projects
                            ->orderBy('availability_status') // Group by status visually
                            ->get();

        // Calculate basic statistics
        $totalProjects = Project::count();
        $availableResourcesCount = Resource::where('availability_status', 'available')->count();
        $projectsDueSoonCount = Project::where('status', '!=', 'completed')
                                        ->whereNotNull('deadline')
                                        ->where('deadline', '<=', now()->addDays(7)->toDateString()) // Due within 7 days
                                        ->count();

        return Inertia::render('Dashboard', [
            'statistics' => [
                'totalProjects' => $totalProjects,
                'availableResources' => $availableResourcesCount,
                'projectsDueSoon' => $projectsDueSoonCount,
            ],
            'ongoingProjects' => $ongoingProjects->map(function ($project) {
                 // Basic progress calculation (example)
                $progress = 50; // Assuming ongoing is 50% for simplicity here
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'deadline' => $project->deadline ? Date::parse($project->deadline)->format('M d, Y') : 'N/A',
                    'resources_count' => $project->resources->count(),
                    'progress' => $progress,
                    'assigned_resources' => $project->resources->map(fn($r) => $r->user->name)->implode(', '),
                ];
            }),
             'upcomingProjects' => $upcomingProjects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'deadline' => $project->deadline ? Date::parse($project->deadline)->format('M d, Y') : 'N/A',
                    'resources_count' => $project->resources->count(),
                     'assigned_resources' => $project->resources->map(fn($r) => $r->user->name)->implode(', '),
                ];
            }),
            'resources' => $resources->map(function ($resource) {
                 // Find the current or next project assignment (simplified)
                $currentProject = $resource->projects()
                                        ->wherePivot('start_date', '<=', now()->toDateString())
                                        ->where(function($q) {
                                            $q->wherePivot('end_date', '>=', now()->toDateString())
                                              ->orWhereNull('pivot_end_date'); // Corrected pivot column name
                                        })
                                        ->orderBy('pivot_start_date') // Corrected pivot column name
                                        ->first();

                return [
                    'id' => $resource->id,
                    'name' => $resource->user->name,
                    'capacity' => $resource->capacity,
                    'availability_status' => $resource->availability_status,
                    'current_project_title' => $currentProject ? $currentProject->title : 'None',
                    'user_id' => $resource->user_id, // Needed for linking or actions
                ];
            }),
        ]);
    }
}
