<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'difficulty',
        'sensor_type',
        'status',
        'admin_notes',
        'flagged',
        'flag_reason',
    ];

    protected $casts = [
        'flagged' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}