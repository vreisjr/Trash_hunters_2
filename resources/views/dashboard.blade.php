<x-layouts::app :title="__('Início')">
    <div class="recovery-home">
        <header class="recovery-home-header">
            <div>
                <span class="recovery-eyebrow">{{ __('Reaproveite perto de você') }}</span>
                <h1>{{ __('Itens para uma nova história') }}</h1>
                <p>{{ __('Encontre materiais e objetos que ainda podem ser reutilizados.') }}</p>
            </div>

            <button class="recovery-publish-button" type="button" id="open-publish-panel" aria-expanded="false" aria-controls="publish-panel">
                <span aria-hidden="true">+</span>
                {{ __('Publicar item') }}
            </button>
        </header>

        <section class="recovery-publish-panel" id="publish-panel" hidden>
            <div class="recovery-publish-panel-heading">
                <div class="recovery-publish-heading-content">
                    <span class="recovery-publish-heading-icon" aria-hidden="true">↗</span>
                    <div>
                        <span class="recovery-eyebrow">{{ __('Nova publicação') }}</span>
                        <h2>{{ __('Dê uma nova história para um resíduo') }}</h2>
                        <p>{{ __('Compartilhe algo que pode ser útil para outra pessoa.') }}</p>
                    </div>
                </div>
                <button class="recovery-panel-close" type="button" id="close-publish-panel" aria-label="{{ __('Fechar') }}">×</button>
            </div>

            @if ($errors->any())
                <div class="recovery-form-errors">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form class="recovery-publish-form" method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                @csrf
                <label>
                    <span>{{ __('O que você está oferecendo?') }} <em>*</em></span>
                    <textarea name="content" rows="3" required placeholder="{{ __('Descreva o resíduo, suas condições e como ele pode ser reaproveitado.') }}">{{ old('content') }}</textarea>
                    <small>{{ __('Quanto mais detalhes, mais fácil será encontrar um novo destino.') }}</small>
                </label>

                <div class="recovery-form-grid">
                    <label>
                        <span>{{ __('Categoria') }} <em>*</em></span>
                        <select name="categoria_ids[]" required>
                            <option value="">{{ __('Selecione uma categoria') }}</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>{{ __('Onde está o item?') }}</span>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="{{ __('Bairro ou endereço') }}">
                    </label>
                </div>

                <div class="recovery-form-grid">
                    <label>
                        <span>{{ __('Imagem do item') }}</span>
                        <input type="file" name="media[]" accept="image/jpeg,image/png,image/gif">
                        <small>{{ __('JPG, PNG ou GIF · até 10 MB') }}</small>
                    </label>

                    <label>
                        <span>{{ __('Como será disponibilizado?') }}</span>
                        <select name="price_type" id="publish-price-type">
                            <option value="free">{{ __('Grátis') }}</option>
                            <option value="paid">{{ __('Defina um valor') }}</option>
                        </select>
                    </label>
                </div>

                <label id="publish-price-field" hidden>
                    <span>{{ __('Valor em reais') }}</span>
                    <input type="number" name="price" min="0.01" step="0.01" placeholder="0,00">
                </label>

                <div class="recovery-publish-form-actions">
                    <button class="recovery-panel-cancel" type="button" id="cancel-publish-panel">{{ __('Cancelar') }}</button>
                    <button class="recovery-publish-button" type="submit">{{ __('Publicar item') }}</button>
                </div>
            </form>
        </section>

        <nav class="recovery-filters" aria-label="{{ __('Filtrar resíduos por categoria') }}">
            <button class="recovery-filter is-active" type="button" data-category-filter="all">{{ __('Todos') }}</button>
            @foreach ($categorias as $categoria)
                <button class="recovery-filter" type="button" data-category-filter="{{ $categoria->id }}">
                    {{ $categoria->nome }}
                </button>
            @endforeach
        </nav>

        <section class="recovery-grid" aria-label="{{ __('Itens disponíveis') }}">
            @forelse ($posts as $post)
                @php
                    $image = $post->attachments->firstWhere('file_type', 'image');
                    $category = $post->categorias->first();
                    $title = \Illuminate\Support\Str::of($post->content)->squish()->limit(48);
                @endphp

                <article class="recovery-card" data-post-categories="{{ $post->categorias->pluck('id')->implode(',') }}">
                    <a class="recovery-card-media" href="{{ route('posts.index') }}#post-{{ $post->id }}">
                        @if ($image)
                            <img src="{{ asset('storage/'.$image->file_path) }}" alt="{{ $title }}">
                        @else
                            <div class="recovery-card-placeholder" aria-hidden="true">
                                <span>{{ __('Sem imagem') }}</span>
                            </div>
                        @endif

                        @if ($category)
                            <span class="recovery-card-category" style="--category-color: {{ $category->cor }}">
                                {{ $category->nome }}
                            </span>
                        @endif
                    </a>

                    <div class="recovery-card-body">
                        <div class="recovery-card-title-row">
                            <h2>{{ $title }}</h2>
                            <button class="recovery-favorite" type="button" aria-label="{{ __('Salvar item') }}">
                                <span aria-hidden="true">♡</span>
                            </button>
                        </div>

                        <p class="recovery-card-author">
                            {{ __('Publicado por :name', ['name' => $post->user->name]) }}
                        </p>

                        <p class="recovery-card-location">
                            <span aria-hidden="true">⌖</span>
                            {{ $post->address ?? $post->district ?? __('Local não informado') }}
                            <span aria-hidden="true">·</span>
                            {{ $post->created_at?->diffForHumans() }}
                        </p>
                        <p>
                            {{ __('Status: :status', ['status' => $post->status]) }}
                        </p>

                        <div class="recovery-card-footer">
                            <span class="recovery-availability">
                                <span aria-hidden="true"></span>
                                {{ __('Disponível') }}
                            </span>
                            <span class="recovery-price">
                                {{ $post->price_type === 'paid' && $post->price !== null
                                    ? 'R$ '.number_format((float) $post->price, 2, ',', '.')
                                    : __('Grátis') }}
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="recovery-empty">
                    <h2>{{ __('Ainda não há resíduos publicados') }}</h2>
                    <p>{{ __('Seja a primeira pessoa a publicar um item para reaproveitamento.') }}</p>
                    <a class="recovery-publish-button" href="{{ route('posts.create') }}">{{ __('Publicar item') }}</a>
                </div>
            @endforelse
        </section>
    </div>

    @push('scripts')
        <script>
            const publishPanel = document.getElementById('publish-panel');
            const publishButton = document.getElementById('open-publish-panel');
            const closePublishPanel = () => {
                publishPanel.hidden = true;
                publishButton.setAttribute('aria-expanded', 'false');
            };

            publishButton.addEventListener('click', () => {
                publishPanel.hidden = false;
                publishButton.setAttribute('aria-expanded', 'true');
                publishPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                publishPanel.querySelector('textarea').focus();
            });
            document.getElementById('close-publish-panel').addEventListener('click', closePublishPanel);
            document.getElementById('cancel-publish-panel').addEventListener('click', closePublishPanel);

            const priceType = document.getElementById('publish-price-type');
            const priceField = document.getElementById('publish-price-field');
            priceType.addEventListener('change', () => {
                priceField.hidden = priceType.value !== 'paid';
            });

            document.querySelectorAll('[data-category-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('[data-category-filter]').forEach((item) => item.classList.remove('is-active'));
                    button.classList.add('is-active');

                    const category = button.dataset.categoryFilter;
                    document.querySelectorAll('.recovery-card').forEach((card) => {
                        const categories = card.dataset.postCategories.split(',').filter(Boolean);
                        card.hidden = category !== 'all' && !categories.includes(category);
                    });
                });
            });
        </script>
    @endpush
</x-layouts::app>
