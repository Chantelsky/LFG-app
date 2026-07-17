<?php

use App\Models\Game;
use App\Models\Post;
use App\Models\User;

test('a user can request to join a post', function () {
    $host = User::factory()->create();
    $requester = User::factory()->create();
    $game = Game::factory()->create();

    $post = Post::create([
        'game_id' => $game->id,
        'user_id' => $host->id,
        'title' => 'Need duo',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
    ]);

    $response = $this->actingAs($requester)->post("/posts/{$post->id}/join-requests");

    $response->assertRedirect();

    $this->assertDatabaseHas('join_requests', [
        'post_id' => $post->id,
        'user_id' => $requester->id,
        'status' => 'pending',
    ]);
});

test('a user cannot request to join their own post', function () {
    $host = User::factory()->create();
    $game = Game::factory()->create();

    $post = Post::create([
        'game_id' => $game->id,
        'user_id' => $host->id,
        'title' => 'Need duo',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
    ]);

    $response = $this->actingAs($host)->post("/posts/{$post->id}/join-requests");

    $response->assertForbidden();
});

test('a user cannot request to join the same post twice', function () {
    $host = User::factory()->create();
    $requester = User::factory()->create();
    $game = Game::factory()->create();

    $post = Post::create([
        'game_id' => $game->id,
        'user_id' => $host->id,
        'title' => 'Need duo',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
    ]);

    $this->actingAs($requester)->post("/posts/{$post->id}/join-requests");
    $response = $this->actingAs($requester)->post("/posts/{$post->id}/join-requests");

    $response->assertStatus(409);
});

test('a user cannot request to join a closed post', function () {
    $host = User::factory()->create();
    $requester = User::factory()->create();
    $game = Game::factory()->create();

    $post = Post::create([
        'game_id' => $game->id,
        'user_id' => $host->id,
        'title' => 'Need duo',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'status' => 'closed',
    ]);

    $response = $this->actingAs($requester)->post("/posts/{$post->id}/join-requests");

    $response->assertForbidden();
});
