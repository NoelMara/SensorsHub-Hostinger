<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'youtube_link',
        'youtube_id',
        'sensor_id',
        'category',
        'description',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
