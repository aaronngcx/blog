<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::all();
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url_slug' => 'required|string|max:255|unique:posts',
            'meta_description' => 'nullable|string|max:255',
        ]);

        return Post::create(array_merge($validated, ['user_id' => $request->user()->id]));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url_slug' => 'required|string|max:255|unique:posts,url_slug,' . $post->id,
            'meta_description' => 'nullable|string|max:255',
        ]);

        $post->update($validated);

        return $post;
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 204);
    }
}
