<?php

use App\Models\Game;
use App\Models\User;

test('a logged-in user can create a post', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    $response = $this->actingAs($user)->post('/posts', [
        'game_id' => $game->id,
        'title' => 'Need duo for ranked climb',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
    ]);

    $response->assertRedirect('/browse');

    $this->assertDatabaseHas('posts', [
        'title' => 'Need duo for ranked climb',
        'user_id' => $user->id,
    ]);
});

test('a guest cannot access the create post page', function () {
    $response = $this->get('/posts/create');

    $response->assertRedirect('/login');
});

test('a guest cannot submit a post', function () {
    $game = Game::factory()->create();

    $response = $this->post('/posts', [
        'game_id' => $game->id,
        'title' => 'Sneaky guest post',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
    ]);

    $response->assertRedirect('/login');

    $this->assertDatabaseMissing('posts', [
        'title' => 'Sneaky guest post',
    ]);
});

test('creating a post requires a title', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();

    $response = $this->actingAs($user)->post('/posts', [
        'game_id' => $game->id,
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
    ]);

    $response->assertSessionHasErrors('title');
});

test('the post is assigned to the logged-in user, not a submitted value', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $game = Game::factory()->create();

    $this->actingAs($user)->post('/posts', [
        'game_id' => $game->id,
        'title' => 'Trying to spoof user_id',
        'user_id' => $otherUser->id, // attempting to inject a different user
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
    ]);

    $this->assertDatabaseHas('posts', [
        'title' => 'Trying to spoof user_id',
        'user_id' => $user->id, // should be the actual logged-in user, not $otherUser
    ]);
});
