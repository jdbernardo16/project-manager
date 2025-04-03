<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash; // For potential user creation/update
use Illuminate\Validation\Rule; // For unique email validation on update
use Illuminate\Support\Facades\DB; // For transaction

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resources = Resource::with(['user', 'projects'])->latest()->get(); // Eager load user and projects

        return Inertia::render('Resources/Index', [
            'resources' => $resources->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'user_id' => $resource->user_id,
                    'name' => $resource->user->name,
                    'email' => $resource->user->email, // Include email
                    'capacity' => $resource->capacity,
                    'availability_status' => $resource->availability_status,
                    'notes' => $resource->notes,
                    'created_at' => $resource->created_at->format('Y-m-d H:i'),
                    'current_projects_count' => $resource->projects()->wherePivot('end_date', '>=', now()->toDateString())->orWherePivotNull('end_date')->count(), // Count active/ongoing projects
                    'current_projects_list' => $resource->projects()->wherePivot('end_date', '>=', now()->toDateString())->orWherePivotNull('end_date')->limit(3)->pluck('title')->implode(', '), // List a few project titles
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * A resource is linked to a user. We might create a new user or link to an existing one.
     * For simplicity, let's assume we create a new user for each new resource initially.
     */
    public function create()
    {
        // Optionally, provide a list of existing users without a resource profile to link to.
        // $usersWithoutResource = User::whereDoesntHave('resource')->get(['id', 'name', 'email']);

        return Inertia::render('Resources/Create', [
            // 'linkableUsers' => $usersWithoutResource // Uncomment if linking existing users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * This will also create a corresponding User record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // User details
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // Add password confirmation
            // Resource details
            'capacity' => 'required|numeric|min:0|max:24', // Max 24 hours capacity
            'availability_status' => 'sometimes|in:available,assigned,unavailable', // Default handled below
            'notes' => 'nullable|string',
            // 'role' => 'sometimes|in:admin,project_manager,resource' // Allow setting role, default handled below
        ]);

        DB::beginTransaction(); // Start transaction

        try {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $request->input('role', 'resource'), // Default role to 'resource'
            ]);

            // Create the resource linked to the user
            $resource = Resource::create([
                'user_id' => $user->id,
                'capacity' => $validated['capacity'],
                'availability_status' => $validated['availability_status'] ?? 'available', // Default status
                'notes' => $validated['notes'],
            ]);

            DB::commit(); // Commit transaction

            return redirect()->route('resources.index')
                ->with('success', 'Resource and associated user created successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            // Log the error message: Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create resource. Please try again.']); // Generic error message
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        $resource->load(['user', 'projects.resources.user']); // Load user and projects with their resources/users

        // Calculate utilization (example: based on assigned hours vs capacity over a period)
        // This requires more complex logic involving project timelines and assigned hours.
        // Placeholder for now:
        $utilization = $resource->availability_status === 'assigned' ? 100 : 0;

        return Inertia::render('Resources/Show', [
            'resource' => [
                 'id' => $resource->id,
                 'user_id' => $resource->user_id,
                 'name' => $resource->user->name,
                 'email' => $resource->user->email,
                 'role' => $resource->user->role, // Show user role
                 'capacity' => $resource->capacity,
                 'availability_status' => $resource->availability_status,
                 'notes' => $resource->notes,
                 'created_at' => $resource->created_at->format('Y-m-d H:i'),
                 'updated_at' => $resource->updated_at->format('Y-m-d H:i'),
                 'utilization_percentage' => $utilization, // Pass calculated utilization
                 'projects' => $resource->projects->map(function ($project) {
                     return [
                         'id' => $project->id,
                         'title' => $project->title,
                         'status' => $project->status,
                         'deadline' => $project->deadline ? \Illuminate\Support\Facades\Date::parse($project->deadline)->format('Y-m-d') : null,
                         'assigned_hours_on_project' => $project->pivot->assigned_hours,
                         'start_date_on_project' => $project->pivot->start_date ? \Illuminate\Support\Facades\Date::parse($project->pivot->start_date)->format('Y-m-d') : null,
                         'end_date_on_project' => $project->pivot->end_date ? \Illuminate\Support\Facades\Date::parse($project->pivot->end_date)->format('Y-m-d') : null,
                     ];
                 }),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        $resource->load('user'); // Load user data for the form

        return Inertia::render('Resources/Edit', [
            'resource' => [ // Pass structured data
                 'id' => $resource->id,
                 'user_id' => $resource->user_id,
                 'name' => $resource->user->name,
                 'email' => $resource->user->email,
                 'role' => $resource->user->role,
                 'capacity' => $resource->capacity,
                 'availability_status' => $resource->availability_status,
                 'notes' => $resource->notes,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
         $validated = $request->validate([
            // User details
            'name' => 'required|string|max:255',
            // Ensure email is unique, ignoring the current user's email
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($resource->user_id)],
            'password' => 'nullable|string|min:8|confirmed', // Allow optional password update
            // Resource details
            'capacity' => 'required|numeric|min:0|max:24',
            'availability_status' => 'required|in:available,assigned,unavailable',
            'notes' => 'nullable|string',
            'role' => 'required|in:admin,project_manager,resource' // Role is required on update
        ]);

        DB::beginTransaction();

        try {
            // Update user details
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $resource->user()->update($userData);

            // Update resource details
            $resource->update([
                'capacity' => $validated['capacity'],
                'availability_status' => $validated['availability_status'],
                'notes' => $validated['notes'],
            ]);

            DB::commit();

            return redirect()->route('resources.index') // Redirect to index after update
                ->with('success', 'Resource updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update resource. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Consider implications: what happens to assigned projects?
     * Option 1: Prevent deletion if assigned.
     * Option 2: Unassign from projects.
     * Option 3: Delete user as well (cascade?).
     * Let's go with Option 1 for safety initially.
     */
    public function destroy(Resource $resource)
    {
        // Check if the resource is assigned to any active projects
        $activeAssignments = $resource->projects()
                                    ->where(function ($query) {
                                        $query->where('status', '!=', 'completed') // Check project status
                                              ->orWhereNull('status'); // Or if status is null
                                    })
                                    ->where(function ($query) { // Check assignment dates
                                        $query->where('project_resource.end_date', '>=', now()->toDateString())
                                              ->orWhereNull('project_resource.end_date');
                                    })
                                    ->count();

        if ($activeAssignments > 0) {
            return redirect()->route('resources.index')
                ->with('error', 'Cannot delete resource. It is currently assigned to active projects.');
        }

        DB::beginTransaction();
        try {
            // Optionally detach from completed projects if needed, though cascade might handle it
            // $resource->projects()->detach();

            // Delete the resource record
            $resource->delete();

            // Optionally delete the associated user record if it's tightly coupled
            // Be careful with this - ensure user doesn't have other roles/data
            // $resource->user()->delete();

            DB::commit();

            return redirect()->route('resources.index')
                ->with('success', 'Resource deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            return redirect()->route('resources.index')
                ->with('error', 'Failed to delete resource.');
        }
    }

    /**
     * Toggle the availability status of a resource.
     */
    public function toggleAvailability(Request $request, Resource $resource)
    {
        // Simple toggle: if available -> unavailable, if unavailable -> available
        // Ignores 'assigned' status for manual toggling.
        $newStatus = $resource->availability_status === 'available' ? 'unavailable' : 'available';

        // Prevent making 'assigned' resource 'unavailable' directly via toggle?
        // Or allow it, assuming PM knows what they're doing? Let's allow it for now.
        // if ($resource->availability_status === 'assigned') {
        //     return back()->with('warning', 'Resource is currently assigned to projects. Change status manually if needed.');
        // }

        $resource->update(['availability_status' => $newStatus]);

        return back()->with('success', 'Resource availability updated.'); // Redirect back with feedback
    }
}
