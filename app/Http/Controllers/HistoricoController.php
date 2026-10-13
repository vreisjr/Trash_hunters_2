<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HistoricoController extends Controller
{
    public function doacoes(Request $request): View
    {
        $query = $request->user()->doacoes()->with('coletor');

        $this->applyFilters($request, $query);

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $doacoes = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('pages.historico.doacoes', [
            'doacoes' => $doacoes,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function coletas(Request $request): View
    {
        $query = Doacao::query()->with(['doador', 'coletor'])
            ->where('coletor_id', $request->user()->id);

        $this->applyFilters($request, $query);

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $doacoes = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('pages.historico.coletas', [
            'doacoes' => $doacoes,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

<<<<<<< HEAD
    /**
     * @param Builder<Doacao>|HasMany<Doacao> $query
     */
    protected function applyFilters(Request $request, Builder|HasMany $query): void
=======
    protected function applyFilters(Request $request, mixed $query): void
>>>>>>> 58cf129 (Altera tipo do parâmetro 'query' para 'mixed' no método applyFilters)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = Str::of($request->search)->trim();

            if ($search->isNotEmpty()) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nome', 'like', "%{$search}%")
                        ->orWhere('descricao', 'like', "%{$search}%");
                });
            }
        }
    }
}
