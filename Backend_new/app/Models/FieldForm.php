<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldForm extends Model
{
    use HasFactory;

    protected $table = 'field_forms';

    protected $fillable = [
        'project_id',
        'name',
        'schema_json',
        'version',
        'description',
    ];

    protected $casts = [
        'schema_json' => 'json',
    ];

    /**
     * Get the project that owns this form
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get all submissions for this form
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(FieldSubmission::class, 'form_id');
    }
}
