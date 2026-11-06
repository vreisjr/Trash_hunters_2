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
        'post_type' => 'marketplace',
    ]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee($post->content)
        ->assertSee($user->name);
});

test('feed and marketplace posts stay in their own areas', function () {
    $user = User::factory()->create();
    $feedPost = Post::factory()->for($user)->create([
        'content' => 'Publicação exclusiva do feed.',
        'post_type' => 'feed',
    ]);
    $marketplacePost = Post::factory()->for($user)->create([
        'content' => 'Oferta exclusiva de venda.',
        'post_type' => 'marketplace',
        'price_type' => 'paid',
        'price' => 25,
    ]);

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertSee($feedPost->content)
        ->assertDontSee($marketplacePost->content)
        ->assertDontSee('Para doação')
        ->assertDontSee('Pede-se uma contribuição');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee($marketplacePost->content)
        ->assertDontSee($feedPost->content);
});

test('users can collect a free item from the dashboard and see it in their history', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();
    $firstPost = Post::factory()->for($firstUser)->create([
        'content' => 'Latas para reutilização.',
        'post_type' => 'marketplace',
        'price_type' => 'free',
    ]);
    $secondPost = Post::factory()->for($secondUser)->create([
        'content' => 'Garrafas de vidro para reutilização.',
        'post_type' => 'marketplace',
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

test('users can save posts and view their saved post history', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $post = Post::factory()->for($author)->create([
        'content' => 'Post salvo para consultar depois.',
        'post_type' => 'marketplace',
    ]);

    $this->actingAs($user)
        ->postJson(route('posts.salvar', $post))
        ->assertOk()
        ->assertJson(['salvo' => true]);

    $this->assertDatabaseHas('post_salvos', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('recovery-favorite is-saved')
        ->assertSee(route('posts.salvar', $post), false);

    $this->actingAs($user)
        ->get(route('posts.saved'))
        ->assertOk()
        ->assertSee('Post salvo para consultar depois.')
        ->assertSee('Posts salvos');

    $this->actingAs($user)
        ->postJson(route('posts.salvar', $post))
        ->assertOk()
        ->assertJson(['salvo' => false]);

    $this->assertDatabaseMissing('post_salvos', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('privileged users can edit and delete any post while regular users can only edit their own post once', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $premium = User::factory()->create(['role' => 'premium']);
    $regular = User::factory()->create();
    $regularPost = Post::factory()->for($regular)->create(['content' => 'Post do usuário comum.']);
    $ownPost = Post::factory()->for($regular)->create(['content' => 'Post próprio.']);
    $adminPost = Post::factory()->for($admin)->create(['content' => 'Post do administrador.']);

    $this->actingAs($admin)
        ->get(route('posts.edit', $regularPost))
        ->assertOk();

    $this->actingAs($premium)
        ->put(route('posts.update', $regularPost), ['content' => 'Editado pelo premium.'])
        ->assertRedirect(route('posts.index'));

    $regularPost->refresh();

    $this->actingAs($premium)
        ->put(route('posts.update', $regularPost), ['content' => 'Editado novamente pelo premium.'])
        ->assertRedirect(route('posts.index'));

    $this->actingAs($regular)
        ->put(route('posts.update', $ownPost), ['content' => 'Editado pelo próprio autor.'])
        ->assertRedirect(route('posts.index'));

    $this->actingAs($regular)
        ->put(route('posts.update', $ownPost), ['content' => 'Segunda edição indevida.'])
        ->assertRedirect(route('posts.index'))
        ->assertSessionHas('error', 'Esta publicação já foi editada e não pode ser alterada novamente.');

    $this->actingAs($regular)
        ->put(route('posts.update', $adminPost), ['content' => 'Alteração indevida.'])
        ->assertForbidden();

    $this->actingAs($regular)
        ->delete(route('posts.destroy', $regularPost))
        ->assertForbidden();

    $this->actingAs($premium)
        ->delete(route('posts.destroy', $adminPost))
        ->assertRedirect(route('posts.index'));
});
