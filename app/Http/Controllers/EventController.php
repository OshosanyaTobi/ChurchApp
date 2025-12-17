<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // Create new event
    public function store(Request $request)
    {
        if (!Auth::check()) return response()->json(['message' => 'Unauthorized'], 401);
        if (Auth::user()->role !== 'admin') return response()->json(['message' => 'Only admins can create events'], 403);

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'venue'      => 'required|string|max:150',
            'event_date' => 'required|date',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('event_images', 'public')
            : null;

        $event = Event::create([
            'user_id'    => Auth::id(),
            'name'       => $validated['name'],
            'venue'      => $validated['venue'],
            'event_date' => $validated['event_date'],
            'image'      => $imagePath,
        ]);

        return response()->json(['message' => 'Event created successfully', 'data' => $event], 201);
    }

    // Get all events
    public function index()
    {
        $events = Event::with('user:id,name,email')->latest()->get();
        return response()->json(['status' => true, 'message' => 'All events retrieved successfully', 'data' => $events]);
    }

    // Update event
    public function update(Request $request, $id)
    {
        if (!Auth::check()) return response()->json(['message' => 'Unauthorized'], 401);
        if (Auth::user()->role !== 'admin') return response()->json(['message' => 'Only admins can update events'], 403);

        $event = Event::find($id);
        if (!$event) return response()->json(['message' => 'Event not found'], 404);

        $validated = $request->validate([
            'name'       => 'sometimes|string|max:100',
            'venue'      => 'sometimes|string|max:150',
            'event_date' => 'sometimes|date',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [];
        if ($request->filled('name'))        $data['name'] = $request->input('name');
        if ($request->filled('venue'))       $data['venue'] = $request->input('venue');
        if ($request->filled('event_date'))  $data['event_date'] = $request->input('event_date');
        if ($request->hasFile('image'))      $data['image'] = $request->file('image')->store('event_images', 'public');

        if (empty($data)) {
            return response()->json(['status' => false, 'message' => 'No fields provided to update'], 400);
        }

        $data['updated_at'] = now();
        \DB::table('events')->where('id', $id)->update($data);

        $event = Event::find($id);

        return response()->json([
            'status' => true,
            'message' => 'Event updated successfully',
            'data' => $event
        ], 200);
    }


    // Delete event
    public function destroy(Request $request, $id)
    {
        if (!Auth::check()) return response()->json(['message' => 'Unauthorized'], 401);
        if (Auth::user()->role !== 'admin') return response()->json(['message' => 'Only admins can delete events'], 403);

        $event = Event::find($id);
        if (!$event) return response()->json(['message' => 'Event not found'], 404);

        $event->delete();
        return response()->json(['status' => true, 'message' => 'Event deleted successfully'], 200);
    }
}
