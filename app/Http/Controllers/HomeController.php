<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\Project;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Redirect based on role
            if ($user->isAdministrator()) {
                return redirect()->route('administrator.dashboard');
            }
            if ($user->isInstructor()) {
                return redirect()->route('instructor.dashboard');
            }
            return redirect()->route('dashboard.index');
        }
        
        // Public home data for guests only
        $featuredSensors = Sensor::where('is_active', true)->latest()->take(6)->get();
        
        // Show recent projects — fall back to featured if any exist
        $featuredProjects = Project::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();
        
        // If no featured projects, show recent ones
        if ($featuredProjects->isEmpty()) {
            $featuredProjects = Project::where('is_active', true)->latest()->take(4)->get();
        }
        
        $latestVideos = Video::where('is_active', true)->orderByDesc('is_featured')->latest()->take(4)->get();
        
        return view('home', compact('featuredSensors', 'featuredProjects', 'latestVideos'));
    }
}