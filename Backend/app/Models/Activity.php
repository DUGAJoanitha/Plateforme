<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'project_id',
        'name',
        'responsible_id',
        'start_date',
        'end_date',
        'status',
        'progress',
        'depends_on',
        'description',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'progress' => 'integer',
    ];

    /**
     * Get the project that owns this activity
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the responsible user
     */
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Get all evidence for this activity
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(FieldSubmission::class, 'activity_id');
    }

    /**
     * Check if activity is blocked by dependencies
     */
    public function isBlocked(): bool
    {
        if (!$this->depends_on) {
            return false;
        }
        
        $dependency = Activity::find($this->depends_on);
        return $dependency && $dependency->status !== 'completed';
    }

    /**
     * Mark activity as completed
     */
    public function markComplete(): void
    {
        $this->update([
            'status' => 'completed',
            'progress' => 100,
        ]);
    }
}
