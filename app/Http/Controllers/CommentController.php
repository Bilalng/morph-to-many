<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{

    private $allowedModels = [
        'post'  => \App\Models\Post::class,
        'video' => \App\Models\Video::class,
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string|in:' . implode(',', array_keys($this->allowedModels)),
            'commentable_id'   => 'required|integer',
        ]);

        $modelClass = $this->allowedModels[$validated['commentable_type']];
        $model = $modelClass::findOrFail($validated['commentable_id']);

        $comments = $model->comments()
            ->whereDoesntHave('parent')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return response()->json([
            'comments' => $comments,
        ]);
    }
    public function show(Comment $comment)
    {
        $comment->load('user', 'replies.user');

        return response()->json([
            'comment' => $comment,
        ]);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string|in:' . implode(',', array_keys($this->allowedModels)),
            'commentable_id'   => 'required|integer',
            'content'         => 'required|string',
            'parent_id'       => 'nullable|integer|exists:comments,id',
        ]);

        $modelClass = $this->allowedModels[$validated['commentable_type']];
        $model = $modelClass::findOrFail($validated['commentable_id']);

        $newComment = $model->comments()->create([
            'content' => $validated['content'],
            //'user_id' => auth()->id(), Öğrenme Dosyası olduğu için auth yok
        ]);

        if (!empty($validated['parent_id'])) {
            $parentComment = \App\Models\Comment::findOrFail($validated['parent_id']);
            $parentComment->replies()->attach($newComment->id);
        }

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $newComment->load('user'),
        ], 201);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
