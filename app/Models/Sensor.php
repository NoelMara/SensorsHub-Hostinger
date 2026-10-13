<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'how_it_works',
        'use_cases',
        'components_needed',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
