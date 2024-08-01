<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a given post or task.
     *
     * @param string $type
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        // Check if the model exists and fetch comments accordingly
        $post = Post::find($id);
        if ($post) {
            $comments = Comment::where('commentable_type', Post::class)
                ->where('commentable_id', $id)
                ->get();
        } else {
            $task = Task::find($id);
            if ($task) {
                $comments = Comment::where('commentable_type', Task::class)
                    ->where('commentable_id', $id)
                    ->get();
            } else {
                return response()->json(['error' => 'Resource not found'], 404);
            }
        }

        return response()->json($comments);
    }

    /**
     * Store a newly created comment for a given post or task.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        // Determine the model and store the comment
        $post = Post::find($id);
        if ($post) {
            $commentable_type = Post::class;
        } else {
            $task = Task::find($id);
            if ($task) {
                $commentable_type = Task::class;
            } else {
                return response()->json(['error' => 'Resource not found'], 404);
            }
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->commentable_id = $id;
        $comment->commentable_type = $commentable_type;
        $comment->save();

        return response()->json($comment, 201);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param int $commentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $comment->delete();

        return response()->json(null, 204);
    }
}
