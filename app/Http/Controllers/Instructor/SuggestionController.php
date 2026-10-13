<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\Comment;
use App\Helpers\ProfanityHelper;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function index()
    {
        $suggestions = Suggestion::with('user')->latest()->paginate(10);
        $stats = [
            'total'       => Suggestion::count(),
            'pending'     => Suggestion::where('status', 'pending')->count(),
            'reviewed'    => Suggestion::where('status', 'reviewed')->count(),
            'implemented' => Suggestion::where('status', 'implemented')->count(),
            'rejected'    => Suggestion::where('status', 'rejected')->count(),
        ];

        return view('instructor.suggestions.index', compact('suggestions', 'stats'));
    }

    public function show(Suggestion $suggestion)
    {
        $suggestion->load(['user', 'comments.user']);

        return view('instructor.suggestions.show', compact('suggestion'));
    }

    public function updateStatus(Request $request, Suggestion $suggestion)
    {
        $validated = $request->validate([
            'status'      => 'required|in:pending,reviewed,implemented,rejected',
        ]);

        $suggestion->update($validated);

        return back()->with('success', 'Suggestion updated successfully.');
    }

    public function storeComment(Request $request, Suggestion $suggestion)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $flagged = ProfanityHelper::has($validated['body']);

        // One comment per user per suggestion (anti-spam)
        $suggestion->comments()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'body'        => $validated['body'],
                'flagged'     => $flagged,
                'flag_reason' => $flagged ? 'profanity' : null,
            ]
        );

        $msg = $flagged
            ? 'Comment submitted — pending review.'
            : 'Comment added successfully.';

        return back()->with('success', $msg);
    }

    public function updateComment(Request $request, Suggestion $suggestion, Comment $comment)
    {
        abort_unless($comment->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $flagged = ProfanityHelper::has($validated['body']);

        $comment->update([
            'body'        => $validated['body'],
            'flagged'     => $flagged,
            'flag_reason' => $flagged ? 'profanity' : null,
        ]);

        return back()->with('success', 'Comment updated successfully.');
    }
}