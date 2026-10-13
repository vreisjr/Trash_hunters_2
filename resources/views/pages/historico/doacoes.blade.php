<x-layouts::app :title="__('Histórico de doações')">
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-600">Histórico</p>
                <h1 class="text-3xl font-bold text-zinc-900">Doações</h1>
            </div>

            <a href="{{ route('doacoes.create') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 font-semibold text-white transition hover:bg-emerald-500">
                Nova doação
            </a>
        </div>

        @include('pages.historico.partials.filtros', ['action' => route('historico.doacoes')])

        @session('status')
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ $value }}
            </div>
        @endsession

        @session('error')
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $value }}
            </div>
        @endsession

        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-zinc-200 text-left text-sm">
                <thead class="bg-zinc-50">
                    <tr>
                        <x-historico.th-ordenavel field="nome" label="Item" :sortField="$sortField" :sortDirection="$sortDirection" />
                        <x-historico.th-ordenavel field="status" label="Status" :sortField="$sortField" :sortDirection="$sortDirection" />
                        <x-historico.th-ordenavel field="created_at" label="Cadastrado" :sortField="$sortField" :sortDirection="$sortDirection" />
                        <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700">Coletor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse ($doacoes as $doacao)
                        <tr class="align-top">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900">{{ $doacao->nome }}</div>
                                <div class="text-zinc-500">{{ \Illuminate\Support\Str::limit($doacao->descricao ?? 'Sem descrição', 60) }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $doacao->status->classes() }}">
                                    {{ $doacao->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-600">{{ $doacao->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-zinc-600">
                                {{ $doacao->coletor ? $doacao->coletor->name : 'Ainda não reservado' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($doacao->status === App\Enums\StatusDoacao::Reservado)
                                    <form method="POST" action="{{ route('doacoes.confirmar', $doacao) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500">
                                            Confirmar coleta
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-zinc-500">
                                Nenhuma doação encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $doacoes->links() }}
        </div>
    </div>
</x-layouts::app>
