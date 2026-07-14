<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'type',
        'file_path',
        'parameters',
        'status',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
