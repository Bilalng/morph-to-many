<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->get();
        return response()->json([
            'videos' => $videos,
        ]);
    }

    public function show(Video $video)
    {
        return response()->json([
            'video' => $video,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url'   => 'required|url',
        ]);

        $video = Video::create($validated);

        return response()->json([
            'message' => 'Video created successfully',
            'video'   => $video,
        ], 201);
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return response()->json([
            'message' => 'Video deleted successfully',
        ]);
    }
}
