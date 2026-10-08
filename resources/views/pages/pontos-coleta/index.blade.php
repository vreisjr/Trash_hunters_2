<x-layouts::app :title="__('Pontos de coleta')">
    <div class="collection-points-page">
        <header class="collection-points-hero">
            <div class="collection-points-hero-icon" aria-hidden="true">
                <span>⌖</span>
                <small>↻</small>
            </div>
            <div>
                <span class="recovery-eyebrow">{{ __('Descarte certo, impacto positivo') }}</span>
                <h1>{{ __('Pontos de coleta perto de você') }}</h1>
                <p>{{ __('Consulte locais reais no mapa e descubra quais materiais separar antes de sair.') }}</p>
            </div>
        </header>

        <nav class="collection-points-filters" aria-label="{{ __('Filtrar pontos por cidade') }}">
            <button class="collection-points-filter is-active" type="button" data-point-filter="all">⊕ {{ __('Todos') }}</button>
            @foreach ($pontos as $ponto)
                <button class="collection-points-filter" type="button" data-point-filter="{{ $ponto['cidade'] }}">⌖ {{ $ponto['cidade'] }}</button>
            @endforeach
        </nav>

        <section class="collection-points-grid" aria-label="{{ __('Pontos de coleta') }}">
            @foreach ($pontos as $index => $ponto)
                @php
                    $mapUrl = "https://www.google.com/maps/search/?api=1&query={$ponto['latitude']},{$ponto['longitude']}";
                @endphp
                <article class="collection-point-card" data-point-city="{{ $ponto['cidade'] }}" style="--point-color: {{ $ponto['cor'] }}">
                    <div class="collection-point-card-top">
                        <span class="collection-point-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="collection-point-city">{{ $ponto['cidade'] }}</span>
                        <span class="collection-point-status"><i></i>{{ __('Consulte no mapa') }}</span>
                    </div>

                    <div class="collection-point-title">
                        <span class="collection-point-pin" aria-hidden="true">⌖</span>
                        <div>
                            <h2>{{ __('Pontos de coleta em :city', ['city' => $ponto['cidade']]) }}</h2>
                            <p>{{ $ponto['regiao'] }}</p>
                        </div>
                    </div>

                    <p class="collection-point-description">{{ $ponto['descricao'] }}</p>

                    <div class="collection-point-materials">
                        <h3><span aria-hidden="true">⌁</span> {{ __('Materiais aceitos') }}</h3>
                        <div>
                            @foreach ($ponto['materiais'] as $material)
                                <span>{{ $material }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="collection-point-tip">
                        <span aria-hidden="true">♧</span>
                        <div>
                            <strong>{{ __('Prepare antes de levar') }}</strong>
                            <small>{{ __('Materiais secos, limpos e separados facilitam a reciclagem.') }}</small>
                        </div>
                    </div>

                    <div class="collection-point-actions">
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="collection-point-map-button">⌖ {{ __('Ver no mapa') }}</a>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="collection-point-directions-button">⌁ {{ __('Como chegar') }}</a>
                    </div>
                </article>
            @endforeach
        </section>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-point-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('[data-point-filter]').forEach((item) => item.classList.remove('is-active'));
                    button.classList.add('is-active');

                    const city = button.dataset.pointFilter;
                    document.querySelectorAll('.collection-point-card').forEach((card) => {
                        card.hidden = city !== 'all' && card.dataset.pointCity !== city;
                    });
                });
            });
        </script>
    @endpush
</x-layouts::app>
