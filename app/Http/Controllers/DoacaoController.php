<?php

namespace App\Http\Controllers;

use App\Enums\StatusDoacao;
use App\Models\Doacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoacaoController extends Controller
{
    /**
     * Doações disponíveis para coleta (exceto as do próprio usuário).
     */
    public function index(Request $request): View
    {
        $doacoes = Doacao::with('doador')
            ->where('status', StatusDoacao::Disponivel->value)
            ->where('user_id', '!=', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('pages.doacoes.index', ['doacoes' => $doacoes]);
    }

    public function create(): View
    {
        return view('pages.doacoes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->doacoes()->create([
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'] ?? null,
            'status' => StatusDoacao::Disponivel,
        ]);

        return redirect()
            ->route('historico.doacoes')
            ->with('status', 'Doação cadastrada com sucesso!');
    }

    /**
     * Coletor clica em "Quero coletar": disponível -> reservado.
     */
    public function coletar(Request $request, Doacao $doacao): RedirectResponse
    {
        abort_if($doacao->user_id === $request->user()->id, 403, 'Você não pode coletar a sua própria doação.');

        $reservou = Doacao::whereKey($doacao->id)
            ->where('status', StatusDoacao::Disponivel->value)
            ->update([
                'coletor_id' => $request->user()->id,
                'status' => StatusDoacao::Reservado->value,
                'reservado_em' => now(),
            ]);

        if (! $reservou) {
            return redirect()
                ->route('doacoes.index')
                ->with('error', 'Este item não está mais disponível.');
        }

        return redirect()
            ->route('historico.coletas')
            ->with('status', 'Item reservado! Aguarde a confirmação do doador.');
    }

    /**
     * Coletor desiste: reservado -> disponível.
     */
    public function liberar(Request $request, Doacao $doacao): RedirectResponse
    {
        abort_unless($doacao->coletor_id === $request->user()->id, 403);

        $liberou = Doacao::whereKey($doacao->id)
            ->where('status', StatusDoacao::Reservado->value)
            ->update([
                'coletor_id' => null,
                'status' => StatusDoacao::Disponivel->value,
                'reservado_em' => null,
            ]);

        return redirect()
            ->route('historico.coletas')
            ->with($liberou ? 'status' : 'error', $liberou ? 'Reserva liberada com sucesso.' : 'Não foi possível liberar esta reserva.');
    }

    /**
     * Doador confirma entrega: reservado -> coletado.
     */
    public function confirmar(Request $request, Doacao $doacao): RedirectResponse
    {
        abort_unless($doacao->user_id === $request->user()->id, 403);

        $confirmou = Doacao::whereKey($doacao->id)
            ->where('status', StatusDoacao::Reservado->value)
            ->update([
                'status' => StatusDoacao::Coletado->value,
                'coletado_em' => now(),
            ]);

        return redirect()
            ->route('historico.doacoes')
            ->with($confirmou ? 'status' : 'error', $confirmou ? 'Doação confirmada como coletada.' : 'Não foi possível confirmar esta doação.');
    }

    /**
     * Cancela a doação ou a reserva.
     */
    public function cancelar(Request $request, Doacao $doacao): RedirectResponse
    {
        $user = $request->user();

        if ($doacao->user_id === $user->id) {
            $cancelou = Doacao::whereKey($doacao->id)
                ->whereIn('status', [StatusDoacao::Disponivel->value, StatusDoacao::Reservado->value])
                ->update([
                    'status' => StatusDoacao::Cancelado->value,
                    'coletor_id' => null,
                    'reservado_em' => null,
                ]);

            return redirect()
                ->route('historico.doacoes')
                ->with($cancelou ? 'status' : 'error', $cancelou ? 'Doação cancelada com sucesso.' : 'Não foi possível cancelar esta doação.');
        }

        abort_unless($doacao->coletor_id === $user->id, 403);

        $cancelou = Doacao::whereKey($doacao->id)
            ->where('status', StatusDoacao::Reservado->value)
            ->update([
                'coletor_id' => null,
                'status' => StatusDoacao::Disponivel->value,
                'reservado_em' => null,
            ]);

        return redirect()
            ->route('historico.coletas')
            ->with($cancelou ? 'status' : 'error', $cancelou ? 'Reserva cancelada com sucesso.' : 'Não foi possível cancelar esta reserva.');
    }
}
