<x-layouts::app :title="__('Histórico de Coletas')">
<div class="historico">
    <h1>Histórico de Coletas</h1>

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

    {{-- Tabela: item, doador do item, data/hora --}}
    <table class="historico-tabela">
        <thead>
            <tr>
                <th>Item coletado</th>
                <th>Doador do item</th>
                <th>Data/hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $r)
                <tr>
                    <td>{{ $r->item }}</td>
                    <td>{{ $r->doador->name ?? 'Usuário removido' }}</td>
                    <td>{{ $r->data_hora instanceof \Carbon\Carbon ? $r->data_hora->format('d/m/Y H:i') : \Carbon\Carbon::parse($r->data_hora)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Nenhuma coleta encontrada.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginação --}}
    <div class="historico-paginacao">
        {{ $registros->links() }}
    </div>
</div>

<style>
    
/* Histórico - Trash Hunters */

.historico {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    padding: 35px 40px;
    color: #183b36;
    box-sizing: border-box;
}

/* Título */
.historico h1 {
    font-size: 28px;
    font-weight: 700;
    color: #073f38;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.historico h1::after {
    content: "";
    display: block;
    width: 55px;
    height: 4px;
    background: #16b99b;
    border-radius: 5px;
    margin-top: 12px;
}

/* Filtros */
.historico-filtros {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    gap: 16px;
    padding: 22px;
    margin: 25px 0;
    background: #ffffff;
    border: 1px solid #e1eeea;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(5, 63, 56, 0.05);
}

.historico-filtros input,
.historico-filtros select {
    padding: 11px 13px;
    border: 1px solid #d6e5e0;
    border-radius: 8px;
    background: #fafffd;
    color: #254a43;
    font-size: 14px;
    outline: none;
    transition: 0.2s;
}

.historico-filtros input:focus,
.historico-filtros select:focus {
    border-color: #16b99b;
    box-shadow: 0 0 0 3px rgba(22, 185, 155, 0.12);
}

.historico-filtros input[name="busca"] {
    min-width: 210px;
}

.historico-filtros label {
    display: flex;
    flex-direction: column;
    gap: 7px;
    font-size: 13px;
    font-weight: 600;
    color: #31574e;
}

/* Botões */
.historico-filtros button {
    padding: 11px 20px;
    border: none;
    border-radius: 8px;
    background: #087d6b;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.historico-filtros button:hover {
    background: #065d50;
}

.historico-filtros a {
    padding: 11px 15px;
    color: #087d6b;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.historico-filtros a:hover {
    text-decoration: underline;
}

/* Tabela */
.historico-tabela {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e1eeea;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(5, 63, 56, 0.05);
}

.historico-tabela th {
    padding: 17px 20px;
    background: #e9f6f2;
    color: #07594c;
    text-align: left;
    font-size: 13px;
    font-weight: 700;
}

.historico-tabela td {
    padding: 17px 20px;
    border-top: 1px solid #edf2f0;
    color: #49635d;
    font-size: 14px;
}

.historico-tabela tbody tr {
    transition: background 0.2s;
}

.historico-tabela tbody tr:hover {
    background: #f3faf7;
}

/* Paginação */
.historico-paginacao {
    display: flex;
    justify-content: center;
    margin-top: 25px;
}

/* Responsividade */
@media (max-width: 700px) {
    .historico {
        padding: 20px 15px;
    }

    .historico-filtros {
        flex-direction: column;
        align-items: stretch;
    }

    .historico-filtros input,
    .historico-filtros select {
        width: 100%;
        box-sizing: border-box;
    }

    .historico-tabela {
        font-size: 12px;
    }

    .historico-tabela th,
    .historico-tabela td {
        padding: 12px 8px;
    }
}
</style>
</x-layouts::app>
