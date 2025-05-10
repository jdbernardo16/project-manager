<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Resource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Date; // For date comparisons
use Carbon\Carbon; // For date manipulation

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
                                ->limit(20) // Limit for dashboard view
                                ->get();

        $upcomingProjects = Project::where('status', 'upcoming')
                                ->with('resources.user')
                                ->oldest('deadline') // Show furthest deadline first? Or nearest start? Let's use deadline for now.
                                ->limit(20)
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

       // Fetch all projects for the calendar view
       $allProjects = Project::whereNotNull('deadline') // Only include projects with a deadline
                                ->with('resources') // Eager load resources to find the earliest start date
                                ->get();

       $allProjectsCalendarData = $allProjects->map(function ($project) {
           // Find the earliest start date from resource assignments
           $earliestStartDate = $project->resources
                                        ->pluck('pivot.start_date')
                                        ->filter() // Remove null values
                                        ->sort() // Sort dates
                                        ->first(); // Get the earliest date

           $startDate = $earliestStartDate ? Carbon::parse($earliestStartDate)->toDateString() : null;
           $endDate = $project->deadline ? $project->deadline->toDateString() : null;

           return [
               'id' => $project->id,
               'title' => $project->title,
               'start' => $startDate,
               'end' => $endDate,
               // Add other relevant project data if needed for the calendar view
           ];
       });

       return Inertia::render('Dashboard', [
           'statistics' => [
               'totalProjects' => $totalProjects,
               'availableResources' => $availableResourcesCount,
               'projectsDueSoon' => $projectsDueSoonCount,
           ],
           'allProjectsCalendarData' => $allProjectsCalendarData, // Pass calendar data
           'ongoingProjects' => $ongoingProjects->map(function ($project) {
                // Find the earliest start date from resource assignments
               $earliestStartDate = $project->resources
                                        ->pluck('pivot.start_date')
                                        ->filter() // Remove null values
                                        ->sort() // Sort dates
                                        ->first(); // Get the earliest date

               $startDate = $earliestStartDate ? Carbon::parse($earliestStartDate)->format('M d, Y') : 'N/A';

                 // Use the project's progress accessor like ProjectController does
                $progress = $project->progress;
                $isOverdue = $project->is_overdue;
                
                // Check deadline status flags
                $deadlineNear = false;
                $deadlineApproaching = false;
                if ($project->deadline && $project->status !== 'completed') {
                    $deadlineDate = $project->deadline;
                    
                    if ($deadlineDate->isPast()) {
                        $deadlineNear = true;
                    } else {
                        $daysUntilDeadline = $deadlineDate->diffInDays(now());
                        // Near if within 3 days (urgent)
                        $deadlineNear = $daysUntilDeadline <= 3;
                        // Approaching if between 4-7 days (warning)
                        $deadlineApproaching = $daysUntilDeadline > 3 && $daysUntilDeadline <= 7;
                    }
                }
                
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'start_date' => $startDate,
                    'deadline' => $project->deadline ? Date::parse($project->deadline)->format('M d, Y') : 'N/A',
                    'resources_count' => $project->resources->count(),
                    'progress' => $progress,
                    'is_overdue' => $isOverdue,
                    'deadline_near' => $deadlineNear,
                    'deadline_approaching' => $deadlineApproaching,
                    'assigned_resources' => $project->resources->map(fn($r) => $r->user->name)->implode(', '),
                ];
            }),
             'upcomingProjects' => $upcomingProjects->map(function ($project) {
                // Find the earliest start date from resource assignments
               $earliestStartDate = $project->resources
                                        ->pluck('pivot.start_date')
                                        ->filter() // Remove null values
                                        ->sort() // Sort dates
                                        ->first(); // Get the earliest date

               $startDate = $earliestStartDate ? Carbon::parse($earliestStartDate)->format('M d, Y') : 'N/A';

                // Check deadline status flags for upcoming projects too
                $deadlineNear = false;
                $deadlineApproaching = false;
                if ($project->deadline) {
                    $deadlineDate = $project->deadline;
                    
                    if ($deadlineDate->isPast()) {
                        $deadlineNear = true;
                    } else {
                        $daysUntilDeadline = $deadlineDate->diffInDays(now());
                        // Near if within 3 days (urgent)
                        $deadlineNear = $daysUntilDeadline <= 3;
                        // Approaching if between 4-7 days (warning)
                        $deadlineApproaching = $daysUntilDeadline > 3 && $daysUntilDeadline <= 7;
                    }
                }
                
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'start_date' => $startDate,
                    'deadline' => $project->deadline ? Date::parse($project->deadline)->format('M d, Y') : 'N/A',
                    'resources_count' => $project->resources->count(),
                    'is_overdue' => $project->is_overdue,
                    'deadline_near' => $deadlineNear,
                    'deadline_approaching' => $deadlineApproaching,
                    'assigned_resources' => $project->resources->map(fn($r) => $r->user->name)->implode(', '),
                ];
            }),
            'resources' => $resources->map(function ($resource) {
                 // Find the current or next project assignment (simplified)
                 $currentProject = $resource->projects()
                    ->where('project_resource.start_date', '<=', now()->toDateString())
                    ->where(function ($q) {
                        $q->where('project_resource.end_date', '>=', now()->toDateString())
                            ->orWhereNull('project_resource.end_date');
                    })
                    ->orderBy('project_resource.start_date')
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
