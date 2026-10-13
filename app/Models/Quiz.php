<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'class_id', 'title', 'description', 'instructions',
        'points', 'passing_score', 'due_date', 'time_limit', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function class()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }
}