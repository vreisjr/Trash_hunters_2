<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Comentario;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class FeedSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que as categorias existam
        $this->call(CategoriaSeeder::class);

        $categorias = Categoria::all();
        $usuarios = User::factory()->count(5)->create();

        $publicacoes = [
            'Hoje fizemos um mutirão de limpeza na praia e recolhemos mais de 30 kg de plástico!',
            'Dica: lavem as embalagens antes de separar para reciclagem, isso facilita muito o trabalho da coleta.',
            'Alguém sabe onde fica o ponto de coleta de eletrônicos mais próximo?',
            'Comecei a fazer compostagem em casa e já reduzi bastante o lixo orgânico.',
            'Encontrei esse terreno cheio de entulho no bairro. Vamos nos organizar para limpar?',
            'Consegui transformar garrafas PET em vasos para as plantas, ficou lindo!',
            'Lembrete: a coleta seletiva passa amanhã de manhã na minha rua.',
            'Quantas latinhas vocês juntam por mês? Aqui em casa são umas 40.',
        ];

        $comentarios = [
            'Parabéns pela iniciativa!',
            'Ótima dica, vou colocar em prática.',
            'Também quero participar!',
            'Aqui no meu bairro também está assim.',
            'Muito bom, compartilhei com meus amigos.',
        ];

        foreach ($publicacoes as $texto) {
            $post = Post::factory()
                ->for($usuarios->random())
                ->create(['content' => $texto]);

            // Cada post recebe de 1 a 2 categorias aleatórias
            $post->categorias()->attach(
                $categorias->random(rand(1, 2))->pluck('id')
            );

            // De 1 a 3 comentários por post
            for ($i = 0; $i < rand(1, 3); $i++) {
                $comentario = Comentario::factory()->create([
                    'post_id' => $post->id,
                    'user_id' => $usuarios->random()->id,
                    'texto' => fake()->randomElement($comentarios),
                ]);

                // Metade dos comentários recebe uma resposta do autor do post
                if (rand(0, 1)) {
                    Comentario::factory()->resposta($comentario)->create([
                        'user_id' => $post->user_id,
                        'texto' => 'Obrigado! Fico feliz que gostou.',
                    ]);
                }
            }
        }
    }
}
