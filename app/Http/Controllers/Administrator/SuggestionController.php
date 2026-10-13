<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\Comment;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Suggestion::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('flagged')) {
            $query->where('flagged', $request->flagged === '1');
        }

        $suggestions = $query->latest()->paginate(10)->withQueryString();
        $stats = [
            'total'       => Suggestion::count(),
            'pending'     => Suggestion::where('status', 'pending')->count(),
            'reviewed'    => Suggestion::where('status', 'reviewed')->count(),
            'implemented' => Suggestion::where('status', 'implemented')->count(),
            'rejected'    => Suggestion::where('status', 'rejected')->count(),
            'flagged'     => Suggestion::where('flagged', true)->count(),
        ];

        return view('administrator.suggestions.index', compact('suggestions', 'stats'));
    }

    public function show(Suggestion $suggestion)
    {
        $suggestion->load(['user', 'comments.user']);

        return view('administrator.suggestions.show', compact('suggestion'));
    }

    public function updateStatus(Request $request, Suggestion $suggestion)
    {
        $validated = $request->validate([
            'status'      => 'required|in:pending,reviewed,implemented,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $suggestion->update($validated);
        ActivityLogHelper::log('changed', 'suggestion', "marked suggestion '{$suggestion->title}' as {$validated['status']}");
        return back()->with('success', 'Suggestion updated successfully.');
    }

    public function approve(Suggestion $suggestion)
    {
        $suggestion->update([
            'flagged'     => false,
            'flag_reason' => null,
        ]);

        ActivityLogHelper::log('approved', 'suggestion', "approved flagged suggestion '{$suggestion->title}'");

        return back()->with('success', 'Suggestion approved and visible to the community.');
    }

    public function destroy(Suggestion $suggestion)
    {
        ActivityLogHelper::log('deleted', 'suggestion', "deleted suggestion '{$suggestion->title}'");
        $suggestion->delete();
        return redirect()->route('administrator.suggestions.index')->with('success', 'Suggestion deleted.');
    }

    public function destroyComment(Suggestion $suggestion, Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }

    public function storeComment(Request $request, Suggestion $suggestion)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        // One comment per user per suggestion (anti-spam)
        $suggestion->comments()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['body' => $validated['body']]
        );

        return back()->with('success', 'Comment added successfully.');
    }

    public function updateComment(Request $request, Suggestion $suggestion, Comment $comment)
    {
        abort_unless($comment->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment->update(['body' => $validated['body']]);

        return back()->with('success', 'Comment updated successfully.');
    }
}