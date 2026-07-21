<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Post;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $isMember = $post->partyMembers()->where('user_id', auth()->id())->exists();

        if (! $isMember) {
            return back()->with('error', 'Only party members can send messages.');
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = $post->messages()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return back();
    }
}
