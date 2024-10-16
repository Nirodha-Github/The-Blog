<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::get();

        if($posts){
            return response()->json($posts);
        }
        else{
            return response()->json(['message' => 'No posts found'], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(), // Get authenticated user ID
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json($post, 200); // 200 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = Post::with('user', 'comments')->findOrFail($id);
            return response()->json($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], 404); // Return 404 Not Found
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Attempt to find the post by its ID
        $post = Post::find($id); // Use find instead of findOrFail

        // Check if the post exists
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404); // Return 404 if not found
        }

        // Check if the authenticated user is the owner of the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unuser_idized'], 403);
        }

        // Validate the incoming request
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        // Update the post with the validated data
        $post->update($request->only(['title', 'content']));

        // Return the updated post
        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Attempt to find the post by ID
        $post = Post::find($id); // Use find() instead of findOrFail()

        // Check if the post exists
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404); // Return 404 if not found
        }

        // Check if the authenticated user is the owner of the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unuser_idized'], 403);
        }

        // Delete the post
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
