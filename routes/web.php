<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserProfileController;

Route::get('/', [HomeController::class, 'index'])->name('/');

Route::get('/user/profile', [UserProfileController::class, 'show'])->name('profile.show');
Route::put('/user/profile', [UserProfileController::class, 'update'])->name('user-profile-information.update');
Route::put('/user/profile-information', [UserProfileController::class, 'update'])->name('user-profile-information.update');
Route::delete('/user/profile-photo', [UserProfileController::class, 'destroyProfilePhoto'])
    ->name('current-user-photo.destroy');

Route::post('/posts/{post}/generate-preview', [PostController::class, 'generatePreviewLink'])
    ->name('posts.generate-preview-link');

Route::get('/posts/preview/{post}', [PostController::class, 'preview'])
    ->name('posts.preview')
    ->middleware('signed');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

Route::get('/posts/{url_slug}', [PostController::class, 'show'])->name('posts.show');

Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comments/{comment}/update', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

Route::delete('/posts/media/{mediaId}', [PostController::class, 'deleteMedia'])->name('posts.delete-media');
