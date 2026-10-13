<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $query = Sensor::where('is_active', true)
            ->withCount(['projects', 'videos']); 

        // Search by name or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('description', 'ilike', '%' . $request->search . '%');
            });
        }

        $sensors = $query->latest()->paginate(12)->appends($request->all());

        return view('sensors.index', compact('sensors'));
    }

    public function show($slug)
    {
        $sensor = Sensor::withCount(['projects', 'videos'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        $relatedProjects = $sensor->projects()
            ->where('is_active', true)
            ->take(3)
            ->get();
        
        $relatedVideos = $sensor->videos()
            ->where('is_active', true)
            ->take(3)
            ->get();
        
        return view('sensors.show', compact('sensor', 'relatedProjects', 'relatedVideos'));
    }
}