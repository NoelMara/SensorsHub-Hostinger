<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Comment;
use App\Models\Suggestion;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => 'required|in:suggestion,comment',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        // Block admins from reporting
        if (auth()->user()->isAdministrator()) {
            return back()->with('error', 'Administrators cannot submit reports.');
        }

        // Load the reported item
        $isSuggestion = $validated['reportable_type'] === 'suggestion';

        if ($isSuggestion) {
            $item = Suggestion::with('user')->find($validated['reportable_id']);
        } else {
            $item = Comment::with('user')->find($validated['reportable_id']);
        }

        if (!$item) {
            return back()->with('error', 'The item you are trying to report does not exist.');
        }
        if ($item->user?->isAdministrator()) {
            return back()->with('error', 'You cannot report an administrator.');
        }

        // Create the report
        Report::create([
            'reporter_id' => auth()->id(),
            'reportable_type' => $isSuggestion ? Suggestion::class : Comment::class,
            'reportable_id' => $item->id,
            'reason' => $validated['reason'],
        ]);

        // Build the notification message
        $reporterName = auth()->user()->name;
        $reportedUserName = $item->user?->name ?? 'Deleted user';
        $preview = $isSuggestion
            ? Str::limit($item->title, 50)
            : Str::limit($item->body, 50);
        $itemType = $isSuggestion ? 'suggestion' : 'comment';

        $message = "{$reporterName} reported {$reportedUserName}'s {$itemType} \"{$preview}\" — Reason: {$validated['reason']}";

        // Determine the link
        if ($isSuggestion) {
            $link = route('administrator.suggestions.show', $item->id);
        } else {
            $link = route('administrator.suggestions.show', $item->suggestion_id);
        }

        // Notify ALL admins
        $admins = User::where('role', 'administrator')->get();
        foreach ($admins as $admin) {
            NotificationHelper::send(
                $admin->id,
                '🚨 New Report',
                $message,
                $link
            );
        }

        return back()->with('success', 'Report submitted. An administrator will review it.');
    }
}
