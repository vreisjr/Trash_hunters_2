<x-layouts::app :title="__('Nova doação')">
    <div class="mx-auto max-w-2xl space-y-6 p-6">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-600">Doação</p>
            <h1 class="text-3xl font-bold text-zinc-900">Cadastrar item para doar</h1>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('doacoes.store') }}" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="nome" class="mb-1 block text-sm font-medium text-zinc-700">Nome do item</label>
                <input id="nome" name="nome" value="{{ old('nome') }}" required maxlength="150" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 outline-none transition focus:border-emerald-500" placeholder="Ex.: mesa de escritório">
            </div>

            <div>
                <label for="descricao" class="mb-1 block text-sm font-medium text-zinc-700">Descrição</label>
                <textarea id="descricao" name="descricao" rows="5" maxlength="1000" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 outline-none transition focus:border-emerald-500" placeholder="Descreva o estado do item e qualquer detalhe importante.">{{ old('descricao') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('doacoes.index') }}" class="inline-flex items-center justify-center rounded-xl border border-zinc-300 px-4 py-2.5 font-semibold text-zinc-700 transition hover:bg-zinc-100">
                    Cancelar
                </a>

                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 font-semibold text-white transition hover:bg-emerald-500">
                    Salvar doação
                </button>
            </div>
        </form>
    </div>
</x-layouts::app>
