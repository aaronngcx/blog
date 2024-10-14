<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserProfileController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
// Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show'); 
Route::get('/posts/{url_slug}', [PostController::class, 'show'])->name('posts.show');

Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

Route::get('/user/profile', [UserProfileController::class, 'show'])->name('profile.show');
Route::put('/user/profile', [UserProfileController::class, 'update'])->name('user-profile-information.update');
Route::put('/user/profile-information', [UserProfileController::class, 'update'])->name('user-profile-information.update');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
});
