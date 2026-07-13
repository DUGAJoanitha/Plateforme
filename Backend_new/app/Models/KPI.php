<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KPI extends Model
{
    use HasFactory;

    protected $table = 'kpis';

    protected $fillable = [
        'project_id',
        'name',
        'target_value',
        'current_value',
        'unit',
        'frequency',
        'baseline',
        'description',
    ];

    protected $casts = [
        'target_value' => 'float',
        'current_value' => 'float',
        'baseline' => 'float',
    ];

    /**
     * Get the project that owns this KPI
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get all measurements for this KPI
     */
    public function measures(): HasMany
    {
        return $this->hasMany(KPIMeasure::class, 'kpi_id');
    }

    /**
     * Calculate performance percentage (can exceed 100 if target is surpassed)
     */
    public function calculatePerformance(): float
    {
        if ($this->target_value == 0) {
            return 0;
        }
        
        return ($this->current_value / $this->target_value) * 100;
    }

    /**
     * Check if KPI is on track (>= 70% of target)
     */
    public function isOnTrack(): bool
    {
        return $this->calculatePerformance() >= 70;
    }

    /**
     * Get measurement history
     */
    public function getHistory()
    {
        return $this->measures()
            ->orderBy('collected_at', 'desc')
            ->get();
    }
}
