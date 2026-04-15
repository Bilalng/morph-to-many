<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    
    public function index()
    {
        $allPost = Post::with('user')->get();

        return response()->json([
            'posts' => $allPost,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            // 'user_id' => auth()->id(), doğrulama yapmıyoruz basit tutalım diye
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
        ], 201);
    }

    //Route Model Binding Yaptık
    public function show(Post $post)
    {
        $post->load(['user', 'comments' => function ($query){
            $query->whereDoesntHave('parent')
            ->with(['user', 'replies.user']);
        }]);
        
        return response()->json([
            'post' => $post,
        ]);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
