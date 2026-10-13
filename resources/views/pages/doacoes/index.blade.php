<x-layouts::app :title="__('Doar & coletar')">
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-600">Doações</p>
                <h1 class="text-3xl font-bold text-zinc-900">Itens disponíveis para coleta</h1>
            </div>

            <a href="{{ route('doacoes.create') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 font-semibold text-white transition hover:bg-emerald-500">
                Cadastrar doação
            </a>
        </div>

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

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($doacoes as $doacao)
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-semibold text-zinc-900">{{ $doacao->nome }}</h2>
                            <p class="mt-1 text-sm text-zinc-500">Doador: {{ $doacao->doador->name }}</p>
                        </div>
                        <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                            Disponível
                        </span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-zinc-600">
                        {{ $doacao->descricao ?? 'Sem descrição adicional.' }}
                    </p>

                    <div class="mt-5 flex items-center justify-between gap-3">
                        <span class="text-xs uppercase tracking-[0.18em] text-zinc-400">
                            {{ $doacao->created_at?->diffForHumans() }}
                        </span>

                        <form method="POST" action="{{ route('doacoes.coletar', $doacao) }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                Quero coletar
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-8 text-center text-zinc-500 md:col-span-2 xl:col-span-3">
                    Nenhum item disponível para coleta no momento.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $doacoes->links() }}
        </div>
    </div>
</x-layouts::app>
