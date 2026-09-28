<?php

use App\Models\Comentario;
use App\Models\Post;
use App\Models\User;

test('authenticated users can reply to a comment on the post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comentario = $post->comentarios()->create([
        'user_id' => $user->id,
        'texto' => 'Comentário original',
    ]);

    $response = $this->actingAs($user)->postJson(
        route('comentarios.store', $post),
        [
            'texto' => 'Minha resposta',
            'parent_id' => $comentario->id,
        ]
    );

    $response
        ->assertOk()
        ->assertJsonPath('parent_id', $comentario->id)
        ->assertJsonPath('texto', 'Minha resposta');

    expect(Comentario::query()->where('parent_id', $comentario->id)->count())->toBe(1);
});

test('a reply cannot target a comment from another post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $otherPost = Post::factory()->create();
    $comentario = $otherPost->comentarios()->create([
        'user_id' => $user->id,
        'texto' => 'Comentário de outro post',
    ]);

    $response = $this->actingAs($user)->postJson(
        route('comentarios.store', $post),
        [
            'texto' => 'Resposta inválida',
            'parent_id' => $comentario->id,
        ]
    );

    $response->assertUnprocessable();
    expect(Comentario::query()->where('texto', 'Resposta inválida')->exists())->toBeFalse();
});
