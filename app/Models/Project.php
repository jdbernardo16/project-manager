<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'estimate_hours',
        'hours_remaining', // Added
        'deadline',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estimate_hours' => 'float',
        'hours_remaining' => 'float', // Added
        'deadline' => 'date',
    ];

    /**
     * The resources that belong to the project.
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'project_resource')
                    ->withPivot('assigned_hours', 'start_date', 'end_date')
                    ->withTimestamps();
    }

    /**
     * Get the calendar events for the project.
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    /**
     * Calculate the project progress percentage.
     *
     * @return float
     */
    public function getProgressAttribute(): float
    {
        if ($this->status === 'completed') {
            return 100.0;
        }

        if ($this->estimate_hours <= 0) {
            return 0.0; // Avoid division by zero
        }

        // If hours_remaining is null, assume no progress unless estimate is also 0
        $remaining = $this->hours_remaining ?? $this->estimate_hours;

        // Ensure remaining doesn't exceed estimate (can happen if estimate is lowered)
        $remaining = min($remaining, $this->estimate_hours);

        // Ensure remaining is not negative
        $remaining = max(0, $remaining);

        $completedHours = $this->estimate_hours - $remaining;

        $progress = ($completedHours / $this->estimate_hours) * 100;

        return round(max(0, min(100, $progress)), 2); // Clamp between 0 and 100
    }

    /**
     * Check if the project is overdue.
     *
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        // Check if deadline exists, is in the past, and status is not 'completed'
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'completed';
    }
}