<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risk extends Model
{
    use HasFactory;

    protected $table = 'risks';

    protected $fillable = [
        'project_id',
        'description',
        'probability',
        'impact',
        'score',
        'mitigation',
        'status',
    ];

    protected $casts = [
        'probability' => 'integer',
        'impact' => 'integer',
        'score' => 'float',
    ];

    /**
     * Get the project that owns this risk
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Calculate risk score (probability * impact)
     */
    public function calculateScore(): void
    {
        $this->score = $this->probability * $this->impact;
    }

    /**
     * Get risk level (LOW, MEDIUM, HIGH, CRITICAL)
     */
    public function getRiskLevel(): string
    {
        $score = $this->probability * $this->impact;
        if ($score >= 20) {
            return 'CRITICAL';
        } elseif ($score >= 15) {
            return 'HIGH';
        } elseif ($score >= 8) {
            return 'MEDIUM';
        }
        return 'LOW';
    }

    protected static function booted()
    {
        static::saving(function ($risk) {
            $risk->calculateScore();
        });
    }
}
