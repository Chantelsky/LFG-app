<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('game', 'user')
            ->where('status', 'open')
            ->latest()
            ->get();

        return Inertia::render('Posts/Browse', [
            'posts' => $posts,
        ]);
    }

    public function create()
    {
        $games = Game::all();

        return Inertia::render('Posts/Create', [
            'games' => $games,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'title' => 'required|string|max:255',
            'skill_rank' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'comm_preference' => 'required|in:mic_required,mic_optional,text_only',
            'join_mode' => 'required|in:auto_accept,manual_review',
        ]);

        $validated['user_id'] = auth()->id();

        Post::create($validated);

        return redirect()->route('posts.index');
    }

    public function myPosts()
    {
        $posts = Post::with('game', 'joinRequests.user')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Posts/MyPosts', [
            'posts' => $posts,
        ]);
    }
}
