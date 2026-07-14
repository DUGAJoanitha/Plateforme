<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'org_id',
        'programme_id',
        'budget_total',
        'start_date',
        'end_date',
        'status',
        'risk_score',
        'description',
    ];

    protected $casts = [
        'budget_total' => 'decimal:2',
        'risk_score' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the organisation that owns this project
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'org_id');
    }

    /**
     * Get the programme this project belongs to
     */
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }

    /**
     * Get all activities for this project
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'project_id');
    }

    /**
     * Get all KPIs for this project
     */
    public function kpis(): HasMany
    {
        return $this->hasMany(KPI::class, 'project_id');
    }

    /**
     * Get all budget lines for this project
     */
    public function budgetLines(): HasMany
    {
        return $this->hasMany(BudgetLine::class, 'project_id');
    }

    /**
     * Get all risks for this project
     */
    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class, 'project_id');
    }

    /**
     * Get all AI recommendations for this project
     */
    public function aiRecommendations(): HasMany
    {
        return $this->hasMany(AIRecommendation::class, 'project_id');
    }

    /**
     * Get all field forms for this project
     */
    public function fieldForms(): HasMany
    {
        return $this->hasMany(FieldForm::class, 'project_id');
    }
}
