<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'class_id',
        'title',
        'description',
        'instructions',
        'points',
        'due_date',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'due_date' => 'datetime',
        'points' => 'integer',
    ];

    public function class()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssessmentSubmission::class);
    }
}