<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que as categorias existam
        $this->call(CategoriaSeeder::class);

        $categorias = Categoria::all();
        $usuarios = User::factory()->count(5)->create();

        $itens = [
            ['Garrafas PET limpas, cerca de 50 unidades', 'free', null],
            ['Caixas de papelão grandes, ótimas para mudança', 'free', null],
            ['Sacos de latinhas de alumínio amassadas', 'paid', 15.00],
            ['Monitor antigo funcionando, para reaproveitar peças', 'paid', 80.00],
            ['Vidros e potes de conserva higienizados', 'free', null],
            ['Fios e cabos de cobre sem capa', 'paid', 45.00],
            ['Restos de madeira de demolição', 'free', null],
            ['Celulares quebrados para reciclagem de componentes', 'paid', 30.00],
        ];

        foreach ($itens as [$texto, $tipoPreco, $preco]) {
            $post = Post::factory()
                ->for($usuarios->random())
                ->marketplace()
                ->create([
                    'content' => $texto,
                    'price_type' => $tipoPreco,
                    'price' => $preco,
                ]);

            // Cada post recebe de 1 a 2 categorias aleatórias
            $post->categorias()->attach(
                $categorias->random(rand(1, 2))->pluck('id')
            );
        }
    }
}
