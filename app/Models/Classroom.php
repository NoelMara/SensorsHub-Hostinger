<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'section',
        'code',
        'description',
        'instructor_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'user_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public static function generateCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'class_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'class_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'class_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'class_id');
    }
    public function resources()
    {
        return $this->hasMany(ClassResource::class, 'class_id');
    }
    
}