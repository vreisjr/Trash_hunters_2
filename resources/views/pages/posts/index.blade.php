<x-layouts::app :title="__('Feed')">

    <div class="preview-container">

        @session('status')
            <div style="background:#e8f8e8; color:#2e7d32; padding:14px 18px; border-radius:12px;">
                {{ $value }}
            </div>
        @endsession

        @if ($errors->any())
            <div style="background:#ffe8ec; color:#d81b60; padding:14px 18px; border-radius:12px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- NOVA POSTAGEM --}}

        <section class="new-post">

            <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="form-nova-postagem">

                @csrf

                <div class="post-header">

                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=A8E6A3&color=2b3d2f"
                        alt="{{ auth()->user()->name }}"
                    >

                    <textarea
                        name="content"
                        id="nova-postagem-texto"
                        placeholder="{{ __('O que você encontrou hoje? Compartilhe sua missão com a comunidade...') }}"
                        required
                    >{{ old('content') }}</textarea>

                </div>

                <div id="media-preview" style="display:none; margin: 8px 0;"></div>

                <input
                    type="file"
                    name="media[]"
                    id="input-media"
                    accept="image/*,video/*"
                    multiple
                    style="display:none;"
                >

                <input type="hidden" name="latitude" id="input-latitude">
                <input type="hidden" name="longitude" id="input-longitude">
                <input type="hidden" name="address" id="input-address">

                <div class="post-actions" style="position:relative;">

                    <button type="button" id="btn-media">
                        <i class="fa-solid fa-photo-film"></i>
                        <span id="btn-media-label">{{ __('Mídia') }}</span>
                    </button>

                    <button type="button" id="btn-local">
                        <i class="fa-solid fa-location-dot"></i>
                        <span id="btn-local-label">{{ __('Local') }}</span>
                    </button>

                    <div style="position:relative; display:inline-block;">

                        <button type="button" id="btn-categoria">
                            <i class="fa-solid fa-recycle"></i>
                            <span id="btn-categoria-label">{{ __('Categoria') }}</span>
                        </button>

                        <div
                            id="dropdown-categoria"
                            style="display:none; position:absolute; bottom:110%; left:0; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); z-index:50; min-width:200px;"
                        >

                            @foreach ($categorias as $categoria)

                                <label style="display:flex; align-items:center; gap:8px; width:100%; padding:8px 14px; cursor:pointer;">

                                    <input
                                        type="checkbox"
                                        name="categoria_ids[]"
                                        value="{{ $categoria->id }}"
                                        class="checkbox-categoria"
                                        data-nome="{{ $categoria->nome }}"
                                        data-cor="{{ $categoria->cor }}"
                                    >

                                    <span
                                        style="width:10px; height:10px; border-radius:50%; background:{{ $categoria->cor }}; flex-shrink:0;"
                                    ></span>

                                    {{ $categoria->nome }}

                                </label>

                            @endforeach

                            <div style="padding:8px 14px; border-top:1px solid #eee; text-align:right;">

                                <button
                                    type="button"
                                    id="btn-fechar-categorias"
                                    style="background:none; border:none; color:#2e7d32; font-weight:600; cursor:pointer; font-size:12px;"
                                >
                                    {{ __('Concluir') }}
                                </button>

                            </div>

                        </div>

                    </div>

                    <button type="submit" class="publish">
                        {{ __('Publicar') }}
                    </button>

                </div>

            </form>

            {{-- Modal de edição de localização --}}

            <div
                id="modal-local"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:100; align-items:center; justify-content:center;"
            >

                <div style="background:#fff; border-radius:12px; padding:20px; width:90%; max-width:420px;">

                    <h3 style="margin-bottom:10px; font-size:16px;">
                        {{ __('Localização da denúncia') }}
                    </h3>

                    <input
                        type="text"
                        id="input-busca-endereco"
                        placeholder="{{ __('Digite um endereço...') }}"
                        autocomplete="off"
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; font-size:14px;"
                    >

                    <div
                        id="lista-sugestoes"
                        style="max-height:180px; overflow-y:auto; margin-top:6px;"
                    ></div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">

                        <button
                            type="button"
                            id="btn-cancelar-local"
                            style="padding:8px 16px; border:none; background:#eee; border-radius:8px; cursor:pointer;"
                        >
                            {{ __('Cancelar') }}
                        </button>

                        <button
                            type="button"
                            id="btn-confirmar-local"
                            style="padding:8px 16px; border:none; background:#2e7d32; color:#fff; border-radius:8px; cursor:pointer;"
                        >
                            {{ __('Usar esta localização') }}
                        </button>

                    </div>

                </div>

            </div>

            {{-- Modal de confirmação de divergência --}}

            <div
                id="modal-confirmacao-local"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:110; align-items:center; justify-content:center;"
            >

                <div
                    style="background:#fff; border-radius:12px; padding:20px; width:90%; max-width:380px; text-align:center;"
                >

                    <p style="font-size:14px; margin-bottom:18px;">
                        {{ __(' Deseja editar a localização?') }}
                    </p>

                    <div style="display:flex; justify-content:center; gap:12px;">

                        <button
                            type="button"
                            id="btn-confirmacao-nao"
                            style="padding:8px 20px; border:none; background:#eee; border-radius:8px; cursor:pointer;"
                        >
                            {{ __('Não') }}
                        </button>

                        <button
                            type="button"
                            id="btn-confirmacao-sim"
                            style="padding:8px 20px; border:none; background:#2e7d32; color:#fff; border-radius:8px; cursor:pointer;"
                        >
                            {{ __('Sim') }}
                        </button>

                    </div>

                </div>

            </div>

        </section>

        {{-- FEED --}}

        <section class="posts-list">

            @forelse ($posts as $post)

                <article class="post">

                    <div class="post-top">

                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=A8E6A3&color=2b3d2f"
                            alt="{{ $post->user->name }}"
                        >

                        <div>

                            <h3>{{ $post->user->name }}</h3>

                            <span>
                                {{ $post->created_at->diffForHumans() }}
                            </span>

                        </div>

                        {{-- AÇÃO: EDITAR (curtir foi para a barra abaixo do post, estilo Instagram) --}}

                        @if ($post->user_id === auth()->id() && $post->edited_at === null)

                            <a
                                href="{{ route('posts.edit', $post) }}"
                                style="margin-left:auto; background:none; border:none; cursor:pointer; padding:2px; color:#2e7d32; font-size:12px; font-weight:600; text-decoration:none;"
                            >
                                Editar
                            </a>

                        @endif

                    </div>

                    <div style="margin:6px 0 8px; display:flex; gap:10px; flex-wrap:wrap;">

                        @foreach ($post->categorias as $categoria)

                            <span
                                style="background:{{ $categoria->cor }}22; color:{{ $categoria->cor }}; border:1px solid {{ $categoria->cor }}; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600;"
                            >

                                <i class="fa-solid fa-recycle"></i>
                                {{ $categoria->nome }}

                            </span>

                        @endforeach

                        @if ($post->latitude && $post->longitude)

                            <a
                                href="https://www.google.com/maps?q={{ $post->latitude }},{{ $post->longitude }}"
                                target="_blank"
                                style="background:#e3f2fd; color:#1565c0; padding:4px 10px; border-radius:999px; font-size:12px; text-decoration:none;"
                            >

                                <i class="fa-solid fa-location-dot"></i>
                                {{ $post->address ?? 'Ver no mapa' }}

                            </a>

                        @endif

                    </div>

                    <p>{{ $post->content }}</p>

                    @if ($post->attachments->isNotEmpty())

                        <div class="post-attachments-grid">

                            @foreach ($post->attachments as $attachment)

                                @if ($attachment->file_type === 'video')

                                    <video
                                        src="{{ asset('storage/'.$attachment->file_path) }}"
                                        controls
                                        class="post-attachment-video"
                                    ></video>

                                @else

                                    <img
                                        src="{{ asset('storage/'.$attachment->file_path) }}"
                                        alt="Imagem da postagem"
                                        class="post-attachment-image"
                                    >

                                @endif

                            @endforeach

                        </div>

                    @endif

                    {{-- BARRA DE AÇÕES ESTILO INSTAGRAM: curtir + balão de comentários --}}

                    <div
                        class="post-action-bar"
                        style="display:flex; align-items:center; gap:16px; margin-top:10px; padding-top:10px; border-top:1px solid #eee;"
                    >

                        <button
                            type="button"
                            class="btn-curtir-post"
                            data-id="{{ $post->id }}"
                            style="background:none; border:none; cursor:pointer; padding:2px; line-height:1; color:{{ $post->curtidoPor(auth()->id()) ? '#e0245e' : '#8e8e8e' }};"
                        >

                            <i
                                class="fa-{{ $post->curtidoPor(auth()->id()) ? 'solid' : 'regular' }} fa-heart"
                                style="font-size:22px;"
                            ></i>

                        </button>

                        <button
                            type="button"
                            class="btn-toggle-comentarios"
                            data-post-id="{{ $post->id }}"
                            style="background:none; border:none; cursor:pointer; padding:2px; line-height:1; color:#8e8e8e;"
                        >

                            <i class="fa-regular fa-comment" style="font-size:22px;"></i>

                        </button>

                    </div>

                    {{-- CONTAGEM DE CURTIDAS, embaixo da barra de ações (igual Instagram) --}}

                    <div
                        class="curtidas-total-post-label"
                        style="font-size:13px; color:#262626; font-weight:600; margin-top:6px; {{ $post->curtidas->count() ? '' : 'display:none;' }}"
                    >

                        <span class="curtidas-total-post">{{ $post->curtidas->count() }}</span> curtida{{ $post->curtidas->count() == 1 ? '' : 's' }}

                    </div>

                    <div
                        class="comentarios"
                        data-post-id="{{ $post->id }}"
                    >

                        {{-- PAINEL DE COMENTÁRIOS: começa escondido, o balão acima abre/fecha --}}

                        <div
                            class="comentarios-painel"
                            id="comentarios-painel-{{ $post->id }}"
                            style="display:none; margin-top:10px; border-top:1px solid #eee; padding-top:8px;"
                        >

                            <div
                                class="comentarios-lista"
                                id="comentarios-lista-{{ $post->id }}"
                            >

                                @foreach ($post->comentarios as $comentario)

                                    @include(
                                        'pages.posts.partials.comentario',
                                        ['comentario' => $comentario]
                                    )

                                @endforeach

                            </div>

                            <div
                                class="emoji-bar"
                                style="display:flex; gap:12px; padding:8px 0 4px; border-top:1px solid #eee; margin-top:4px; font-size:18px;"
                            >

                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">❤️</button>
                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">🙌</button>
                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">🔥</button>
                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">👏</button>
                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😢</button>
                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😍</button>
                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😮</button>
                                <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😂</button>

                            </div>

                            <form
                                class="form-comentario"
                                data-post-id="{{ $post->id }}"
                                style="display:flex; gap:8px; align-items:center;"
                            >

                                @csrf

                                <input
                                    type="text"
                                    name="texto"
                                    placeholder="Adicione um comentário..."
                                    maxlength="500"
                                    required
                                    autocomplete="off"
                                    style="flex:1; border:none; padding:8px 4px; font-size:13px; outline:none; background:transparent;"
                                >

                                <button
                                    type="submit"
                                    style="background:none; border:none; color:#2e7d32; font-weight:600; font-size:13px; cursor:pointer;"
                                >
                                    Publicar
                                </button>

                            </form>

                        </div>

                    </div>

                </article>

            @empty

                <p class="empty-state">
                    {{ __('Nenhuma postagem ainda. Seja o primeiro a compartilhar uma missão!') }}
                </p>

            @endforelse

        </section>

    </div>

    @push('scripts')

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            function getCsrfToken() {

                const input = document.querySelector('input[name="_token"]');

                return input ? input.value : '';

            }

            // --- Foto/Vídeo ---

            const btnMedia = document.getElementById('btn-media');
            const inputMedia = document.getElementById('input-media');
            const btnMediaLabel = document.getElementById('btn-media-label');
            const mediaPreview = document.getElementById('media-preview');

            btnMedia.addEventListener('click', () => inputMedia.click());

            function renderMediaPreview() {
                const files = Array.from(inputMedia.files);

                if (files.length === 0) {
                    mediaPreview.style.display = 'none';
                    mediaPreview.innerHTML = '';

                    return;
                }

                btnMediaLabel.textContent = `${files.length} mídia${files.length > 1 ? 's' : ''}`;

                mediaPreview.style.display = 'block';
                mediaPreview.innerHTML = '';

                files.forEach((file) => {
                    const element = file.type.startsWith('video/')
                        ? document.createElement('video')
                        : document.createElement('img');

                    element.src = URL.createObjectURL(file);
                    element.alt = file.name;
                    element.style.cssText = 'width:120px; height:90px; object-fit:cover; border-radius:8px; margin-right:8px;';

                    if (element.tagName === 'VIDEO') {
                        element.controls = true;
                    }

                    mediaPreview.appendChild(element);
                });
            }

            inputMedia.addEventListener('change', renderMediaPreview);

            // --- Local ---

            const btnLocal = document.getElementById('btn-local');
            const btnLocalLabel = document.getElementById('btn-local-label');

            const inputLat = document.getElementById('input-latitude');
            const inputLng = document.getElementById('input-longitude');
            const inputAddress = document.getElementById('input-address');

            const modalLocal = document.getElementById('modal-local');
            const modalConfirmacao = document.getElementById('modal-confirmacao-local');

            const inputBusca = document.getElementById('input-busca-endereco');
            const listaSugestoes = document.getElementById('lista-sugestoes');

            let gpsLat = null;
            let gpsLng = null;
            let gpsEndereco = null;

            let tempLat = null;
            let tempLng = null;
            let tempEndereco = null;

            let debounceTimer = null;

            btnLocal.addEventListener('click', () => {

                if (!navigator.geolocation) {

                    alert('Seu navegador não suporta geolocalização.');

                    return;

                }

                btnLocalLabel.textContent = 'Obtendo local...';

                navigator.geolocation.getCurrentPosition(async (position) => {

                    gpsLat = position.coords.latitude;
                    gpsLng = position.coords.longitude;

                    try {

                        const resp = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${gpsLat}&lon=${gpsLng}`
                        );

                        const json = await resp.json();

                        gpsEndereco =
                            json.display_name ?? `${gpsLat}, ${gpsLng}`;

                    } catch (e) {

                        gpsEndereco = `${gpsLat}, ${gpsLng}`;

                    }

                    inputBusca.value = gpsEndereco;

                    tempLat = gpsLat;
                    tempLng = gpsLng;
                    tempEndereco = gpsEndereco;

                    listaSugestoes.innerHTML = '';

                    modalLocal.style.display = 'flex';

                    btnLocalLabel.textContent = 'Local';

                }, () => {

                    btnLocalLabel.textContent = 'Local';

                    alert('Não foi possível obter sua localização.');

                });

            });

            // Autocomplete

            inputBusca.addEventListener('input', () => {

                clearTimeout(debounceTimer);

                const termo = inputBusca.value.trim();

                if (termo.length < 3) {

                    listaSugestoes.innerHTML = '';

                    return;

                }

                debounceTimer = setTimeout(async () => {

                    try {

                        const resp = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(termo)}&limit=5`
                        );

                        const resultados = await resp.json();

                        listaSugestoes.innerHTML = '';

                        resultados.forEach(item => {

                            const div = document.createElement('div');

                            div.textContent = item.display_name;

                            div.style.cssText =
                                'padding:8px; font-size:13px; cursor:pointer; border-bottom:1px solid #eee;';

                            div.addEventListener('click', () => {

                                inputBusca.value = item.display_name;

                                tempLat = parseFloat(item.lat);
                                tempLng = parseFloat(item.lon);
                                tempEndereco = item.display_name;

                                listaSugestoes.innerHTML = '';

                            });

                            listaSugestoes.appendChild(div);

                        });

                    } catch (e) {

                        // Falha silenciosa na busca de sugestões

                    }

                }, 400);

            });

            inputBusca.addEventListener('change', () => {

                if (inputBusca.value.trim() !== tempEndereco) {

                    tempEndereco = inputBusca.value.trim();

                }

            });

            document
                .getElementById('btn-cancelar-local')
                .addEventListener('click', () => {

                    modalLocal.style.display = 'none';

                });

            document
                .getElementById('btn-confirmar-local')
                .addEventListener('click', () => {

                    const enderecoDigitado = inputBusca.value.trim();

                    if (enderecoDigitado === gpsEndereco) {

                        aplicarLocalizacao(
                            gpsLat,
                            gpsLng,
                            gpsEndereco
                        );

                        modalLocal.style.display = 'none';

                        return;

                    }

                    tempEndereco = enderecoDigitado;

                    modalConfirmacao.style.display = 'flex';

                });

            document
                .getElementById('btn-confirmacao-sim')
                .addEventListener('click', () => {

                    aplicarLocalizacao(
                        tempLat,
                        tempLng,
                        tempEndereco
                    );

                    modalConfirmacao.style.display = 'none';
                    modalLocal.style.display = 'none';

                });

            document
                .getElementById('btn-confirmacao-nao')
                .addEventListener('click', () => {

                    aplicarLocalizacao(
                        gpsLat,
                        gpsLng,
                        gpsEndereco
                    );

                    modalConfirmacao.style.display = 'none';
                    modalLocal.style.display = 'none';

                });

            function aplicarLocalizacao(lat, lng, endereco) {

                inputLat.value = lat ?? '';
                inputLng.value = lng ?? '';
                inputAddress.value = endereco ?? '';

                btnLocalLabel.textContent =
                    endereco && endereco.length > 25
                        ? endereco.substring(0, 25) + '...'
                        : (endereco || 'Local');

            }

            // --- Categoria ---

            const btnCategoria = document.getElementById('btn-categoria');
            const dropdownCategoria = document.getElementById('dropdown-categoria');
            const btnCategoriaLabel = document.getElementById('btn-categoria-label');

            const checkboxesCategoria =
                document.querySelectorAll('.checkbox-categoria');

            btnCategoria.addEventListener('click', (e) => {

                e.stopPropagation();

                dropdownCategoria.style.display =
                    dropdownCategoria.style.display === 'none'
                        ? 'block'
                        : 'none';

            });

            dropdownCategoria.addEventListener(
                'click',
                (e) => e.stopPropagation()
            );

            function atualizarLabelCategoria() {

                const marcadas =
                    Array.from(checkboxesCategoria)
                        .filter(c => c.checked);

                if (marcadas.length === 0) {

                    btnCategoriaLabel.textContent = 'Categoria';
                    btnCategoria.style.borderColor = '';
                    btnCategoria.style.color = '';

                } else if (marcadas.length === 1) {

                    btnCategoriaLabel.textContent =
                        marcadas[0].dataset.nome;

                    btnCategoria.style.borderColor =
                        marcadas[0].dataset.cor;

                    btnCategoria.style.color =
                        marcadas[0].dataset.cor;

                } else {

                    btnCategoriaLabel.textContent =
                        `${marcadas.length} categorias`;

                    btnCategoria.style.borderColor = '#2e7d32';
                    btnCategoria.style.color = '#2e7d32';

                }

            }

            checkboxesCategoria.forEach(checkbox => {

                checkbox.addEventListener(
                    'change',
                    atualizarLabelCategoria
                );

            });

            document
                .getElementById('btn-fechar-categorias')
                .addEventListener('click', () => {

                    dropdownCategoria.style.display = 'none';

                });

            document.addEventListener('click', () => {

                dropdownCategoria.style.display = 'none';

            });

            // --- Curtir publicação ---

            function aplicarEstadoCurtidaPost(icon, btn, label, curtido, total) {

                label.style.display =
                    total > 0 ? 'block' : 'none';

                label.innerHTML =
                    `<span class="curtidas-total-post">${total}</span> curtida${total == 1 ? '' : 's'}`;

                if (curtido) {

                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');

                    btn.style.color = '#e0245e';

                } else {

                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');

                    btn.style.color = '#8e8e8e';

                }

            }

            document.querySelectorAll('.btn-curtir-post').forEach(btn => {

                btn.addEventListener('click', async () => {

                    // Evita clique duplo enquanto a requisição anterior não termina

                    if (btn.dataset.loading === '1') return;

                    btn.dataset.loading = '1';

                    const id = btn.dataset.id;

                    const container = btn.closest('.post');
                    const icon = btn.querySelector('i');

                    const label = container.querySelector(
                        '.curtidas-total-post-label'
                    );

                    const totalSpan = label.querySelector(
                        '.curtidas-total-post'
                    );

                    // Estado antes do clique, para poder desfazer se der erro

                    const curtidoAntes = icon.classList.contains('fa-solid');

                    const totalAntes = parseInt(
                        (totalSpan ? totalSpan.textContent : '0').trim()
                    ) || 0;

                    // --- Atualização otimista: muda a tela IMEDIATAMENTE ---

                    const curtidoOtimista = !curtidoAntes;

                    const totalOtimista = curtidoOtimista
                        ? totalAntes + 1
                        : Math.max(totalAntes - 1, 0);

                    aplicarEstadoCurtidaPost(
                        icon,
                        btn,
                        label,
                        curtidoOtimista,
                        totalOtimista
                    );

                    try {

                        const resp = await fetch(
                            `/posts/${id}/curtir`,
                            {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Accept': 'application/json'
                                },
                            }
                        );

                        if (!resp.ok) {
                            throw new Error('Erro ao curtir');
                        }

                        const data = await resp.json();

                        // Confirma com o valor real do servidor

                        aplicarEstadoCurtidaPost(
                            icon,
                            btn,
                            label,
                            data.curtido,
                            data.total
                        );

                    } catch (err) {

                        // Desfaz a atualização otimista, já que deu erro

                        aplicarEstadoCurtidaPost(
                            icon,
                            btn,
                            label,
                            curtidoAntes,
                            totalAntes
                        );

                        alert('Não foi possível curtir a postagem.');

                    } finally {

                        btn.dataset.loading = '0';

                    }

                });

            });

            // --- Abrir/fechar comentários (balão) ---

            document.querySelectorAll('.btn-toggle-comentarios').forEach(btn => {

                btn.addEventListener('click', () => {

                    const postId = btn.dataset.postId;

                    const painel = document.getElementById(
                        `comentarios-painel-${postId}`
                    );

                    const icon = btn.querySelector('i');

                    const aberto = painel.style.display !== 'none';

                    if (aberto) {

                        painel.style.display = 'none';

                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');

                        btn.style.color = '#8e8e8e';

                    } else {

                        painel.style.display = 'block';

                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');

                        btn.style.color = '#262626';

                        const input = painel.querySelector(
                            'input[name="texto"]'
                        );

                        if (input) {
                            input.focus();
                        }

                    }

                });

            });

            // --- Comentários: enviar novo ---

            document.querySelectorAll('.form-comentario').forEach(form => {

                form.addEventListener('submit', async (e) => {

                    e.preventDefault();

                    const postId = form.dataset.postId;

                    const input =
                        form.querySelector('input[name="texto"]');

                    const texto = input.value.trim();

                    if (!texto) return;

                    try {

                        const resp = await fetch(
                            `/posts/${postId}/comentarios`,
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    texto
                                }),
                            }
                        );

                        if (!resp.ok) {
                            throw new Error('Erro ao comentar');
                        }

                        const data = await resp.json();

                        const lista =
                            document.getElementById(
                                `comentarios-lista-${postId}`
                            );

                        const div =
                            document.createElement('div');

                        div.className = 'comentario';
                        div.dataset.comentarioId = data.id;

                        div.style.cssText =
                            'display:flex; gap:10px; padding:8px 0; align-items:flex-start;';

                        div.innerHTML = `

                            <img
                                src="https://ui-avatars.com/api/?name=${encodeURIComponent(data.user_name)}&background=A8E6A3&color=2b3d2f"
                                style="width:32px; height:32px; border-radius:50%; flex-shrink:0; margin-top:2px;"
                            >

                            <div style="flex:1; min-width:0;">

                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">

                                    <div style="font-size:14px; line-height:1.4; word-break:break-word;">

                                        <strong style="margin-right:5px;">
                                            ${data.user_name}
                                        </strong>

                                        <span class="comentario-texto">
                                            ${data.texto}
                                        </span>

                                    </div>

                                    <button
                                        type="button"
                                        class="btn-curtir-comentario"
                                        data-id="${data.id}"
                                        style="background:none; border:none; cursor:pointer; padding:2px; flex-shrink:0; color:#8e8e8e;"
                                    >

                                        <i
                                            class="fa-regular fa-heart"
                                            style="font-size:13px;"
                                        ></i>

                                    </button>

                                </div>

                                <div style="display:flex; gap:14px; align-items:center; margin-top:4px; flex-wrap:wrap;">

                                    <span style="font-size:11px; color:#8e8e8e;">
                                        ${data.created_at}
                                    </span>

                                    <span
                                        class="curtidas-total-label"
                                        style="font-size:11px; color:#8e8e8e; font-weight:600; display:none;"
                                    >
                                        <span class="curtidas-total">0</span>
                                        curtidas
                                    </span>

                                    <button
                                        type="button"
                                        class="btn-editar-comentario"
                                        data-id="${data.id}"
                                        style="background:none; border:none; cursor:pointer; font-size:11px; color:#8e8e8e; font-weight:600;"
                                    >
                                        Editar
                                    </button>

                                    <button
                                        type="button"
                                        class="btn-apagar-comentario"
                                        data-id="${data.id}"
                                        style="background:none; border:none; cursor:pointer; font-size:11px; color:#8e8e8e; font-weight:600;"
                                    >
                                        Apagar
                                    </button>

                                </div>

                            </div>

                        `;

                        lista.appendChild(div);

                        input.value = '';

                        ativarEventosComentario(div);

                    } catch (err) {

                        alert('Não foi possível publicar o comentário.');

                    }

                });

            });

            // --- Comentários: curtir / editar / apagar ---

            function ativarEventosComentario(container) {

                const btnCurtir =
                    container.querySelector('.btn-curtir-comentario');

                const btnEditar =
                    container.querySelector('.btn-editar-comentario');

                const btnApagar =
                    container.querySelector('.btn-apagar-comentario');

                if (btnCurtir) {

                    btnCurtir.addEventListener('click', async () => {

                        const id = btnCurtir.dataset.id;

                        try {

                            const resp = await fetch(
                                `/comentarios/${id}/curtir`,
                                {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': getCsrfToken(),
                                        'Accept': 'application/json'
                                    },
                                }
                            );

                            const data = await resp.json();

                            const icon =
                                btnCurtir.querySelector('i');

                            const label =
                                container.querySelector(
                                    '.curtidas-total-label'
                                );

                            label.style.display =
                                data.total > 0 ? 'inline' : 'none';

                            label.innerHTML =
                                `<span class="curtidas-total">${data.total}</span> curtida${data.total == 1 ? '' : 's'}`;

                            if (data.curtido) {

                                icon.classList.remove('fa-regular');
                                icon.classList.add('fa-solid');

                                btnCurtir.style.color = '#e0245e';

                            } else {

                                icon.classList.remove('fa-solid');
                                icon.classList.add('fa-regular');

                                btnCurtir.style.color = '#8e8e8e';

                            }

                        } catch (err) {

                            alert('Não foi possível curtir o comentário.');

                        }

                    });

                }

                if (btnEditar) {

                    btnEditar.addEventListener('click', () => {

                        const comentarioDiv =
                            btnEditar.closest('.comentario');

                        const textoSpan =
                            comentarioDiv.querySelector(
                                '.comentario-texto'
                            );

                        const textoAtual =
                            textoSpan.textContent.trim();

                        const input =
                            document.createElement('input');

                        input.type = 'text';
                        input.value = textoAtual;
                        input.maxLength = 500;

                        input.style.cssText =
                            'font-size:13px; border:1px solid #ccc; border-radius:4px; padding:2px 6px; margin-left:4px; width:70%;';

                        textoSpan.replaceWith(input);

                        input.focus();

                        btnEditar.style.display = 'none';

                        const salvar = async () => {

                            const novoTexto =
                                input.value.trim();

                            if (!novoTexto) return;

                            try {

                                const resp = await fetch(
                                    `/comentarios/${btnEditar.dataset.id}`,
                                    {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': getCsrfToken(),
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            texto: novoTexto
                                        }),
                                    }
                                );

                                const data = await resp.json();

                                const novoSpan =
                                    document.createElement('span');

                                novoSpan.className =
                                    'comentario-texto';

                                novoSpan.textContent =
                                    data.texto;

                                input.replaceWith(novoSpan);

                                btnEditar.style.display = 'inline';

                            } catch (err) {

                                alert('Não foi possível editar o comentário.');

                            }

                        };

                        input.addEventListener(
                            'keydown',
                            (e) => {

                                if (e.key === 'Enter') {
                                    salvar();
                                }

                            }
                        );

                        input.addEventListener(
                            'blur',
                            salvar
                        );

                    });

                }

                if (btnApagar) {

                    btnApagar.addEventListener('click', async () => {

                        if (!confirm('Apagar este comentário?')) {
                            return;
                        }

                        const id = btnApagar.dataset.id;

                        try {

                            await fetch(
                                `/comentarios/${id}`,
                                {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': getCsrfToken(),
                                        'Accept': 'application/json'
                                    },
                                }
                            );

                            btnApagar
                                .closest('.comentario')
                                .remove();

                        } catch (err) {

                            alert('Não foi possível apagar o comentário.');

                        }

                    });

                }

            }

            document
                .querySelectorAll('.comentario')
                .forEach(ativarEventosComentario);

            // --- Emojis rápidos ---

            document.querySelectorAll('.emoji-bar').forEach(bar => {

                bar.querySelectorAll('.btn-emoji').forEach(btn => {

                    btn.addEventListener('click', () => {

                        const input =
                            bar.nextElementSibling.querySelector(
                                'input[name="texto"]'
                            );

                        input.value += btn.textContent;

                        input.focus();

                    });

                });

            });

        });

    </script>

    @endpush

</x-layouts::app>