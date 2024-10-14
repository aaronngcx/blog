<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        if ($request->hasFile('media')) {
            Log::info('Media files found.');

            foreach ($request->file('media') as $file) {
                Log::info('Processing file:', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);

                try {
                    $filePath = $file->store('media_uploads', 'public');
                    Log::info('File stored at: ' . $filePath);

                    $fileUrl = config('app.url') . Storage::url($filePath);
                    Log::info('Full Path at: ' . $fileUrl);

                    $post->mediaUploads()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $fileUrl,
                        'file_type' => $file->getClientMimeType(),
                    ]);
                    Log::info('Media record created successfully.');
                } catch (\Exception $e) {
                    Log::error('Error storing file: ' . $e->getMessage());
                }
            }
        } else {
            Log::info('No media files found in the request.');
        }

        $post = Post::with(['tags', 'mediaUploads'])->where('url_slug', $post->url_slug)->firstOrFail();

        return Inertia::render('Posts/Show', [
            'post' => $post->with('tags', 'mediaUploads')
        ]);
    }

    public function show($url_slug)
    {
        $post = Post::where('url_slug', $url_slug)->firstOrFail();

        return inertia('Posts/Show', [
            'post' => $post,
        ]);
    }

    public function edit($id)
    {
        $post = Post::with('mediaUploads', 'tags')->find($id);

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
            $tag = Tag::firstOrCreate(['name' => Str::slug($tagName)], []);

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
