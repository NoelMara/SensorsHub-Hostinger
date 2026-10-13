<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    protected $fillable = [
    'quiz_id', 'user_id', 'score', 'total_questions',
    'correct_answers', 'status', 'started_at', 'submitted_at', 'tab_switches',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'started_at' => 'datetime',
    ];


    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'user_id', 'user_id')
            ->whereIn('quiz_question_id', function ($q) {
                $q->select('id')->from('quiz_questions')->where('quiz_id', $this->quiz_id);
            });
    }
}