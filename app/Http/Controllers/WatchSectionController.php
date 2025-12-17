<?php

namespace App\Http\Controllers;

use App\Models\WatchSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchSectionController extends Controller
{
    // Create new video
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized - no valid token provided'], 401);
        }

        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Only admins can create watch-section videos.'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'video_link'  => 'required|url',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('watch_images', 'public')
            : null;

        $watch = WatchSection::create([
            'user_id'    => Auth::id(),
            'title'      => $validated['title'],
            'video_link' => $validated['video_link'],
            'description'=> $validated['description'] ?? null,
            'image'      => $imagePath,
        ]);

        return response()->json(['status' => true, 'message' => 'Video created successfully', 'data' => $watch], 201);
    }

    // Get all videos
    public function index()
    {
        $videos = WatchSection::with('user:id,name,email')->latest()->get();
        return response()->json(['status' => true, 'message' => 'All videos retrieved', 'data' => $videos]);
    }

    // Update video (Admin only)
    public function update(Request $request, $id)
    {
        if (!Auth::check()) return response()->json(['message' => 'Unauthorized'], 401);
        if (Auth::user()->role !== 'admin') return response()->json(['message' => 'Only admins can update videos'], 403);

        $video = WatchSection::find($id);
        if (!$video) return response()->json(['message' => 'Video not found'], 404);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:100',
            'video_link'  => 'sometimes|url',
            'description' => 'sometimes|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [];
        if ($request->filled('title'))        $data['title'] = $request->input('title');
        if ($request->filled('video_link'))   $data['video_link'] = $request->input('video_link');
        if ($request->filled('description'))  $data['description'] = $request->input('description');
        if ($request->hasFile('image'))       $data['image'] = $request->file('image')->store('watch_images', 'public');

        if (empty($data)) {
            return response()->json(['status' => false, 'message' => 'No fields provided to update'], 400);
        }

        $data['updated_at'] = now();
        \DB::table('watch_sections')->where('id', $id)->update($data);

        $video = WatchSection::find($id);

        return response()->json([
            'status' => true,
            'message' => 'Video updated successfully',
            'data' => $video
        ], 200);
    }


    // Delete video (Admin only)
    public function destroy(Request $request, $id)
    {
        if (!Auth::check()) return response()->json(['message' => 'Unauthorized'], 401);
        if (Auth::user()->role !== 'admin') return response()->json(['message' => 'Only admins can delete videos'], 403);

        $video = WatchSection::find($id);
        if (!$video) return response()->json(['message' => 'Video not found'], 404);

        $video->delete();
        return response()->json(['status' => true, 'message' => 'Video deleted successfully'], 200);
    }
}
