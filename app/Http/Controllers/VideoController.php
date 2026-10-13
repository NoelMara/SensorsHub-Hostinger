<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::with('sensor')->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $videos = $query->orderByDesc('is_featured')->latest()->paginate(12)->appends($request->all());

        return view('videos.index', compact('videos'));
    }
}