<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $suggestionsCount = $user->suggestions()->count();
        $savedProjectsCount = $user->savedProjects()->count();
        $myClass = $user->classes()->first();
        $recentSuggestions = $user->suggestions()->latest()->take(5)->get();
        
        return view('user.dashboard', compact('user', 'suggestionsCount', 'savedProjectsCount', 'myClass', 'recentSuggestions'));
    }
}