<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    use HasFactory;

    protected $table = 'programmes';

    protected $fillable = [
        'name',
        'description',
        'org_id',
    ];

    /**
     * Get the organisation that owns this programme
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'org_id');
    }

    /**
     * Get all projects in this programme
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'programme_id');
    }
}
