<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Papel' => '#2F6FED',
            'Plástico' => '#E53935',
            'Vidro' => '#2E9E4F',
            'Metal' => '#D4A017',
            'Orgânico' => '#7A4A2B',
            'Eletrônico' => '#FF7A00',
            'Outros' => '#6B6B6B',
        ];

        foreach ($categorias as $nome => $cor) {
            Categoria::updateOrCreate(['nome' => $nome], ['cor' => $cor]);
        }
    }
}
