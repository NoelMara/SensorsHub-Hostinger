<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentSubmission extends Model
{
    protected $fillable = [
        'assessment_id',
        'user_id',
        'content',
        'file_path',
        'score',
        'feedback',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'submitted_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}