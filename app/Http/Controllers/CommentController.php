<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($postId)
    {
        return Comment::where('post_id', $postId)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        Comment::create([
            'content' => $request->content,
            'post_id' => $request->post_id,
            'user_id' => auth()->check() ? auth()->id() : null,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return;
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted.');
    }
}
