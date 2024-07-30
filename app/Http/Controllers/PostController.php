<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        return Post::withCount('comments')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create($request->all());
        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        return $post->load('comments');
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($request->all());
        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 204);
    }


    public function viewComments($postId) {
        $post = Post::findOrFail($postId);
        $comments = $post->comments;

        return view('commentsView', compact('post', 'comments'));
    }

}
