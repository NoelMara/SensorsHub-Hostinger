<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'difficulty',
        'sensor_id',
        'image',
        'components_needed',
        'instructions',
        'code',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }

    public function savedByUsers()
    {
        return $this->hasMany(SavedProject::class);
    }
}
