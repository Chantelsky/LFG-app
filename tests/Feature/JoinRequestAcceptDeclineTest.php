<?php

use App\Models\Game;
use App\Models\JoinRequest;
use App\Models\Post;
use App\Models\User;

function createPostWithRequest(int $partySize = 5): array
{
    $host = User::factory()->create();
    $requester = User::factory()->create();
    $game = Game::factory()->create();

    $post = Post::create([
        'game_id' => $game->id,
        'user_id' => $host->id,
        'title' => 'Need duo',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => $partySize,
        'current_members' => 1,
    ]);

    $joinRequest = JoinRequest::create([
        'post_id' => $post->id,
        'user_id' => $requester->id,
        'status' => 'pending',
    ]);

    return compact('host', 'requester', 'post', 'joinRequest');
}

test('the host can accept a join request', function () {
    ['host' => $host, 'post' => $post, 'joinRequest' => $joinRequest] = createPostWithRequest();

    $response = $this->actingAs($host)->patch("/join-requests/{$joinRequest->id}/accept");

    $response->assertRedirect();

    $this->assertDatabaseHas('join_requests', [
        'id' => $joinRequest->id,
        'status' => 'accepted',
    ]);

    expect($post->fresh()->current_members)->toBe(2);
});

test('accepting a request fills the post when party size is reached', function () {
    ['host' => $host, 'post' => $post, 'joinRequest' => $joinRequest] = createPostWithRequest(partySize: 2);

    $this->actingAs($host)->patch("/join-requests/{$joinRequest->id}/accept");

    expect($post->fresh()->status)->toBe('filled');
});

test('the host can decline a join request', function () {
    ['host' => $host, 'joinRequest' => $joinRequest] = createPostWithRequest();

    $response = $this->actingAs($host)->patch("/join-requests/{$joinRequest->id}/decline");

    $response->assertRedirect();

    $this->assertDatabaseHas('join_requests', [
        'id' => $joinRequest->id,
        'status' => 'declined',
    ]);
});

test('a non-host cannot accept a join request', function () {
    ['requester' => $requester, 'joinRequest' => $joinRequest] = createPostWithRequest();

    $response = $this->actingAs($requester)->patch("/join-requests/{$joinRequest->id}/accept");

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Only the host can accept requests.');
});

test('an already-handled request cannot be accepted again', function () {
    ['host' => $host, 'joinRequest' => $joinRequest] = createPostWithRequest();

    $joinRequest->update(['status' => 'accepted']);

    $response = $this->actingAs($host)->patch("/join-requests/{$joinRequest->id}/accept");

    $response->assertRedirect();
    $response->assertSessionHas('error', 'This request has already been handled.');
});
