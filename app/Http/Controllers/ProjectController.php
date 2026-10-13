<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SavedProject;
use App\Models\Sensor;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::where('is_active', true)->with('sensor');

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by sensor
        if ($request->filled('sensor_id')) {
            $query->where('sensor_id', $request->sensor_id);
        }

        $projects = $query->latest()->paginate(12)->appends($request->all());

        // Get saved project IDs for the current user
        $savedProjectIds = auth()->check()
            ? SavedProject::where('user_id', auth()->id())->pluck('project_id')->toArray()
            : [];
        $sensors = Sensor::where('is_active', true)->orderBy('name')->get();

        return view('projects.index', compact('projects', 'sensors', 'savedProjectIds'));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)
            ->where('is_active', true)
            ->with('sensor')
            ->firstOrFail();
        
        $isSaved = auth()->check() && SavedProject::where('user_id', auth()->id())
            ->where('project_id', $project->id)
            ->exists();
        
        $relatedProjects = collect();
        if ($project->sensor_id) {
            $relatedProjects = Project::where('sensor_id', $project->sensor_id)
                ->where('id', '!=', $project->id)
                ->where('is_active', true)
                ->limit(3)
                ->get();
        }
        
        return view('projects.show', compact('project', 'isSaved', 'relatedProjects'));
    }

    public function saved()
    {
        $savedProjects = SavedProject::where('user_id', auth()->id())->with('project')->latest()->get();
        return view('user.saved-projects', compact('savedProjects'));
    }

    public function toggleSave(Project $project)
    {
        $savedProject = SavedProject::where('user_id', auth()->id())->where('project_id', $project->id)->first();
        
        if ($savedProject) {
            $savedProject->delete();
            return back()->with('success', 'Project removed from saved list.');
        } else {
            SavedProject::create([
                'user_id' => auth()->id(),
                'project_id' => $project->id,
            ]);
            return back()->with('success', 'Project saved successfully!');
        }
    }
}