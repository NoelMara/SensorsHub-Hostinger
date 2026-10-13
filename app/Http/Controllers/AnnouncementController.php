<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Announcement;
use App\Models\Notification;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $announcements = $class->announcements()->latest()->paginate(5);
        return view('instructor.classes.announcements.index', compact('class', 'announcements'));
    }

    public function create(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        return view('instructor.classes.announcements.create', compact('class'));
    }

    public function store(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['class_id'] = $class->id;
        $validated['is_published'] = $request->has('is_published');

        $announcement = Announcement::create($validated);

        if ($announcement->is_published) {
            NotificationHelper::sendToClass(
                $class->id,
                '📢 ' . $announcement->title,
                \Str::limit($announcement->content, 100),
                route('dashboard.classes.announcements.index', $class)
            );
        }

        return redirect()
            ->route('instructor.classes.announcements.index', $class)
            ->with('success', 'Announcement posted!');
    }

    public function edit(Classroom $class, Announcement $announcement)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($announcement->class_id !== $class->id) abort(404);
        return view('instructor.classes.announcements.edit', compact('class', 'announcement'));
    }

    public function update(Request $request, Classroom $class, Announcement $announcement)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($announcement->class_id !== $class->id) abort(404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');
        $announcement->update($validated);

        if ($announcement->wasChanged('is_published') && $announcement->is_published) {
            NotificationHelper::sendToClass(
                $class->id,
                $announcement->title,
                \Str::limit($announcement->content, 100),
                route('dashboard.classes.announcements.index', $class)
            );
        }

        return redirect()
            ->route('instructor.classes.announcements.index', $class)
            ->with('success', 'Announcement updated!');
    }

    public function destroy(Classroom $class, Announcement $announcement)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($announcement->class_id !== $class->id) abort(404);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted!');
    }

    public function studentIndex(Classroom $class)
    {
        // Allow only the class owner or admin to view
        if (auth()->user()->isAdministrator()) {
            $announcements = $class->announcements()
                ->where('is_published', true)
                ->latest()
                ->paginate(10);
            return view('user.classes.announcements.index', compact('class', 'announcements'));
        }
        if (auth()->user()->isInstructor()) {
            if ($class->instructor_id !== auth()->id()) {
                abort(403);
            }
            $announcements = $class->announcements()
                ->where('is_published', true)
                ->latest()
                ->paginate(10);
            return view('user.classes.announcements.index', compact('class', 'announcements'));
        }
        
        // Students must be enrolled
        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $announcements = $class->announcements()
            ->where('is_published', true)
            ->latest()
            ->paginate(10);
        return view('user.classes.announcements.index', compact('class', 'announcements'));
    }

    public function import(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $otherClasses = Classroom::where('instructor_id', auth()->id())
            ->where('id', '!=', $class->id)
            ->get();
        return view('instructor.classes.announcements.import', compact('class', 'otherClasses'));
    }

    public function copyAnnouncements(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $request->validate([
            'from_class' => 'required|exists:classes,id',
            'announcements' => 'required|array',
        ]);

        $sourceClass = Classroom::where('instructor_id', auth()->id())
            ->findOrFail($request->from_class);

        $announcements = $sourceClass->announcements()->whereIn('id', $request->announcements)->get();

        foreach ($announcements as $announcement) {
            Announcement::create([
                'class_id' => $class->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'is_published' => false,
            ]);
        }

        return redirect()->route('instructor.classes.announcements.index', $class)
            ->with('success', count($announcements) . ' announcements imported!');
    }
}