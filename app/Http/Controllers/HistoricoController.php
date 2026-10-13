<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HistoricoController extends Controller
{
    public function doacoes(\Illuminate\Http\Request $request): \Illuminate\View\View
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

    public function coletas(\Illuminate\Http\Request $request): \Illuminate\View\View
    {
        $query = \App\Models\Doacao::query()->with(['doador', 'coletor'])
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

    /**
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Doacao>|\Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Doacao, \App\Models\User> $query
     */
    protected function applyFilters(\Illuminate\Http\Request $request, \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany $query): void
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
