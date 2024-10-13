<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url_slug' => 'required|string|max:255|unique:posts,url_slug',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $post = Post::create([
            'user_id' => Auth::user()->id,
            ...$validatedData
        ]);

        $tags = $request->input('tags');
        $tagIds = [];
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $tag = Tag::firstOrCreate(['name' => $tag]);
                $tagIds[] = $tag->id;
            }
        }

        $post->tags()->sync($tagIds);

        return Inertia::render('Posts/Show', [
            'post' => $post->load('tags')
        ]);
    }

    public function show($id)
    {
        $post = Post::with('tags')->findOrFail($id);

        return Inertia::render('Posts/Show', [
            'post' => $post,
            'canEdit' => Auth::user()->can('update', $post),
        ]);
    }

    public function edit($id)
    {
        $post = Post::find($id);

        return Inertia::render('Posts/Edit', ['post' => $post]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url_slug' => 'required|string|max:255|unique:posts,url_slug,' . $id,
            'meta_description' => 'nullable|string|max:255',
        ]);

        $post->update($validatedData);

        $tags = $request->input('tags');
        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => Str::slug($tagName)]);
            $tagIds[] = $tag->id;
        }
        $post->tags()->sync($tagIds);

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully!');
    }

    public function destroy($id)
    {
        $post = Post::where('id', $id)->firstOrFail();
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}
