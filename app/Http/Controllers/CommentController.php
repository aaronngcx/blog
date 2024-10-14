<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'author' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'post_id' => $postId,
            'author' => $request->author,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.show', ['url_slug' => $comment->post->url_slug]);
    }

    public function index($postId)
    {
        return Comment::where('post_id', $postId)->get();
    }
}
