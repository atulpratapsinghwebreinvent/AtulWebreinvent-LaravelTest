<?php



namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Retrieve all posts with the count of comments
    public function index()
    {
        return Post::withCount('comments')->get();
    }

    // Create a new post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create($request->only(['title', 'slug', 'content']));
        return response()->json($post, 201);
    }


    // Retrieve a single post with its comments
    public function show(Post $post)
    {
        return $post->load('comments');
    }

    // Update an existing post
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

    // Delete a post
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 204);
    }

    // View comments for a specific post
    public function viewComments($postId)
    {
        $post = Post::findOrFail($postId);
        $comments = $post->comments;

        return view('commentsView', compact('post', 'comments'));
    }

    // Store a new comment for a specific post
    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->commentable_id = $post->id;
        $comment->commentable_type = Post::class;
        $comment->save();

        return response()->json($comment, 201);
    }
}
