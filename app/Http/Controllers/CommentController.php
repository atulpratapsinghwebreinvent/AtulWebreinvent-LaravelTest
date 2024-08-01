<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index($id)
    {

        $task = Task::find($id);
        $post = Post::find($id);


        if ($task) {
            $comments = Comment::where('commentable_type', Task::class)
                ->where('commentable_id', $id)
                ->get();
            return response()->json($comments);
        }
        if ($post) {
            $comments = Comment::where('commentable_type', Post::class)
                ->where('commentable_id', $id)
                ->get();
            return response()->json($comments);
        }

        return response()->json(['error' => 'Task not found'], 404);
    }



    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        // Determine the model and store the comment
        $commentable_type = null;
        $post = Post::find($id);
        $task = Task::find($id);
        if ($post) {
            $commentable_type = Post::class;


        }
        if($task)
        {

            $commentable_type = Task::class;
        }


        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->commentable_id = $id;
        $comment->commentable_type = $commentable_type;
        $comment->save();

        return response()->json($comment, 201);
    }


    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $comment->delete();

        return response()->json(null, 204);
    }
}
