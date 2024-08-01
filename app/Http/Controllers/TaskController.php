<?php

namespace App\Http\Controllers;


use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::withCount('comments')->get();
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
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',

        ]);


        $post = Task::create($request->only(['title', 'slug', 'content']));
        return response()->json($post, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
        return $task->load('comments');
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
    public function update(Request $request, Task $task)
    {
        //
        $request->validate([
           'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string|max:255'

        ]);

        $task->update($request->all());

        return response()->json($task, 205);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
        $task->delete();

        return response()->json(null, 205);
    }
    public function storeComment(Request $request, Task $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->commentable_id = $post->id;
        $comment->commentable_type = Task::class;
        $comment->save();

        return response()->json($comment, 201);
    }
}
