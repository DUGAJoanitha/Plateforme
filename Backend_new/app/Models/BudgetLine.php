<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetLine extends Model
{
    use HasFactory;

    protected $table = 'budget_lines';

    protected $fillable = [
        'project_id',
        'category',
        'allocated',
        'spent',
        'alert_threshold',
        'description',
    ];

    protected $casts = [
        'allocated' => 'decimal:2',
        'spent' => 'decimal:2',
        'alert_threshold' => 'integer',
    ];

    /**
     * Get the project that owns this budget line
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get all expenses for this budget line
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'budget_line_id');
    }

    /**
     * Get remaining balance
     */
    public function getBalance(): float
    {
        return (float) ($this->allocated - $this->spent);
    }

    /**
     * Check if budget is over
     */
    public function isOverBudget(): bool
    {
        return $this->spent > $this->allocated;
    }

    /**
     * Get budget consumption percentage
     */
    public function getConsumptionPercentage(): float
    {
        if ($this->allocated == 0) {
            return 0;
        }
        
        return ($this->spent / $this->allocated) * 100;
    }

    /**
     * Check if alert threshold is reached
     */
    public function shouldTriggerAlert(): bool
    {
        return $this->getConsumptionPercentage() >= $this->alert_threshold;
    }
}
