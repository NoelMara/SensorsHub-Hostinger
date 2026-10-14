<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // Show the submit form (student + instructor)
    public function create()
    {
        return view('feedback.create');
    }

    // Save submitted feedback
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bug,feature,other',
            'message' => 'required|string|min:10|max:2000',
        ], [
            'message.min' => 'Please write at least 10 characters so we can understand the issue.',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'message' => $validated['message'],
            'page_url' => $request->input('page_url'),
            'user_role' => auth()->user()->role,
            'status' => 'new',
        ]);

        return back()->with('success', 'Thanks! Your feedback has been sent.');
    }

    // Admin: list all feedback
    public function index(Request $request)
    {
        $query = Feedback::with('user')->latest();

        // Search by message or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $feedback = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Feedback::count(),
            'new' => Feedback::where('status', 'new')->count(),
            'read' => Feedback::where('status', 'read')->count(),
            'resolved' => Feedback::where('status', 'resolved')->count(),
            'wont_fix' => Feedback::where('status', 'wont_fix')->count(),
        ];

        return view('administrator.feedback.index', compact('feedback', 'counts'));
    }

    // Admin: show one feedback
    public function show(Feedback $feedback)
    {
        $feedback->load('user');

        return view('administrator.feedback.show', compact('feedback'));
    }

    // Admin: update status
    public function updateStatus(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,resolved,wont_fix',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $feedback->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? $feedback->admin_note,
            'resolved_at' => in_array($validated['status'], ['resolved', 'wont_fix'])
                ? ($feedback->resolved_at ?? now())
                : null,
        ]);

        return back()->with('success', 'Feedback updated.');
    }
    // Admin: delete feedback
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('administrator.feedback.index')
            ->with('success', 'Feedback deleted.');
    }
}
