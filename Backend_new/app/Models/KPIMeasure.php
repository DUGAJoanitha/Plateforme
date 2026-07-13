<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KPIMeasure extends Model
{
    use HasFactory;

    protected $table = 'kpi_measures';

    protected $fillable = [
        'kpi_id',
        'value',
        'collected_at',
        'collected_by',
        'notes',
    ];

    protected $casts = [
        'value' => 'float',
        'collected_at' => 'datetime',
    ];

    /**
     * Get the KPI that owns this measurement
     */
    public function kpi(): BelongsTo
    {
        return $this->belongsTo(KPI::class, 'kpi_id');
    }

    /**
     * Get the user who collected this measurement
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
