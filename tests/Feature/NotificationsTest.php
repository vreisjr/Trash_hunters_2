<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Str;

test('users can view and mark notifications as read', function () {
    $user = User::factory()->create();
    $user->notify(new SystemNotification('test', 'Você recebeu uma atualização.'));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Você recebeu uma atualização.');

    $notification = $user->fresh()->notifications()->first();

    $this->actingAs($user)
        ->post(route('notifications.mark-read', $notification->id))
        ->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('new posts notify other users', function () {
    $author = User::factory()->create();
    $recipient = User::factory()->create();

    $this->actingAs($author)
        ->post(route('posts.store'), [
            'content' => 'Nova publicação para reaproveitamento.',
            'post_type' => 'feed',
        ])
        ->assertRedirect(route('posts.index'));

    expect($recipient->fresh()->unreadNotifications)->toHaveCount(1)
        ->and($author->fresh()->notifications)->toBeEmpty();
});

test('post owners are notified about likes and comments', function () {
    $author = User::factory()->create();
    $interactor = User::factory()->create();
    $post = Post::factory()->for($author)->create(['post_type' => 'feed']);

    $this->actingAs($interactor)
        ->postJson(route('posts.curtir', $post))
        ->assertOk();

    $this->actingAs($interactor)
        ->postJson(route('comentarios.store', $post), ['texto' => 'Ótimo material!'])
        ->assertOk();

    $notifications = $author->fresh()->unreadNotifications;

    expect($notifications)->toHaveCount(2)
        ->and($notifications->pluck('data.message')->toArray())->toContain(
            $interactor->name.' curtiu sua postagem "'.Str::limit($post->content, 120).'".',
            ''.$interactor->name.' comentou "Ótimo material!" na sua postagem.',
        );
});
