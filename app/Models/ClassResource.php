<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassResource extends Model
{
    protected $table = 'class_resource';

    protected $fillable = ['class_id', 'resource_type', 'resource_id'];

    public function class()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
}