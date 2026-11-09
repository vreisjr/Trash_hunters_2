{{--
  Lista do histórico (usada por doacoes e coletas).
  Recebe: $registros, $titulo, $subtitulo, $colItem, $colPessoa, $pessoa ('coletor' ou 'doador'), $vazio
--}}
<div class="hist">

    {{-- Cabeçalho --}}
    <header class="hist-header">
        <div>
            <span class="hist-eyebrow">{{ __('Histórico') }}</span>
            <h1>{{ $titulo }}</h1>
            <p>{{ $subtitulo }}</p>
        </div>
        <span class="hist-total">
            <strong>{{ $registros->total() }}</strong>
            {{ $registros->total() === 1 ? __('registro') : __('registros') }}
        </span>
    </header>

    {{-- Filtros --}}
    <form method="GET" class="hist-card hist-filters">
        <label class="hist-field hist-field-search">
            <span>{{ __('Buscar') }}</span>
            <input type="text" name="busca" value="{{ request('busca') }}" placeholder="{{ __('Item ou nome do usuário') }}">
        </label>

        <label class="hist-field">
            <span>{{ __('De') }}</span>
            <input type="date" name="de" value="{{ request('de') }}">
        </label>

        <label class="hist-field">
            <span>{{ __('Até') }}</span>
            <input type="date" name="ate" value="{{ request('ate') }}">
        </label>

        <label class="hist-field">
            <span>{{ __('Ordenar') }}</span>
            <select name="ordem">
                <option value="desc" @selected(request('ordem', 'desc') === 'desc')>{{ __('Mais recentes') }}</option>
                <option value="asc" @selected(request('ordem') === 'asc')>{{ __('Mais antigos') }}</option>
            </select>
        </label>

        <div class="hist-actions">
            <button type="submit" class="hist-btn hist-btn-primary">{{ __('Filtrar') }}</button>
            @if (request()->hasAny(['busca', 'de', 'ate', 'ordem']))
                <a href="{{ url()->current() }}" class="hist-btn">{{ __('Limpar') }}</a>
            @endif
        </div>
    </form>

    {{-- Tabela --}}
    <div class="hist-card hist-table-wrap">
        @if ($registros->isEmpty())
            <div class="hist-empty">
                <div class="hist-empty-icon" aria-hidden="true">♻</div>
                @if (request()->hasAny(['busca', 'de', 'ate']))
                    <h2>{{ __('Nenhum resultado para esse filtro') }}</h2>
                    <p>{{ __('Tente outro nome, outro período ou limpe os filtros.') }}</p>
                @else
                    <h2>{{ $vazio }}</h2>
                    <p>{{ __('Quando houver movimentações, elas aparecem aqui.') }}</p>
                    <a href="{{ route('dashboard') }}" class="hist-btn hist-btn-primary">{{ __('Ver itens disponíveis') }}</a>
                @endif
            </div>
        @else
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>{{ $colItem }}</th>
                        <th>{{ $colPessoa }}</th>
                        <th>{{ __('Data e hora') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registros as $r)
                        @php $outro = $r->$pessoa; @endphp
                        <tr>
                            <td class="hist-item" data-label="{{ $colItem }}">{{ $r->item }}</td>
                            <td data-label="{{ $colPessoa }}">
                                <span class="hist-person">
                                    <span class="hist-avatar">{{ mb_strtoupper(mb_substr($outro->name ?? '?', 0, 1)) }}</span>
                                    {{ $outro->name ?? __('Usuário removido') }}
                                </span>
                            </td>
                            <td data-label="{{ __('Data e hora') }}">
                                <span class="hist-date">{{ $r->data_hora->format('d/m/Y') }}</span>
                                <span class="hist-time">{{ $r->data_hora->format('H:i') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Paginação --}}
    @if ($registros->hasPages())
        <nav class="hist-pagination" aria-label="{{ __('Paginação') }}">
            <span class="hist-muted">
                {{ __('Mostrando :de a :ate de :total', ['de' => $registros->firstItem(), 'ate' => $registros->lastItem(), 'total' => $registros->total()]) }}
            </span>

            <div class="hist-pagination-buttons">
                @if ($registros->onFirstPage())
                    <span class="hist-btn is-disabled">‹ {{ __('Anterior') }}</span>
                @else
                    <a class="hist-btn" href="{{ $registros->previousPageUrl() }}">‹ {{ __('Anterior') }}</a>
                @endif

                <span class="hist-page">{{ __('Página :a de :b', ['a' => $registros->currentPage(), 'b' => $registros->lastPage()]) }}</span>

                @if ($registros->hasMorePages())
                    <a class="hist-btn" href="{{ $registros->nextPageUrl() }}">{{ __('Próxima') }} ›</a>
                @else
                    <span class="hist-btn is-disabled">{{ __('Próxima') }} ›</span>
                @endif
            </div>
        </nav>
    @endif
</div>

<style>
    .hist { max-width: 980px; margin: 0 auto; padding: 2rem 1.25rem 3rem; color: #1f2937; }

    .hist-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
    .hist-eyebrow { display: block; font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #059669; margin-bottom: .35rem; }
    .hist-header h1 { font-size: 1.9rem; font-weight: 700; line-height: 1.15; margin: 0; }
    .hist-header p { margin: .35rem 0 0; color: #6b7280; font-size: .95rem; }
    .hist-total { background: #d1fae5; color: #065f46; padding: .45rem .9rem; border-radius: 999px; font-size: .85rem; }
    .hist-total strong { font-size: 1rem; }

    .hist-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 4px 18px rgba(17, 24, 39, .05); }

    .hist-filters { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: .9rem; align-items: end; padding: 1rem 1.1rem; margin-bottom: 1.1rem; }
    .hist-field { display: flex; flex-direction: column; gap: .3rem; min-width: 0; }
    .hist-field span { font-size: .72rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; }
    .hist-field input, .hist-field select { width: 100%; padding: .6rem .75rem; border: 1px solid #d1d5db; border-radius: 10px; background: #fff; font-size: .9rem; color: #1f2937; }
    .hist-field input:focus, .hist-field select:focus { outline: 2px solid #34d399; outline-offset: 1px; border-color: #059669; }
    .hist-actions { display: flex; gap: .5rem; }

    .hist-btn { display: inline-flex; align-items: center; justify-content: center; padding: .6rem 1.1rem; border: 1px solid #d1d5db; border-radius: 999px; background: #fff; color: #374151; font-size: .85rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: background .15s, border-color .15s; }
    .hist-btn:hover { background: #f3f4f6; }
    .hist-btn-primary { background: #059669; border-color: #059669; color: #fff; }
    .hist-btn-primary:hover { background: #047857; }
    .hist-btn.is-disabled { opacity: .45; cursor: default; pointer-events: none; }

    .hist-table-wrap { overflow-x: auto; }
    .hist-table { width: 100%; border-collapse: collapse; }
    .hist-table th { text-align: left; padding: .9rem 1.1rem; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .hist-table td { padding: .95rem 1.1rem; border-bottom: 1px solid #f1f5f9; font-size: .92rem; vertical-align: middle; }
    .hist-table tbody tr:last-child td { border-bottom: 0; }
    .hist-table tbody tr:hover { background: #f0fdf9; }
    .hist-item { font-weight: 600; max-width: 360px; }

    .hist-person { display: inline-flex; align-items: center; gap: .6rem; }
    .hist-avatar { width: 30px; height: 30px; border-radius: 50%; background: #a8e6a3; color: #2b3d2f; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .8rem; flex-shrink: 0; }
    .hist-date { font-weight: 600; margin-right: .4rem; }
    .hist-time, .hist-muted { color: #6b7280; font-size: .85rem; }

    .hist-empty { text-align: center; padding: 3rem 1.5rem; }
    .hist-empty-icon { width: 56px; height: 56px; margin: 0 auto 1rem; border-radius: 50%; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
    .hist-empty h2 { margin: 0 0 .35rem; font-size: 1.1rem; font-weight: 700; }
    .hist-empty p { margin: 0 0 1.1rem; color: #6b7280; font-size: .9rem; }

    .hist-pagination { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1.1rem; flex-wrap: wrap; }
    .hist-pagination-buttons { display: flex; align-items: center; gap: .6rem; }
    .hist-page { font-size: .85rem; color: #374151; font-weight: 600; }

    @media (max-width: 820px) {
        .hist-filters { grid-template-columns: 1fr 1fr; }
        .hist-field-search { grid-column: 1 / -1; }
        .hist-actions { grid-column: 1 / -1; }

        /* Tabela vira lista de cartões no celular */
        .hist-table thead { display: none; }
        .hist-table, .hist-table tbody, .hist-table tr, .hist-table td { display: block; width: 100%; }
        .hist-table tr { padding: .6rem 0; border-bottom: 1px solid #e5e7eb; }
        .hist-table td { border: 0; padding: .3rem 1.1rem; }
        .hist-table td::before { content: attr(data-label); display: block; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; }
    }
</style>