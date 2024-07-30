<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post, Task $task)
    {
        $postComments = $post->comments()->get();
        $taskComments = $task->comments()->get();

        //combine the both comments
        $mergedComments = $postComments->merge($taskComments);

        return $mergedComments;
    }


    public function store(Request $request, Post $post, Task $task)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->post_id = $post->id;
        $comment->task_id = $task->id;
        $comment->save();

        return response()->json($comment, 201);
    }
}

