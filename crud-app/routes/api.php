<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostApiController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\CommentApiController;
use App\Http\Controllers\AuthController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('users', [UserApiController::class, 'index']);

    Route::get('/posts', [PostApiController::class, 'index']);
    Route::post('posts', [PostApiController::class, 'store']);
    Route::get('/posts/{id}', [PostApiController::class, 'show']);
    Route::put('/posts/{id}/update', [PostApiController::class, 'update']);
    Route::delete('/posts/{id}/delete', [PostApiController::class, 'destroy']);

    Route::get('/posts/{postId}/comments', [CommentApiController::class, 'index']); // Get comments for a post
    Route::post('/posts/{postId}/comments', [CommentApiController::class, 'store']); // Create a new comment
    Route::get('/posts/{postId}/comments/{commentId}', [CommentApiController::class, 'show']); // Show a comment
    Route::post('/posts/{postId}/comments/{commentId}/update', [CommentApiController::class, 'update']); // Update a comment
    Route::delete('/posts/{postId}/comments/{commentId}/delete', [CommentApiController::class, 'destroy']); // Delete a comment
});


