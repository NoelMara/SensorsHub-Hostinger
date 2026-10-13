<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogHelper
{
    public static function log($action, $type, $description)
    {
        $user = auth()->user();
        
        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'user_role' => $user?->role ?? 'system',
            'action' => $action,
            'type' => $type,
            'description' => $description,
        ]);

        // Keep only last 1000 logs
        $count = ActivityLog::count();
        if ($count > 1000) {
            ActivityLog::oldest()->take($count - 1000)->delete();
        }
    }
}