<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use App\Models\Post;

class JoinRequestController extends Controller
{
    public function store(Post $post)
    {
        abort_if($post->user_id === auth()->id(), 403, 'You cannot request to join your own post.');

        abort_if($post->status !== 'open', 403, 'This lobby is no longer open.');

        $alreadyRequested = JoinRequest::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->exists();

        abort_if($alreadyRequested, 409, 'You have already requested to join this lobby.');

        JoinRequest::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->back();
    }

    public function accept(JoinRequest $joinRequest)
    {
        $post = $joinRequest->post;

        abort_if($post->user_id !== auth()->id(), 403, 'Only the host can accept requests.');

        abort_if($joinRequest->status !== 'pending', 409, 'This request has already been handled.');

        $joinRequest->update(['status' => 'accepted']);

        $post->increment('current_members');

        if ($post->current_members >= $post->party_size) {
            $post->update(['status' => 'filled']);
        }

        return redirect()->back();
    }

    public function decline(JoinRequest $joinRequest)
    {
        $post = $joinRequest->post;

        abort_if($post->user_id !== auth()->id(), 403, 'Only the host can decline requests.');

        abort_if($joinRequest->status !== 'pending', 409, 'This request has already been handled.');

        $joinRequest->update(['status' => 'declined']);

        return redirect()->back();
    }
}
