<?php

use App\Models\Post;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('post.{postId}', function ($user, $postId) {
    $post = Post::find($postId);

    if (! $post) {
        return false;
    }

    return $post->partyMembers()->where('user_id', $user->id)->exists();
});
