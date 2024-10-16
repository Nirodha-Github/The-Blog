<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentApiController extends Controller
{
    /**
     * Display a listing of the comments for a specific post.
     */
    public function index($postId)
    {
        $comments = Comment::where('post_id', $postId)->with('user')->get(); // Eager load user
        return response()->json($comments);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        // Check if the post exists
        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(), // Get authenticated user ID
            'comment' => $request->comment,
        ]);

        return response()->json($comment, 201); // 201 Created
    }

    /**
     * Display the specified comment.
     */
    public function show($id)
    {
        $comment = Comment::with('user')->findOrFail($id); // Eager load user
        return response()->json($comment);
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, string $postId, string $commentId)
    {
        $comment = Comment::where('post_id', $postId)->with('user')->findOrFail($commentId);
        
        // Check if the authenticated user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'sometimes|required|string',
        ]);

        $comment->update($request->only('comment'));

        return response()->json($comment);
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);

        // Check if the authenticated user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
