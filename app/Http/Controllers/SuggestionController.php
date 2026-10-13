<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;
use App\Helpers\ActivityLogHelper;
use App\Helpers\ProfanityHelper;
use App\Models\Comment;

class SuggestionController extends Controller
{
    // ─── Community View ───────────────────────────────────────────────
    public function community()
    {
        $suggestions = Suggestion::with(['user', 'comments' => function ($q) {
                $q->where('flagged', false);
            }])
            ->where('flagged', false)
            ->latest()
            ->paginate(12);

        return view('suggestions.community', compact('suggestions'));
    }

    // ─── My Suggestions ───────────────────────────────────────────────
    public function mySuggestions()
    {
        $suggestions = auth()->user()->suggestions()->latest()->get();
        return view('user.suggestions', compact('suggestions'));
    }

    // ─── Show Single Suggestion ───────────────────────────────────────
    public function show(Suggestion $suggestion)
    {
        // Flagged suggestions only visible to author + admins
        if ($suggestion->flagged
            && $suggestion->user_id !== auth()->id()
            && auth()->user()->role !== 'administrator') {
            abort(404);
        }

        $suggestion->load([
            'user',
            'comments' => function ($q) {
                $q->where('flagged', false)
                  ->orWhere('user_id', auth()->id());
            },
            'comments.user',
        ]);

        return view('user.suggestions-show', compact('suggestion'));
    }

    // ─── Store Suggestion ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty'  => 'nullable|string|in:Beginner,Intermediate,Advanced',
            'sensor_type' => 'nullable|string|max:255',
        ]);

        $flagged = ProfanityHelper::has($request->title . ' ' . $request->description);

        $suggestion = auth()->user()->suggestions()->create([
            'title'       => $request->title,
            'description' => $request->description,
            'difficulty'  => $request->difficulty,
            'sensor_type' => $request->sensor_type,
            'flagged'     => $flagged,
            'flag_reason' => $flagged ? 'profanity' : null,
        ]);

        ActivityLogHelper::log('created', 'suggestion', "submitted a suggestion '{$suggestion->title}'");

        $msg = $flagged
            ? 'Suggestion submitted — pending review.'
            : 'Suggestion submitted successfully! We\'ll review it soon.';

        return back()->with('success', $msg);
    }

    // ─── Edit Suggestion ──────────────────────────────────────────────
    public function edit(Suggestion $suggestion)
    {
        abort_if($suggestion->user_id !== auth()->id(), 403);
        abort_if($suggestion->status !== 'pending', 403, 'You can only edit pending suggestions.');

        return view('user.suggestions-edit', compact('suggestion'));
    }

    // ─── Update Suggestion ────────────────────────────────────────────
    public function update(Request $request, Suggestion $suggestion)
    {
        abort_if($suggestion->user_id !== auth()->id(), 403);
        abort_if($suggestion->status !== 'pending', 403, 'You can only edit pending suggestions.');

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty'  => 'nullable|string|in:Beginner,Intermediate,Advanced',
            'sensor_type' => 'nullable|string|max:255',
        ]);

        $flagged = ProfanityHelper::has($request->title . ' ' . $request->description);

        $suggestion->update([
            'title'       => $request->title,
            'description' => $request->description,
            'difficulty'  => $request->difficulty,
            'sensor_type' => $request->sensor_type,
            'flagged'     => $flagged,
            'flag_reason' => $flagged ? 'profanity' : null,
        ]);

        return redirect()->route('dashboard.suggestions')
            ->with('success', 'Suggestion updated successfully.');
    }

    // ─── Delete Suggestion ────────────────────────────────────────────
    public function destroy(Suggestion $suggestion)
    {
        abort_if($suggestion->user_id !== auth()->id(), 403);
        abort_if($suggestion->status !== 'pending', 403, 'You can only delete pending suggestions.');

        $suggestion->delete();

        return back()->with('success', 'Suggestion deleted successfully.');
    }

    // ─── Store Comment (1 per user - updateOrCreate) ──────────────────
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

    // ─── Update Comment ───────────────────────────────────────────────
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