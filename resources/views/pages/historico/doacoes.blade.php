<x-layouts::app :title="__('Histórico de Doações')">
<div class="historico">
    <h1>Histórico de Doações</h1>

    {{-- Filtros e ordenação --}}
    <form method="GET" class="historico-filtros">
        <input type="text" name="busca" value="{{ request('busca') }}"
               placeholder="Buscar item ou usuário">

        <label>De <input type="date" name="de" value="{{ request('de') }}"></label>
        <label>Até <input type="date" name="ate" value="{{ request('ate') }}"></label>

        <select name="ordem">
            <option value="desc" @selected(request('ordem', 'desc') === 'desc')>Mais recentes</option>
            <option value="asc"  @selected(request('ordem') === 'asc')>Mais antigos</option>
        </select>

        <button type="submit">Filtrar</button>
        <a href="{{ url()->current() }}">Limpar</a>
    </form>

    {{-- Tabela: item, para qual usuário, data/hora --}}
    <table class="historico-tabela">
        <thead>
            <tr>
                <th>Item doado</th>
                <th>Coletado por</th>
                <th>Data/hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $r)
                <tr>
                    <td>{{ $r->item }}</td>
                    <td>{{ $r->coletor->name ?? 'Usuário removido' }}</td>
                    <td>{{ $r->data_hora instanceof \Carbon\Carbon ? $r->data_hora->format('d/m/Y H:i') : \Carbon\Carbon::parse($r->data_hora)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Nenhuma doação encontrada.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginação --}}
    <div class="historico-paginacao">
        {{ $registros->links() }}
    </div>
</div>

<style>
    .historico { max-width: 900px; margin: 0 auto; padding: 1rem; }
    .historico-filtros { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; align-items: center; }
    .historico-tabela { width: 100%; border-collapse: collapse; }
    .historico-tabela th, .historico-tabela td { padding: .6rem; border-bottom: 1px solid #ddd; text-align: left; }
    .historico-paginacao { margin-top: 1rem; }
</style>
</x-layouts::app>
