<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\State;
use App\Models\MediaUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $posts = Post::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            })
            ->paginate(9);
        $totalPosts = Post::count();
        $usersWithPostCount = User::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'user' => Auth::user(),
            'usersWithPostCount' => $usersWithPostCount,
            'totalPosts' => $totalPosts,
        ]);
    }


    public function create()
    {
        $states = State::all();
        $tags = Tag::all();

        return Inertia::render('Posts/Create', [
            'states' => $states,
            'allTags' => $tags,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url_slug' => 'required|string|max:255|unique:posts,url_slug',
            'meta_description' => 'nullable|string|max:255',
            'state_id' => 'required',
            'status' => 'required|in:hidden,public',

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
        }

        $post = Post::with(['tags', 'mediaUploads'])->where('url_slug', $post->url_slug)->firstOrFail();

        return redirect()->route('posts.show', ['url_slug' => $post->url_slug]);
    }

    public function show($url_slug)
    {
        $post = Post::with(['tags', 'mediaUploads', 'comments', 'comments.user'])->where('url_slug', $url_slug)->firstOrFail();

        return inertia('Posts/Show', [
            'post' => $post,
        ]);
    }

    public function edit($id)
    {
        $post = Post::with('mediaUploads', 'tags')->find($id);
        $states = State::all();
        $allTags = Tag::all();

        return Inertia::render('Posts/Edit', [
            'post' => $post,
            'states' => $states,
            'allTags' => $allTags
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('id', $id)->firstOrFail();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url_slug' => 'required|string|max:255|unique:posts,url_slug,' . $id,
            'meta_description' => 'nullable|string|max:255',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4|max:20480',
            'state_id' => 'required',
            'status' => 'required|in:hidden,public'
        ]);

        $post->update($validatedData);

        $tags = $request->input('tags');
        $tagIds = [];
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $tagModel = Tag::firstOrCreate(['name' => $tag]);
                $tagIds[] = $tagModel->id;
            }
        }

        $post->tags()->sync($tagIds);

        if ($request->hasFile('media')) {
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

        return Inertia::location(route('posts.show', $post->url_slug));
    }


    public function destroy($id)
    {
        $post = Post::where('id', $id)->firstOrFail();
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    public function generatePreviewLink($postId)
    {
        $post = Post::findOrFail($postId);
        $expiresAt = Carbon::now()->addMinutes(5);
        $previewLink = URL::temporarySignedRoute('posts.preview', $expiresAt, ['post' => $post]);
        Log::info('Generated Preview Link:', ['link' => $previewLink]);

        return response()->json(['preview_link' => $previewLink]);
    }

    public function preview(Post $post)
    {
        $post = Post::with(['tags', 'mediaUploads', 'comments', 'comments.user'])->where('id', $post->id)->first();

        return inertia('Posts/Preview', [
            'post' => $post,
        ]);
    }

    public function deleteMedia($mediaId)
    {
        $media = MediaUploads::find($mediaId);

        if (Storage::exists($media->file_path)) {
            Storage::delete($media->file_path);
        }

        $media->delete();

        return response()->json(['message' => 'Media deleted successfully']);
    }
}
