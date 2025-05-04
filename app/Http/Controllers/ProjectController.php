<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Resource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Date;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with(['resources.user'])->latest()->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects->map(function ($project) {
                // Use the accessor for progress and overdue status
                $progress = $project->progress;
                $isOverdue = $project->is_overdue;

                // Check if deadline is near (e.g., within 7 days) and not overdue/completed
                $deadlineNear = false;
                if ($project->deadline && !$isOverdue && $project->status !== 'completed') {
                    $deadlineDate = $project->deadline; // Use the casted date object
                    $deadlineNear = $deadlineDate->diffInDays(now()) <= 7 && !$deadlineDate->isPast(); // Only near if not already past
                }

                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->description,
                    'estimate_hours' => $project->estimate_hours,
                    'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                    'status' => $project->status,
                    'created_at' => $project->created_at->format('Y-m-d H:i'),
                    'resources_count' => $project->resources->count(),
                    'progress' => $progress, // Use accessor value
                    'is_overdue' => $isOverdue, // Add overdue status
                    'deadline_near' => $deadlineNear, // Add deadline warning flag (updated logic)
                    'assigned_resources' => $project->resources->map(fn($r) => $r->user->name)->implode(', '),
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $resources = Resource::where('availability_status', '!=', 'unavailable')
            ->with('user')
            ->get(['id', 'user_id', 'capacity', 'availability_status']);

        return Inertia::render('Projects/Create', [
            'availableResources' => $resources->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'name' => $resource->user->name,
                    'capacity' => $resource->capacity,
                    'availability_status' => $resource->availability_status,
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimate_hours' => 'required|numeric|min:0.1',
            'hours_remaining' => 'nullable|numeric|min:0|lte:estimate_hours', // Added validation
            'deadline' => 'required|date',
            'status' => 'sometimes|in:upcoming,ongoing,completed',
            'resources' => 'nullable|array',
            'resources.*.id' => 'required|exists:resources,id',
            'resources.*.assigned_hours' => 'required|numeric|min:0.1',
            'resources.*.start_date' => 'required|date|after_or_equal:today',
            'resources.*.end_date' => 'nullable|date|after_or_equal:resources.*.start_date',
        ]);

        $project = Project::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimate_hours' => $validated['estimate_hours'],
            'hours_remaining' => $validated['hours_remaining'] ?? $validated['estimate_hours'], // Set default
            'deadline' => $validated['deadline'],
            'status' => $validated['status'] ?? 'upcoming'
        ]);

        if (!empty($validated['resources'])) {
            $syncData = [];
            foreach ($validated['resources'] as $resourceData) {
                $syncData[$resourceData['id']] = [
                    'assigned_hours' => $resourceData['assigned_hours'],
                    'start_date' => $resourceData['start_date'],
                    'end_date' => $resourceData['end_date'] ?? null
                ];
                $resource = Resource::find($resourceData['id']);
                if ($resource && $resource->availability_status !== 'unavailable') {
                     $resource->update(['availability_status' => 'assigned']);
                }
            }
            $project->resources()->sync($syncData);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['resources.user']);

        $latestEndDate = null;

        foreach ($project->resources as $resource) {
            $startDate = Date::parse($resource->pivot->start_date);
            $assignedHours = $resource->pivot->assigned_hours;
            $dailyCapacity = $resource->capacity > 0 ? $resource->capacity : 7; // Use a default if capacity is 0

            $estimatedDaysForResource = 0;
            if ($dailyCapacity > 0) {
                 $estimatedDaysForResource = ceil($assignedHours / $dailyCapacity);
            }

            $resourceEndDate = $startDate->copy();
            $daysAdded = 0;
            while ($daysAdded < $estimatedDaysForResource) {
                $resourceEndDate->addDay();
                if (!$resourceEndDate->isWeekend()) {
                    $daysAdded++;
                }
            }

            if ($latestEndDate === null || $resourceEndDate->gt($latestEndDate)) {
                $latestEndDate = $resourceEndDate;
            }
        }

        $estimatedEndDate = $latestEndDate ? $latestEndDate->format('Y-m-d') : null;

        return Inertia::render('Projects/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'estimate_hours' => $project->estimate_hours,
                'hours_remaining' => $project->hours_remaining, // Pass hours remaining
                'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                'status' => $project->status,
                'progress' => $project->progress, // Pass progress
                'is_overdue' => $project->is_overdue, // Pass overdue status
                'created_at' => $project->created_at->format('Y-m-d H:i'),
                'updated_at' => $project->updated_at->format('Y-m-d H:i'),
                'estimated_end_date' => $estimatedEndDate,
                'resources' => $project->resources->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'name' => $resource->user->name,
                        'capacity' => $resource->capacity,
                        'assigned_hours' => $resource->pivot->assigned_hours,
                        'start_date' => $resource->pivot->start_date ? Date::parse($resource->pivot->start_date)->format('Y-m-d') : null,
                        'end_date' => $resource->pivot->end_date ? Date::parse($resource->pivot->end_date)->format('Y-m-d') : null,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
         $project->load('resources');
         $assignedResourceIds = $project->resources->pluck('id')->toArray();

         $availableResources = Resource::where('availability_status', '!=', 'unavailable')
            ->orWhereIn('id', $assignedResourceIds)
            ->with('user')
            ->get(['id', 'user_id', 'capacity', 'availability_status']);

        return Inertia::render('Projects/Edit', [
             'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'estimate_hours' => $project->estimate_hours,
                'hours_remaining' => $project->hours_remaining, // Pass hours remaining
                'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                'status' => $project->status,
                'assigned_resources' => $project->resources->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'assigned_hours' => $resource->pivot->assigned_hours,
                        'start_date' => $resource->pivot->start_date ? Date::parse($resource->pivot->start_date)->format('Y-m-d') : null,
                        'end_date' => $resource->pivot->end_date ? Date::parse($resource->pivot->end_date)->format('Y-m-d') : null,
                    ];
                })->keyBy('id')->toArray(),
            ],
            'availableResources' => $availableResources->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'name' => $resource->user->name,
                    'capacity' => $resource->capacity,
                    'availability_status' => $resource->availability_status,
                ];
            })
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
         $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimate_hours' => 'required|numeric|min:0.1',
            'hours_remaining' => 'required|numeric|min:0|lte:estimate_hours', // Required on update
            'deadline' => 'required|date',
            'status' => 'required|in:upcoming,ongoing,completed',
            'resources' => 'nullable|array',
            'resources.*.id' => 'required|exists:resources,id',
            'resources.*.assigned_hours' => 'required|numeric|min:0.1',
            'resources.*.start_date' => 'required|date',
            'resources.*.end_date' => 'nullable|date|after_or_equal:resources.*.start_date',
        ]);

        $project->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimate_hours' => $validated['estimate_hours'],
            'hours_remaining' => $validated['hours_remaining'], // Update hours remaining
            'deadline' => $validated['deadline'],
            'status' => $validated['status']
        ]);

        // --- Resource Syncing Logic ---
        $newResourceIds = [];
        $syncData = [];
        if (!empty($validated['resources'])) {
            foreach ($validated['resources'] as $resourceData) {
                $newResourceIds[] = $resourceData['id'];
                $syncData[$resourceData['id']] = [
                    'assigned_hours' => $resourceData['assigned_hours'],
                    'start_date' => $resourceData['start_date'],
                    'end_date' => $resourceData['end_date'] ?? null
                ];
            }
        }

        $oldResourceIds = $project->resources()->pluck('resources.id')->toArray();
        $project->resources()->sync($syncData);

        $addedResources = array_diff($newResourceIds, $oldResourceIds);
        $removedResources = array_diff($oldResourceIds, $newResourceIds);

        if (!empty($addedResources)) {
            Resource::whereIn('id', $addedResources)
                    ->where('availability_status', '!=', 'unavailable')
                    ->update(['availability_status' => 'assigned']);
        }

        if (!empty($removedResources)) {
            $resourcesToUpdate = Resource::whereIn('id', $removedResources)
                                        ->whereDoesntHave('projects')
                                        ->get();
            foreach($resourcesToUpdate as $res) {
                $res->update(['availability_status' => 'available']);
            }
        }
        // --- End Resource Syncing Logic ---

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $assignedResourceIds = $project->resources()->pluck('resources.id')->toArray();
        $project->resources()->detach();
        $project->delete();

        if (!empty($assignedResourceIds)) {
            $resourcesToUpdate = Resource::whereIn('id', $assignedResourceIds)
                                        ->whereDoesntHave('projects')
                                        ->get();
            foreach($resourcesToUpdate as $res) {
                $res->update(['availability_status' => 'available']);
            }
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

     /**
      * Display the calendar view for the specified project.
      */
     public function calendar(Project $project)
     {
         $project->load(['resources.user', 'calendarEvents']);

         $generatedEvents = [];
         $resourceColors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6', '#1abc9c', '#e67e22'];

         foreach ($project->resources as $index => $resource) {
             $startDate = Date::parse($resource->pivot->start_date);
             $endDate = $resource->pivot->end_date ? Date::parse($resource->pivot->end_date) : null;
             $assignedHours = $resource->pivot->assigned_hours;
             $dailyCapacity = $resource->capacity > 0 ? $resource->capacity : 7;

             $daysNeeded = ceil($assignedHours / $dailyCapacity);
             $currentDate = $startDate->copy();
             $daysScheduled = 0;

             while ($daysScheduled < $daysNeeded) {
                 if ($currentDate->isWeekend()) {
                     $currentDate->addDay();
                     continue;
                 }
                 if ($endDate && $currentDate->gt($endDate)) {
                     break;
                 }

                 $generatedEvents[] = [
                     'id' => "generated-{$project->id}-{$resource->id}-{$currentDate->format('Ymd')}",
                     'title' => "{$resource->user->name}",
                     'start' => $currentDate->format('Y-m-d'),
                     'end' => $currentDate->format('Y-m-d'),
                     'resourceId' => $resource->id,
                     'color' => $resourceColors[$index % count($resourceColors)],
                     'allDay' => true,
                 ];

                 $daysScheduled++;
                 $currentDate->addDay();
             }
         }

         $allEvents = $generatedEvents; // Use generated events for now

         return Inertia::render('Projects/Calendar', [
              'project' => [
                 'id' => $project->id,
                 'title' => $project->title,
                 'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
              ],
              'projectResources' => $project->resources->map(function ($resource) use ($resourceColors) {
                  static $colorIndex = 0;
                  $color = $resourceColors[$colorIndex % count($resourceColors)];
                  $colorIndex++;
                  return [
                      'id' => $resource->id,
                      'name' => $resource->user->name,
                      'color' => $color,
                      'assigned_hours' => $resource->pivot->assigned_hours,
                      'start_date' => $resource->pivot->start_date ? Date::parse($resource->pivot->start_date)->format('Y-m-d') : null,
                      'end_date' => $resource->pivot->end_date ? Date::parse($resource->pivot->end_date)->format('Y-m-d') : null,
                  ];
              }),
             'calendarEvents' => $allEvents
         ]);
     }
}