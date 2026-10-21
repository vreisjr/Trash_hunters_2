<?php

use App\Models\Post;
use App\Models\User;

test('moderators can delete regular user accounts and their posts', function () {
    $moderator = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);
    $post = Post::factory()->for($user)->create();

    $this->actingAs($moderator)
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('posts.index'));

    expect($user->fresh())->toBeNull()
        ->and($post->fresh())->toBeNull();
});

test('regular users cannot delete accounts', function () {
    $user = User::factory()->create(['role' => 'user']);
    $target = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->delete(route('users.destroy', $target))
        ->assertForbidden();

    expect($target->fresh())->not->toBeNull();
});

test('moderators cannot delete themselves or another moderator', function () {
    $moderator = User::factory()->create(['role' => 'admin']);
    $otherModerator = User::factory()->create(['role' => 'premium']);

    $this->actingAs($moderator)
        ->delete(route('users.destroy', $moderator))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('users.destroy', $otherModerator))
        ->assertForbidden();

    expect($moderator->fresh())->not->toBeNull()
        ->and($otherModerator->fresh())->not->toBeNull();
});
