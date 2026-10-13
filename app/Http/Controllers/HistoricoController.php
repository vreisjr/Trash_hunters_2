<?php

namespace App\Http\Controllers;

use App\Models\Coleta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HistoricoController extends Controller
{
    // Histórico de DOAÇÕES: itens que o usuário logado doou
    public function doacoes(Request $request): View
    {
        $query = Coleta::with('coletor')
            ->where('doador_id', auth()->id());

        // FILTRO por texto (nome do item ou nome de quem coletou)
        if ($busca = $request->input('busca')) {
            $query->where(function ($q) use ($busca) {
                $q->where('item', 'like', "%{$busca}%")
                    ->orWhereHas('coletor', fn ($u) => $u->where('name', 'like', "%{$busca}%"));
            });
        }

        // FILTRO por período
        if ($request->filled('de')) {
            $query->whereDate('data_hora', '>=', $request->input('de'));
        }
        if ($request->filled('ate')) {
            $query->whereDate('data_hora', '<=', $request->input('ate'));
        }

        // ORDENAÇÃO (mais recentes primeiro por padrão)
        $ordem = $request->input('ordem') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('data_hora', $ordem);

        // PAGINAÇÃO (10 por página, mantendo os filtros na URL)
        $registros = $query->paginate(10)->withQueryString();

        return view('pages.historico.doacoes', compact('registros'));
    }
}
