<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserProfileController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/posts', [PostController::class, 'index'])->name('posts.index'); // To list posts
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create'); // To create a new post
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show'); // To show a single post
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update'); // Update route
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy'); // Delete route

Route::get('/user/profile', [UserProfileController::class, 'show'])->name('profile.show');
Route::put('/user/profile', [UserProfileController::class, 'update'])->name('user-profile-information.update');
Route::put('/user/profile-information', [UserProfileController::class, 'update'])->name('user-profile-information.update');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    
});
