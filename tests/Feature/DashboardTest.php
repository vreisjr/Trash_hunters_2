<?php

use App\Models\Post;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard with posts linked to their authors', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create([
        'content' => 'Material disponível para reaproveitamento.',
    ]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee($post->content)
        ->assertSee($user->name);
});

test('authenticated users can visit the collection points page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('collection-points.index'))
        ->assertOk()
        ->assertSee('Pontos de coleta perto de você')
        ->assertSee('Igarassu')
        ->assertSee('Ver no mapa');
});
