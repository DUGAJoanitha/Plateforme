<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIRecommendation extends Model
{
    use HasFactory;

    protected $table = 'ai_recommendations';

    protected $fillable = [
        'project_id',
        'type',
        'content',
        'confidence',
        'generated_at',
        'read_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'read_at' => 'datetime',
        'confidence' => 'float',
    ];

    /**
     * Get the project this recommendation belongs to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Mark recommendation as read
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }
}
