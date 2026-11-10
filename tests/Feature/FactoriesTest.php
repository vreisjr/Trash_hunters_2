<?php

use App\Models\Categoria;
use App\Models\Coleta;
use App\Models\Comentario;
use App\Models\Post;
use App\Models\User;

test('usuário admin é moderador e usuário comum não', function () {
    $admin = User::factory()->admin()->create();
    $comum = User::factory()->create();

    expect($admin->isModerator())->toBeTrue()
        ->and($comum->isModerator())->toBeFalse();
});

test('post pago do marketplace tem preço e tipo corretos', function () {
    $post = Post::factory()->paid(40)->create();

    expect($post->post_type)->toBe('marketplace')
        ->and($post->price_type)->toBe('paid')
        ->and($post->price)->toBe('40.00');
});

test('comentário pode ter respostas', function () {
    $pai = Comentario::factory()->create();
    Comentario::factory()->count(3)->resposta($pai)->create();

    expect($pai->filhos)->toHaveCount(3)
        ->and($pai->filhos->first()->post_id)->toBe($pai->post_id);
});

test('post pode ter várias categorias', function () {
    $post = Post::factory()
        ->hasAttached(Categoria::factory()->count(2), [], 'categorias')
        ->create();

    expect($post->categorias)->toHaveCount(2);
});

test('coleta liga um doador a um coletor', function () {
    $doador = User::factory()->create();
    $coletor = User::factory()->create();

    $coleta = Coleta::factory()->create([
        'doador_id' => $doador->id,
        'coletor_id' => $coletor->id,
    ]);

    expect($coleta->doador->is($doador))->toBeTrue()
        ->and($coleta->coletor->is($coletor))->toBeTrue();
});
