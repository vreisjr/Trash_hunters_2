<x-layouts::app :title="__('Início')">
    <div class="recovery-home">

        {{-- Mensagens de sucesso/erro da coleta --}}
        @session('status')
            {{-- <div style="background:#ffffff; color:#374151; padding:14px 18px; border-radius:12px; margin-bottom:16px;"> --}}
            <div class="bg-white text-gray-700 px-[18px] py-[14px] rounded-xl mb-4">
                {{ $value }}
            </div>
        @endsession

        @if ($errors->has('coleta'))
            <div style="background:#ffe8ec; color:#d81b60; padding:14px 18px; border-radius:12px; margin-bottom:16px;">
                {{ $errors->first('coleta') }}
            </div>
        @endif

        <header class="recovery-home-header">
            <div>
                <span class="recovery-eyebrow">{{ __('Reaproveite perto de você') }}</span>
                <h1>{{ __('Itens para uma nova história') }}</h1>
                <p>{{ __('Encontre materiais e objetos que ainda podem ser reutilizados.') }}</p>
            </div>

            <button class="recovery-publish-button" type="button" id="open-publish-panel" aria-expanded="false" aria-controls="publish-panel">
                <span aria-hidden="true">+</span>
                {{ __('Anunciar Venda') }}
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
                <input type="hidden" name="post_type" value="marketplace">
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
                    <button class="recovery-publish-button" type="submit">{{ __('Anunciar Venda') }}</button>
                </div>
            </form>
        </section>

        @php
            $locations = $posts
                ->map(fn ($post) => $post->address ?? $post->district)
                ->filter()
                ->unique()
                ->sort()
                ->values();
        @endphp

        <section class="recovery-search-bar" aria-label="{{ __('Buscar e filtrar itens') }}">
            <label class="recovery-search-field">
                <span aria-hidden="true">⌕</span>
                <span class="sr-only">{{ __('Buscar por item') }}</span>
                <input id="item-search" type="search" placeholder="{{ __('Buscar por item...') }}" autocomplete="off">
            </label>

            <label class="recovery-location-field">
                <span aria-hidden="true">⌖</span>
                <span class="sr-only">{{ __('Filtrar por local') }}</span>
                <select id="item-location-filter">
                    <option value="all">{{ __('Filtrar por local') }}</option>
                    @foreach ($locations as $location)
                        <option value="{{ \Illuminate\Support\Str::lower($location) }}">{{ $location }}</option>
                    @endforeach
                </select>
            </label>

            <div class="recovery-type-filters" role="group" aria-label="{{ __('Filtrar por tipo de disponibilização') }}">
                <button class="recovery-type-filter is-active" type="button" data-type-filter="all">{{ __('Todos') }}</button>
                <button class="recovery-type-filter" type="button" data-type-filter="free">{{ __('Doações') }}</button>
                <button class="recovery-type-filter" type="button" data-type-filter="paid">{{ __('Vendas') }}</button>
            </div>
        </section>

        @php
            $categoryFilterColors = [
                'all' => '!border-emerald-600 !bg-emerald-600 !text-white hover:!border-emerald-700 hover:!bg-emerald-700',
                'eletrônico' => '!border-blue-500 !bg-blue-50 !text-blue-700 hover:!border-blue-600 hover:!bg-blue-100',
                'metal' => '!border-yellow-500 !bg-yellow-50 !text-yellow-700 hover:!border-yellow-600 hover:!bg-yellow-100',
                'orgânico' => '!border-green-600 !bg-green-50 !text-green-700 hover:!border-green-700 hover:!bg-green-100',
                'outros' => '!border-slate-500 !bg-slate-50 !text-slate-700 hover:!border-slate-600 hover:!bg-slate-100',
                'papel' => '!border-sky-500 !bg-sky-50 !text-sky-700 hover:!border-sky-600 hover:!bg-sky-100',
                'plástico' => '!border-amber-500 !bg-amber-50 !text-amber-700 hover:!border-amber-600 hover:!bg-amber-100',
                'vidro' => '!border-teal-500 !bg-teal-50 !text-teal-700 hover:!border-teal-600 hover:!bg-teal-100',
            ];
        @endphp

        <nav class="recovery-filters" aria-label="{{ __('Filtrar resíduos por categoria') }}">
            <button class="recovery-filter is-active {{ $categoryFilterColors['all'] }}" type="button" data-category-filter="all">{{ __('Todos') }}</button>
            @foreach ($categorias as $categoria)
                @php
                    $categoryName = \Illuminate\Support\Str::lower($categoria->nome);
                    $categoryColor = $categoryFilterColors[$categoryName] ?? '!border-emerald-500 !text-emerald-700 hover:!border-emerald-600 hover:!bg-emerald-50';
                @endphp
                <button class="recovery-filter {{ $categoryColor }}" type="button" data-category-filter="{{ $categoria->id }}">
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
                    $jaColetado = in_array($post->id, $coletados ?? [], true);
                    $isSaved = $post->savedByUsers->isNotEmpty();
                @endphp

                <article
                    class="recovery-card"
                    data-post-categories="{{ $post->categorias->pluck('id')->implode(',') }}"
                    data-post-type="{{ $post->price_type }}"
                    data-post-location="{{ \Illuminate\Support\Str::lower($post->address ?? $post->district ?? '') }}"
                    data-post-search="{{ \Illuminate\Support\Str::lower($post->content) }}"
                >
                    <div class="recovery-card-media">
                        <a class="recovery-card-media-link" href="{{ route('posts.index') }}#post-{{ $post->id }}">
                            @if ($image)
                                <img src="{{ asset('storage/'.$image->file_path) }}" alt="{{ $title }}">
                            @else
                                <div class="recovery-card-placeholder" aria-hidden="true">
                                    <span>{{ __('Sem imagem') }}</span>
                                </div>
                            @endif
                        </a>

                        <span class="recovery-card-offer !text-sm !font-extrabold {{ $post->price_type === 'paid' ? 'is-sale' : 'is-donation' }}">
                            <span aria-hidden="true">{{ $post->price_type === 'paid' ? '▰' : '♻' }}</span>
                            {{ $post->price_type === 'paid' ? __('Venda') : __('Doação') }}
                        </span>

                        <button
                            class="recovery-favorite {{ $isSaved ? 'is-saved' : '' }}"
                            type="button"
                            aria-label="{{ $isSaved ? __('Remover item dos salvos') : __('Salvar item') }}"
                            aria-pressed="{{ $isSaved ? 'true' : 'false' }}"
                            data-save-url="{{ route('posts.salvar', $post) }}"
                        >
                            <span aria-hidden="true">{{ $isSaved ? '♥' : '♡' }}</span>
                        </button>
                    </div>

                    <div class="recovery-card-body">
                        <div class="recovery-card-title-row">
                            <h2 class="!text-xl !font-extrabold !text-slate-800">{{ $title }}</h2>
                        </div>

                        <p class="recovery-card-author !text-sm !font-semibold !text-slate-700">
                            @if ($post->user->profilePhotoUrl())
                                <img
                                    class="!size-8 !shrink-0 !rounded-full !object-cover"
                                    src="{{ $post->user->profilePhotoUrl() }}"
                                    alt="{{ __('Foto de perfil de :name', ['name' => $post->user->name]) }}"
                                >
                            @else
                                <span class="!grid !size-8 !shrink-0 !place-items-center !rounded-full !bg-slate-200 !text-xs !font-bold !text-slate-600" aria-hidden="true">
                                    {{ $post->user->initials() }}
                                </span>
                            @endif
                            {{ __('Publicado por :name', ['name' => $post->user->name]) }}
                        </p>

                        <p class="recovery-card-location !text-sm !font-medium !text-slate-600">
                            <span class="!text-base" aria-hidden="true">📍</span>
                            {{ $post->address ?? $post->district ?? __('Local não informado') }}
                            <span aria-hidden="true">•</span>
                            <span aria-hidden="true">◷</span>
                            {{ $post->created_at?->diffForHumans() }}
                        </p>
                        <p class="recovery-card-status !text-sm !font-bold !text-emerald-800">
                            <span aria-hidden="true"></span>
                            {{ __('Status: :status', ['status' => $post->status]) }}
                        </p>

                        <div class="recovery-card-footer">
                            <span class="recovery-availability !text-sm !font-bold !text-emerald-700">
                                <span aria-hidden="true"></span>
                                {{ $jaColetado ? __('Coletado') : __('Disponível') }}
                            </span>
                            <span class="recovery-price !text-sm !font-bold !text-emerald-800">
                                {{ $post->price_type === 'paid' && $post->price !== null
                                    ? 'R$ '.number_format((float) $post->price, 2, ',', '.')
                                    : __('Grátis') }}
                            </span>
                        </div>

                        {{-- Botão Coletar: itens disponíveis de outros usuários --}}
                        @if (! $jaColetado && $post->user_id !== auth()->id())
                            <form
                                method="POST"
                                action="{{ route('posts.coletar', $post) }}"
                                onsubmit="return confirm('Confirmar que você vai coletar este item?');"
                                style="margin-top:10px;"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    style="width:100%; padding:9px 14px; border:1px solid #059669; background:#059669; color:#ffffff; border-radius:999px; cursor:pointer; font-weight:600; font-size:13px;"
                                >
                                    {{ __('Coletar') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="recovery-empty">
                    <h2>{{ __('Ainda não há resíduos publicados') }}</h2>
                    <p>{{ __('Seja a primeira pessoa a publicar um item para reaproveitamento.') }}</p>
                    <a class="recovery-publish-button" href="{{ route('posts.create') }}">{{ __('Anunciar Venda') }}</a>
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

            let selectedCategory = 'all';
            let selectedType = 'all';

            const filterCards = () => {
                const search = document.getElementById('item-search').value.trim().toLocaleLowerCase();
                const location = document.getElementById('item-location-filter').value;

                document.querySelectorAll('.recovery-card').forEach((card) => {
                    const categories = card.dataset.postCategories.split(',').filter(Boolean);
                    const matchesCategory = selectedCategory === 'all' || categories.includes(selectedCategory);
                    const matchesType = selectedType === 'all' || card.dataset.postType === selectedType;
                    const matchesLocation = location === 'all' || card.dataset.postLocation === location;
                    const matchesSearch = !search || card.dataset.postSearch.includes(search);

                    card.hidden = !matchesCategory || !matchesType || !matchesLocation || !matchesSearch;
                });
            };

            document.querySelectorAll('[data-category-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('[data-category-filter]').forEach((item) => item.classList.remove('is-active'));
                    button.classList.add('is-active');

                    selectedCategory = button.dataset.categoryFilter;
                    filterCards();
                });
            });

            document.querySelectorAll('[data-type-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('[data-type-filter]').forEach((item) => item.classList.remove('is-active'));
                    button.classList.add('is-active');
                    selectedType = button.dataset.typeFilter;
                    filterCards();
                });
            });

            document.getElementById('item-search').addEventListener('input', filterCards);
            document.getElementById('item-location-filter').addEventListener('change', filterCards);

            document.querySelectorAll('.recovery-favorite').forEach((button) => {
                button.addEventListener('click', async (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    if (button.dataset.loading === '1') {
                        return;
                    }

                    button.dataset.loading = '1';

                    try {
                        const response = await fetch(button.dataset.saveUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('save-failed');
                        }

                        const data = await response.json();
                        const icon = button.querySelector('span');

                        button.classList.toggle('is-saved', data.salvo);
                        button.setAttribute('aria-pressed', data.salvo ? 'true' : 'false');
                        button.setAttribute('aria-label', data.salvo ? 'Remover item dos salvos' : 'Salvar item');
                        icon.textContent = data.salvo ? '♥' : '♡';
                    } catch (error) {
                        alert('Não foi possível salvar a publicação.');
                    } finally {
                        button.dataset.loading = '0';
                    }
                });
            });
        </script>
    @endpush
</x-layouts::app>