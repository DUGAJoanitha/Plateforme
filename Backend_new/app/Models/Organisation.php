<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    use HasFactory;

    protected $table = 'organisations';

    protected $fillable = [
        'name',
        'type',
        'country',
        'settings_json',
    ];

    protected $casts = [
        'settings_json' => 'json',
    ];

    /**
     * Get all users belonging to this organisation
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'org_id');
    }

    /**
     * Get all projects for this organisation
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'org_id');
    }

    /**
     * Get all programmes for this organisation
     */
    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class, 'org_id');
    }
}
