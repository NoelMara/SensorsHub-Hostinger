<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'user_id', 'type', 'message', 'page_url', 'user_role',
        'status', 'admin_note', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Shortcut for admin view
    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'wont_fix']);
    }
}