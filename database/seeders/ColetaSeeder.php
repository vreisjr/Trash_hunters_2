<?php

namespace Database\Seeders;

use App\Models\Coleta;
use App\Models\User;
use Illuminate\Database\Seeder;

// Só para TESTAR a tela. Precisa de pelo menos 2 usuários cadastrados.
class ColetaSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = User::take(2)->get();
        if ($usuarios->count() < 2) {
            $this->command->warn('Cadastre pelo menos 2 usuários antes.');

            return;
        }
        [$a, $b] = [$usuarios[0], $usuarios[1]];

        for ($i = 1; $i <= 25; $i++) {
            // alterna: ora $a doa para $b, ora $b doa para $a
            Coleta::create([
                'doador_id' => $i % 2 ? $a->id : $b->id,
                'coletor_id' => $i % 2 ? $b->id : $a->id,
                'item' => "Item de teste #$i",
                'data_hora' => now()->subDays($i)->subMinutes($i * 7),
            ]);
        }
    }
}
