<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    /**
     * Create a new blog post (Admin only)
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized - no valid token provided'], 401);
        }

        if ($request->user()->role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Only admins can create blog posts'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:30',
            'body'  => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('blog_images', 'public')
            : null;

        $blog = Blog::create([
            'user_id' => Auth::id(),
            'title'   => $validated['title'],
            'body'    => $validated['body'],
            'image'   => $imagePath,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Blog post created successfully',
            'blog'    => $blog
        ], 201);
    }

    /**
     * Get all blogs
     */
    public function index()
    {
        $blogs = Blog::with('user:id,name,email')->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'All blogs retrieved successfully',
            'data'    => $blogs
        ]);
    }

    /**
     * Update a blog post (Admin only)
     */
    public function update(Request $request, $id)
    {
        // ✅ Step 1: Check authentication
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized - no valid token provided'], 401);
        }

        // ✅ Step 2: Check admin role
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Only admins can update blog posts'], 403);
        }

        // ✅ Step 3: Find blog
        $blog = Blog::find($id);
            if (!$blog) {
                return response()->json(['message' => 'Blog not found'], 404);
        }

        // ✅ Step 4: Validate request data
        $validated = $request->validate([
            'title' => 'nullable|string|max:30',
            'body'  => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // ✅ Step 5: Update fields dynamically
        $data =[];
        if ($request->filled('title')) {
            $data['title'] = $request->input('title');
        }

        if ($request->filled('body')) {
            $data['body'] = $request->input('body');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog_images', 'public');
        }
        
        if (empty($data)) {
            return response()->json([
                'status' => false,
                'message' => 'No fields provided to update'
                ], 400);
        }
        
        $data['updated_at'] = now();
        DB::table('blogs')->where('id', $id)->update($data);
        
        $blog = Blog::find($id);

        // ✅ Step 6: Save and confirm persistence
        /**if (!$blog->isDirty()) {
            // No changes were made
            return response()->json([
                'status' => false,
                'message' => 'No changes detected to update'
            ], 200);
        }*/

        // $blog->save();

        return response()->json([
            'status'  => true,
            'message' => 'Blog post updated successfully',
            'blog'    => $blog
        ], 200);
    }


    /**
     * Delete a blog post (Admin only)
     */
    public function destroy(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized - no valid token provided'], 401);
        }

        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Only admins can delete blog posts'], 403);
        }

        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $blog->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Blog post deleted successfully'
        ], 200);
    }
}
