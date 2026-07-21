<?php

use App\Models\Game;
use App\Models\Post;
use App\Models\User;

function createPostWithMember(): array
{
    $host = User::factory()->create();
    $member = User::factory()->create();
    $outsider = User::factory()->create();
    $game = Game::factory()->create();

    $post = Post::create([
        'game_id' => $game->id,
        'user_id' => $host->id,
        'title' => 'Need duo',
        'comm_preference' => 'mic_optional',
        'join_mode' => 'manual_review',
        'party_size' => 5,
        'current_members' => 2,
    ]);

    $post->partyMembers()->create(['user_id' => $host->id, 'is_host' => true]);
    $post->partyMembers()->create(['user_id' => $member->id, 'is_host' => false]);

    return compact('host', 'member', 'outsider', 'post');
}

test('a party member can send a message', function () {
    ['member' => $member, 'post' => $post] = createPostWithMember();

    $response = $this->actingAs($member)->post("/posts/{$post->uuid}/messages", [
        'body' => 'gg lets go',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('messages', [
        'post_id' => $post->id,
        'user_id' => $member->id,
        'body' => 'gg lets go',
    ]);
});

test('the host can send a message', function () {
    ['host' => $host, 'post' => $post] = createPostWithMember();

    $response = $this->actingAs($host)->post("/posts/{$post->uuid}/messages", [
        'body' => 'ready when you are',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('messages', [
        'post_id' => $post->id,
        'user_id' => $host->id,
    ]);
});

test('a non-member cannot send a message', function () {
    ['outsider' => $outsider, 'post' => $post] = createPostWithMember();

    $response = $this->actingAs($outsider)->post("/posts/{$post->uuid}/messages", [
        'body' => 'sneaky message',
    ]);

    $response->assertSessionHas('error', 'Only party members can send messages.');

    $this->assertDatabaseMissing('messages', [
        'post_id' => $post->id,
        'body' => 'sneaky message',
    ]);
});

test('a guest cannot send a message', function () {
    ['post' => $post] = createPostWithMember();

    $response = $this->post("/posts/{$post->id}/messages", [
        'body' => 'guest message',
    ]);

    $response->assertRedirect('/login');

    $this->assertDatabaseMissing('messages', [
        'post_id' => $post->id,
    ]);
});

test('sending a message requires a body', function () {
    ['member' => $member, 'post' => $post] = createPostWithMember();

    $response = $this->actingAs($member)->post("/posts/{$post->uuid}/messages", []);

    $response->assertSessionHasErrors('body');
});

test('a non-member cannot view message history when viewing a post', function () {
    ['host' => $host, 'outsider' => $outsider, 'post' => $post] = createPostWithMember();

    $post->messages()->create(['user_id' => $host->id, 'body' => 'private squad chat']);

    $response = $this->actingAs($outsider)->get("/posts/{$post->uuid}");

    $response->assertInertia(fn ($page) => $page->where('post.messages', [])
    );
});

test('a party member can view message history when viewing a post', function () {
    ['host' => $host, 'member' => $member, 'post' => $post] = createPostWithMember();

    $post->messages()->create(['user_id' => $host->id, 'body' => 'private squad chat']);

    $response = $this->actingAs($member)->get("/posts/{$post->uuid}");

    $response->assertInertia(fn ($page) => $page->has('post.messages', 1)
    );
});
