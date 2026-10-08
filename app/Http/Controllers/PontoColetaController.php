<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PontoColetaController extends Controller
{
    public function index(): View
    {
        $pontos = [
            [
                'cidade' => 'Igarassu',
                'regiao' => 'Centro e bairros próximos',
                'descricao' => 'Encontre pontos que recebem embalagens, papel, plástico, vidro e metais separados e limpos.',
                'materiais' => ['Papel', 'Plástico', 'Vidro', 'Metal'],
                'cor' => '#2563eb',
                'latitude' => -7.8342,
                'longitude' => -34.9063,
            ],
            [
                'cidade' => 'Paulista',
                'regiao' => 'Centro, Janga e Maranguape',
                'descricao' => 'Consulte cooperativas e pontos de entrega para recicláveis domésticos, eletrônicos e óleo usado.',
                'materiais' => ['Plástico', 'Eletrônico', 'Metal', 'Outros'],
                'cor' => '#f97316',
                'latitude' => -7.9408,
                'longitude' => -34.8731,
            ],
            [
                'cidade' => 'Olinda',
                'regiao' => 'Bairro Novo, Casa Caiada e Rio Doce',
                'descricao' => 'Leve seus materiais separados para pontos parceiros e ajude a manter a cidade mais limpa.',
                'materiais' => ['Papel', 'Plástico', 'Vidro'],
                'cor' => '#16a34a',
                'latitude' => -8.0089,
                'longitude' => -34.8553,
            ],
            [
                'cidade' => 'Abreu e Lima',
                'regiao' => 'Centro e região metropolitana',
                'descricao' => 'Cooperativas locais recebem recicláveis limpos e organizados para triagem e reaproveitamento.',
                'materiais' => ['Papel', 'Metal', 'Vidro'],
                'cor' => '#8b5e3c',
                'latitude' => -7.9117,
                'longitude' => -34.9028,
            ],
            [
                'cidade' => 'Recife',
                'regiao' => 'Região central e zona norte',
                'descricao' => 'Consulte os locais de entrega voluntária mais próximos para descartar corretamente.',
                'materiais' => ['Papel', 'Plástico', 'Eletrônico', 'Óleo'],
                'cor' => '#0f766e',
                'latitude' => -8.0476,
                'longitude' => -34.8770,
            ],
        ];

        return view('pages.pontos-coleta.index', compact('pontos'));
    }
}
