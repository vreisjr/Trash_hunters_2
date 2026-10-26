<?php

use App\Models\Coleta;
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

test('users can collect a free item from the dashboard and see it in their history', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();
    $firstPost = Post::factory()->for($firstUser)->create([
        'content' => 'Latas para reutilização.',
        'price_type' => 'free',
    ]);
    $secondPost = Post::factory()->for($secondUser)->create([
        'content' => 'Garrafas de vidro para reutilização.',
        'price_type' => 'paid',
        'price' => 20,
    ]);

    $firstUserDashboard = $this->actingAs($firstUser)
        ->get(route('dashboard'))
        ->assertOk();

    expect(substr_count($firstUserDashboard->getContent(), 'posts/'.$secondPost->id.'/coletar'))->toBe(1)
        ->and(substr_count($firstUserDashboard->getContent(), 'posts/'.$firstPost->id.'/coletar'))->toBe(0);

    $this->actingAs($firstUser)
        ->from(route('dashboard'))
        ->post(route('posts.coletar', $secondPost))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status', 'Item coletado! Ele já aparece no seu histórico de coletas.');

    $coleta = Coleta::query()->where('post_id', $secondPost->id)->first();

    expect($coleta)->not->toBeNull()
        ->and($coleta->coletor_id)->toBe($firstUser->id)
        ->and($coleta->doador_id)->toBe($secondUser->id);

    $this->actingAs($firstUser)
        ->get(route('historico.coletas'))
        ->assertOk()
        ->assertSee('Garrafas de vidro para reutilização.');

    $secondUserDashboard = $this->actingAs($secondUser)
        ->get(route('dashboard'))
        ->assertOk();

    expect(substr_count($secondUserDashboard->getContent(), 'posts/'.$firstPost->id.'/coletar'))->toBe(1)
        ->and(substr_count($secondUserDashboard->getContent(), 'posts/'.$secondPost->id.'/coletar'))->toBe(0);
});

test('authenticated users can visit the collection points page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('collection-points.index'))
        ->assertOk()
        ->assertSee('Pontos de coleta perto de você')
        ->assertSee('Igarassu')
        ->assertSee('Ver no mapa');
});
