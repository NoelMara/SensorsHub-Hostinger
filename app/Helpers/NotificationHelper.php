<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function send($userId, $title, $message, $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    public static function sendToClass($classId, $title, $message, $link = null)
    {
        $students = \App\Models\Classroom::find($classId)
            ->students()
            ->wherePivot('status', 'approved')
            ->get();

        foreach ($students as $student) {
            self::send($student->id, $title, $message, $link);
        }
    }
}