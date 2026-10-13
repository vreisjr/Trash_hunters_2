<?php

use App\Enums\StatusDoacao;
use App\Models\Doacao;
use App\Models\User;

test('usuário autenticado vê o histórico de doações', function () {
    $user = User::factory()->create();
    $doacao = Doacao::factory()->for($user, 'doador')->create([
        'nome' => 'Notebook em bom estado',
        'status' => StatusDoacao::Coletado,
    ]);

    $this->actingAs($user)
        ->get(route('historico.doacoes'))
        ->assertOk()
        ->assertSee($doacao->nome)
        ->assertSee('Doações');
});

test('usuário autenticado vê o histórico de coletas', function () {
    $user = User::factory()->create();
    $coletor = User::factory()->create();
    $doacao = Doacao::factory()->for($user, 'doador')->create([
        'coletor_id' => $coletor->id,
        'status' => StatusDoacao::Coletado,
        'coletado_em' => now(),
    ]);

    $this->actingAs($coletor)
        ->get(route('historico.coletas'))
        ->assertOk()
        ->assertSee($doacao->nome)
        ->assertSee('Coletas');
});
