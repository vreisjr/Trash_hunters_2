<form method="GET" action="{{ $action }}" class="mb-6 grid gap-3 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm md:grid-cols-[1.4fr_220px_auto]">
    <label class="flex flex-col text-sm font-medium text-zinc-700">
        <span class="mb-1">Buscar</span>
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Nome ou descrição"
            class="rounded-xl border border-zinc-300 px-3 py-2 outline-none transition focus:border-emerald-500"
        >
    </label>

    <label class="flex flex-col text-sm font-medium text-zinc-700">
        <span class="mb-1">Status</span>
        <select name="status" class="rounded-xl border border-zinc-300 px-3 py-2 outline-none transition focus:border-emerald-500">
            <option value="">Todos</option>
            <option value="disponivel" {{ request('status') === 'disponivel' ? 'selected' : '' }}>Disponível</option>
            <option value="reservado" {{ request('status') === 'reservado' ? 'selected' : '' }}>Reservado</option>
            <option value="coletado" {{ request('status') === 'coletado' ? 'selected' : '' }}>Coletado</option>
            <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>
    </label>

    <div class="flex items-end gap-2">
        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-600 px-4 font-semibold text-white transition hover:bg-emerald-500">
            Filtrar
        </button>

        @if (request()->has('search') || request()->has('status'))
            <a href="{{ $action }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-zinc-300 px-4 font-semibold text-zinc-700 transition hover:bg-zinc-100">
                Limpar
            </a>
        @endif
    </div>
</form>
