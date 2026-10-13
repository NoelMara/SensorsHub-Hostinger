<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Module;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function index(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $modules = $class->modules()->orderBy('order')->paginate(5);
        return view('instructor.classes.modules.index', compact('class', 'modules'));
    }

    public function create(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        return view('instructor.classes.modules.create', compact('class'));
    }

    public function store(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:51200',
            'is_published' => 'boolean',
        ]);

        $validated['class_id'] = $class->id;
        $validated['order'] = $class->modules()->count() + 1;
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Store in storage/app/public/modules/
            $path = $file->store('modules', 'public');

            $validated['file_path'] = $path; // relative path: modules/xyz.pdf
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $module = Module::create($validated);

        if ($module->is_published) {
            NotificationHelper::sendToClass(
                $class->id,
                '📖 New Module: ' . $module->title,
                'A new module is now available',
                route('dashboard.classes.modules.show', [$class, $module])
            );
        }

        return redirect()->route('instructor.classes.modules.index', $class)
            ->with('success', 'Module created successfully!');
    }

    public function show(Classroom $class, Module $module)
    {
        if ($module->class_id !== $class->id) abort(404);

        if (auth()->user()->isAdministrator()) {
            return view('user.classes.modules.show', compact('class', 'module'));
        }
        if (auth()->user()->isInstructor()) {
            if ($class->instructor_id !== auth()->id()) {
                abort(403);
            }
            return view('user.classes.modules.show', compact('class', 'module'));
        }

        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        return view('user.classes.modules.show', compact('class', 'module'));
    }

    public function edit(Classroom $class, Module $module)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($module->class_id !== $class->id) abort(404);
        return view('instructor.classes.modules.edit', compact('class', 'module'));
    }

    public function update(Request $request, Classroom $class, Module $module)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($module->class_id !== $class->id) abort(404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:51200',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('file')) {
            // Delete old file if it exists
            if ($module->file_path) {
                Storage::disk('public')->delete($module->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('modules', 'public');

            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $module->update($validated);

        if ($module->wasChanged('is_published') && $module->is_published) {
            NotificationHelper::sendToClass(
                $class->id,
                'New Module: ' . $module->title,
                'A new module is now available',
                route('dashboard.classes.modules.show', [$class, $module])
            );
        }

        return redirect()->route('instructor.classes.modules.index', $class)
            ->with('success', 'Module updated!');
    }

    public function destroy(Classroom $class, Module $module)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($module->class_id !== $class->id) abort(404);

        $filePath = $module->file_path;
        $module->delete();

        // Mark imported copies as draft
        if ($filePath) {
            Module::where('file_path', $filePath)->update(['is_published' => false]);

            // Delete file from storage if no other module uses it
            $publishedCount = Module::where('file_path', $filePath)
                ->where('is_published', true)->count();
            if ($publishedCount === 0) {
                Storage::disk('public')->delete($filePath);
            }
        }

        return back()->with('success', 'Module deleted!');
    }

    public function import(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $otherClasses = Classroom::where('instructor_id', auth()->id())
            ->where('id', '!=', $class->id)
            ->get();
        return view('instructor.classes.modules.import', compact('class', 'otherClasses'));
    }

    public function copyModules(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $request->validate([
            'from_class' => 'required|exists:classes,id',
            'modules' => 'required|array',
        ]);

        $sourceClass = Classroom::where('instructor_id', auth()->id())
            ->findOrFail($request->from_class);

        $modules = $sourceClass->modules()->whereIn('id', $request->modules)->get();

        foreach ($modules as $module) {
            Module::create([
                'class_id' => $class->id,
                'title' => $module->title,
                'content' => $module->content,
                'file_path' => $module->file_path,
                'file_name' => $module->file_name,
                'file_size' => $module->file_size,
                'order' => $class->modules()->count() + 1,
                'is_published' => false,
            ]);
        }

        return redirect()->route('instructor.classes.modules.index', $class)
            ->with('success', count($modules) . ' modules imported!');
    }

    public function studentIndex(Classroom $class)
    {
        if (auth()->user()->isAdministrator()) {
            $modules = $class->modules()->where('is_published', true)->orderBy('order')->paginate(10);
            return view('user.classes.modules.index', compact('class', 'modules'));
        }
        if (auth()->user()->isInstructor()) {
            if ($class->instructor_id !== auth()->id()) {
                abort(403);
            }
            $modules = $class->modules()->where('is_published', true)->orderBy('order')->paginate(10);
            return view('user.classes.modules.index', compact('class', 'modules'));
        }

        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $modules = $class->modules()->where('is_published', true)->orderBy('order')->paginate(10);
        return view('user.classes.modules.index', compact('class', 'modules'));
    }
}