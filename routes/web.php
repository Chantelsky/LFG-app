<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\JoinRequestController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return redirect('/browse');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/browse', [PostController::class, 'index'])->name('posts.index');

    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my-posts');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts/{post}/join-requests', [JoinRequestController::class, 'store'])->name('join-requests.store');
    Route::delete('/posts/{post}/members/{partyMember}', [JoinRequestController::class, 'removeMember'])->name('party-members.remove');
    Route::delete('/posts/{post}/leave', [JoinRequestController::class, 'leave'])->name('posts.leave');

    Route::get('/games/search', [GameController::class, 'search'])->name('games.search');

    Route::patch('/join-requests/{joinRequest}/accept', [JoinRequestController::class, 'accept'])->name('join-requests.accept');
    Route::patch('/join-requests/{joinRequest}/decline', [JoinRequestController::class, 'decline'])->name('join-requests.decline');

    Route::post('/posts/{post}/messages', [MessageController::class, 'store'])->name('messages.store');
});

require __DIR__.'/auth.php';
