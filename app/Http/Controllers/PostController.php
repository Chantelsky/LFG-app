<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_name' => 'required|string|max:255',
            'igdb_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'skill_rank' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'comm_preference' => 'required|in:mic_required,mic_optional,text_only',
            'join_mode' => 'required|in:auto_accept,manual_review',
            'party_size' => 'required|integer|min:2|max:10',
        ]);

        $game = null;

        if ($validated['igdb_id']) {
            $game = Game::where('igdb_id', $validated['igdb_id'])->first();

            if (! $game) {
                $slug = Str::slug($validated['game_name']);
                $game = Game::where('slug', $slug)->first();

                if ($game) {
                    $game->update(['igdb_id' => $validated['igdb_id']]);
                } else {
                    $game = Game::create([
                        'name' => $validated['game_name'],
                        'slug' => $slug,
                        'igdb_id' => $validated['igdb_id'],
                    ]);
                }
            }
        } else {
            $game = Game::firstOrCreate(
                ['slug' => Str::slug($validated['game_name'])],
                ['name' => $validated['game_name'], 'is_custom' => true]
            );
        }

        $post = Post::create([
            'game_id' => $game->id,
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'skill_rank' => $validated['skill_rank'] ?? null,
            'region' => $validated['region'] ?? null,
            'timezone' => $validated['timezone'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'comm_preference' => $validated['comm_preference'],
            'join_mode' => $validated['join_mode'],
            'party_size' => $validated['party_size'],
            'current_members' => 1,
        ]);

        $post->partyMembers()->create([
            'user_id' => auth()->id(),
            'is_host' => true,
        ]);

        return redirect()->route('posts.index')->with('success', 'Lobby posted!');
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
