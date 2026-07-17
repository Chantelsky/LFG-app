<?php

use App\Models\Game;
use App\Models\User;

test('a logged-in user can create a post with an existing igdb game', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/posts', [
        'game_name' => 'Valorant',
        'igdb_id' => 126459,
        'title' => 'Need duo for ranked climb',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 5,
    ]);

    $response->assertRedirect('/browse');

    $this->assertDatabaseHas('posts', [
        'title' => 'Need duo for ranked climb',
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('games', [
        'name' => 'Valorant',
        'igdb_id' => 126459,
    ]);
});

test('a logged-in user can create a post with a custom game not on igdb', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/posts', [
        'game_name' => 'Mecha Chameleon',
        'igdb_id' => null,
        'title' => 'Need a squad',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 4,
    ]);

    $response->assertRedirect('/browse');

    $this->assertDatabaseHas('games', [
        'name' => 'Mecha Chameleon',
        'is_custom' => true,
    ]);
});

test('selecting the same igdb game twice reuses the existing game record', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/posts', [
        'game_name' => 'Valorant',
        'igdb_id' => 126459,
        'title' => 'First post',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 5,
    ]);

    $this->actingAs($user)->post('/posts', [
        'game_name' => 'Valorant',
        'igdb_id' => 126459,
        'title' => 'Second post',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 5,
    ]);

    expect(Game::where('igdb_id', 126459)->count())->toBe(1);
});

test('a guest cannot submit a post', function () {
    $response = $this->post('/posts', [
        'game_name' => 'Valorant',
        'igdb_id' => 126459,
        'title' => 'Sneaky guest post',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 5,
    ]);

    $response->assertRedirect('/login');

    $this->assertDatabaseMissing('posts', [
        'title' => 'Sneaky guest post',
    ]);
});

test('creating a post requires a title', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/posts', [
        'game_name' => 'Valorant',
        'igdb_id' => 126459,
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 5,
    ]);

    $response->assertSessionHasErrors('title');
});

test('the post is assigned to the logged-in user, not a submitted value', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)->post('/posts', [
        'game_name' => 'Valorant',
        'igdb_id' => 126459,
        'title' => 'Trying to spoof user_id',
        'user_id' => $otherUser->id,
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 5,
    ]);

    $this->assertDatabaseHas('posts', [
        'title' => 'Trying to spoof user_id',
        'user_id' => $user->id,
    ]);
});
