<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldSubmission extends Model
{
    use HasFactory;

    protected $table = 'field_submissions';

    protected $fillable = [
        'form_id',
        'agent_id',
        'activity_id',
        'data_json',
        'gps_lat',
        'gps_lng',
        'photos_json',
        'synced_at',
        'status',
        'validation_notes',
    ];

    protected $casts = [
        'data_json' => 'json',
        'photos_json' => 'json',
        'synced_at' => 'datetime',
        'gps_lat' => 'float',
        'gps_lng' => 'float',
    ];

    /**
     * Get the form this submission belongs to
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(FieldForm::class, 'form_id');
    }

    /**
     * Get the agent who submitted this
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the activity this submission is linked to
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    /**
     * Get location as array
     */
    public function getLocation(): array
    {
        return [
            'latitude' => $this->gps_lat,
            'longitude' => $this->gps_lng,
        ];
    }

    /**
     * Validate submission
     */
    public function validate(): void
    {
        $this->update(['status' => 'validated']);
    }

    /**
     * Mark as synced
     */
    public function markSynced(): void
    {
        $this->update(['synced_at' => now()]);
    }
}
