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
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=A8E6A3&color=2b3d2f" alt="{{ auth()->user()->name }}">
                    <textarea name="content" id="nova-postagem-texto" placeholder="{{ __('O que você encontrou hoje? Compartilhe sua missão com a comunidade...') }}" required>{{ old('content') }}</textarea>
                </div>

                <div id="media-preview" style="display:none; margin: 8px 0;"></div>

                <input type="file" name="media" id="input-media" accept="image/*,video/*" style="display:none;">

                <input type="hidden" name="latitude" id="input-latitude">
                <input type="hidden" name="longitude" id="input-longitude">
                <input type="hidden" name="endereco" id="input-endereco">
                <input type="hidden" name="categoria_id" id="input-categoria">

                <div class="post-actions" style="position:relative;">
                    <button type="button" id="btn-media">
                        <i class="fa-regular fa-image"></i>
                        <span id="btn-media-label">{{ __('Foto/Vídeo') }}</span>
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

                        <div id="dropdown-categoria" style="display:none; position:absolute; bottom:110%; left:0; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); z-index:50; min-width:160px;">
                            @foreach ($categorias as $categoria)
                                <button type="button" class="opcao-categoria" data-id="{{ $categoria->id }}" data-nome="{{ $categoria->nome }}" data-cor="{{ $categoria->cor }}" style="display:flex; align-items:center; gap:8px; width:100%; text-align:left; padding:8px 14px; border:none; background:none; cursor:pointer;">
                                    <span style="width:10px; height:10px; border-radius:50%; background:{{ $categoria->cor }}; flex-shrink:0;"></span>
                                    {{ $categoria->nome }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="publish">
                        {{ __('Publicar') }}
                    </button>
                </div>
            </form>
        </section>

        {{-- FEED --}}
        <section class="posts-list">
            @forelse ($posts as $post)
                <article class="post">
                    <div class="post-top">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=A8E6A3&color=2b3d2f" alt="{{ $post->user->name }}">
                        <div>
                            <h3>{{ $post->user->name }}</h3>
                            <span>{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div style="margin: 6px 0 8px; display:flex; gap:10px; flex-wrap:wrap;">
                        @if ($post->categoria)
                            <span style="background:{{ $post->categoria->cor }}22; color:{{ $post->categoria->cor }}; border:1px solid {{ $post->categoria->cor }}; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600;">
                                <i class="fa-solid fa-recycle"></i> {{ $post->categoria->nome }}
                            </span>
                        @endif

                        @if ($post->latitude && $post->longitude)
                            <a href="https://www.google.com/maps?q={{ $post->latitude }},{{ $post->longitude }}" target="_blank" style="background:#e3f2fd; color:#1565c0; padding:4px 10px; border-radius:999px; font-size:12px; text-decoration:none;">
                                <i class="fa-solid fa-location-dot"></i> {{ $post->endereco ?? 'Ver no mapa' }}
                            </a>
                        @endif
                    </div>

                    <p>{{ $post->content }}</p>

                    @if ($post->media_path)
                        <div style="margin-top:8px;">
                            @if ($post->media_type === 'video')
                                <video src="{{ $post->media_url }}" controls style="max-width:100%; border-radius:12px;"></video>
                            @else
                                <img src="{{ $post->media_url }}" alt="Mídia da postagem" style="max-width:100%; border-radius:12px;">
                            @endif
                        </div>
                    @endif

                    <div class="comentarios" data-post-id="{{ $post->id }}" style="margin-top:10px; border-top:1px solid #eee; padding-top:8px;">
                        <div class="comentarios-lista" id="comentarios-lista-{{ $post->id }}">
                            @foreach ($post->comentarios as $comentario)
                                @include('pages.posts.partials.comentario', ['comentario' => $comentario])
                            @endforeach
                        </div>

                        <div class="emoji-bar" style="display:flex; gap:12px; padding:8px 0 4px; border-top:1px solid #eee; margin-top:4px; font-size:18px;">
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">❤️</button>
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">🙌</button>
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">🔥</button>
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">👏</button>
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😢</button>
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😍</button>
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😮</button>
                            <button type="button" class="btn-emoji" style="background:none; border:none; cursor:pointer;">😂</button>
                        </div>

                        <form class="form-comentario" data-post-id="{{ $post->id }}" style="display:flex; gap:8px; align-items:center;">
                            @csrf
                            <input type="text" name="texto" placeholder="Adicione um comentário..." maxlength="500" required autocomplete="off" style="flex:1; border:none; padding:8px 4px; font-size:13px; outline:none; background:transparent;">
                            <button type="submit" style="background:none; border:none; color:#2e7d32; font-weight:600; font-size:13px; cursor:pointer;">Publicar</button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="empty-state">{{ __('Nenhuma postagem ainda. Seja o primeiro a compartilhar uma missão!') }}</p>
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

            inputMedia.addEventListener('change', () => {
                const file = inputMedia.files[0];
                if (!file) return;

                btnMediaLabel.textContent = file.name;
                mediaPreview.style.display = 'block';
                mediaPreview.innerHTML = '';

                const url = URL.createObjectURL(file);
                if (file.type.startsWith('video')) {
                    mediaPreview.innerHTML = `<video src="${url}" controls style="max-width:200px; border-radius:8px;"></video>`;
                } else {
                    mediaPreview.innerHTML = `<img src="${url}" style="max-width:200px; border-radius:8px;">`;
                }
            });

            // --- Local ---
            const btnLocal = document.getElementById('btn-local');
            const btnLocalLabel = document.getElementById('btn-local-label');
            const inputLat = document.getElementById('input-latitude');
            const inputLng = document.getElementById('input-longitude');
            const inputEndereco = document.getElementById('input-endereco');

            btnLocal.addEventListener('click', () => {
                if (!navigator.geolocation) {
                    alert('Seu navegador não suporta geolocalização.');
                    return;
                }

                btnLocalLabel.textContent = 'Obtendo local...';

                navigator.geolocation.getCurrentPosition(async (position) => {
                    const { latitude, longitude } = position.coords;
                    inputLat.value = latitude;
                    inputLng.value = longitude;

                    try {
                        const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
                        const json = await resp.json();
                        const endereco = json.display_name ?? `${latitude}, ${longitude}`;
                        inputEndereco.value = endereco;
                        btnLocalLabel.textContent = endereco.length > 25 ? endereco.substring(0, 25) + '...' : endereco;
                    } catch (e) {
                        inputEndereco.value = `${latitude}, ${longitude}`;
                        btnLocalLabel.textContent = 'Local adicionado';
                    }
                }, () => {
                    btnLocalLabel.textContent = 'Local';
                    alert('Não foi possível obter sua localização.');
                });
            });

            // --- Categoria ---
            const btnCategoria = document.getElementById('btn-categoria');
            const dropdownCategoria = document.getElementById('dropdown-categoria');
            const btnCategoriaLabel = document.getElementById('btn-categoria-label');
            const inputCategoria = document.getElementById('input-categoria');

            btnCategoria.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownCategoria.style.display = dropdownCategoria.style.display === 'none' ? 'block' : 'none';
            });

            document.querySelectorAll('.opcao-categoria').forEach(opcao => {
                opcao.addEventListener('click', () => {
                    inputCategoria.value = opcao.dataset.id;
                    btnCategoriaLabel.textContent = opcao.dataset.nome;
                    btnCategoria.style.borderColor = opcao.dataset.cor;
                    btnCategoria.style.color = opcao.dataset.cor;
                    dropdownCategoria.style.display = 'none';
                });
            });

            document.addEventListener('click', () => {
                dropdownCategoria.style.display = 'none';
            });

            // --- Comentários: enviar novo ---
            document.querySelectorAll('.form-comentario').forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const postId = form.dataset.postId;
                    const input = form.querySelector('input[name="texto"]');
                    const texto = input.value.trim();
                    if (!texto) return;

                    try {
                        const resp = await fetch(`/posts/${postId}/comentarios`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ texto }),
                        });

                        if (!resp.ok) throw new Error('Erro ao comentar');
                        const data = await resp.json();

                        const lista = document.getElementById(`comentarios-lista-${postId}`);
                        const div = document.createElement('div');
                        div.className = 'comentario';
                        div.dataset.comentarioId = data.id;
                        div.style.cssText = 'display:flex; gap:10px; padding:8px 0; align-items:flex-start;';
                        div.innerHTML = `
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(data.user_name)}&background=A8E6A3&color=2b3d2f" style="width:32px; height:32px; border-radius:50%; flex-shrink:0; margin-top:2px;">
                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                                    <div style="font-size:14px; line-height:1.4; word-break:break-word;">
                                        <strong style="margin-right:5px;">${data.user_name}</strong>
                                        <span class="comentario-texto">${data.texto}</span>
                                    </div>
                                    <button type="button" class="btn-curtir-comentario" data-id="${data.id}" style="background:none; border:none; cursor:pointer; padding:2px; flex-shrink:0; color:#8e8e8e;">
                                        <i class="fa-regular fa-heart" style="font-size:13px;"></i>
                                    </button>
                                </div>
                                <div style="display:flex; gap:14px; align-items:center; margin-top:4px; flex-wrap:wrap;">
                                    <span style="font-size:11px; color:#8e8e8e;">${data.created_at}</span>
                                    <span class="curtidas-total-label" style="font-size:11px; color:#8e8e8e; font-weight:600; display:none;">
                                        <span class="curtidas-total">0</span> curtidas
                                    </span>
                                    <button type="button" class="btn-editar-comentario" data-id="${data.id}" style="background:none; border:none; cursor:pointer; font-size:11px; color:#8e8e8e; font-weight:600;">Editar</button>
                                    <button type="button" class="btn-apagar-comentario" data-id="${data.id}" style="background:none; border:none; cursor:pointer; font-size:11px; color:#8e8e8e; font-weight:600;">Apagar</button>
                                </div>
                            </div>`;
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
                const btnCurtir = container.querySelector('.btn-curtir-comentario');
                const btnEditar = container.querySelector('.btn-editar-comentario');
                const btnApagar = container.querySelector('.btn-apagar-comentario');

                if (btnCurtir) {
                    btnCurtir.addEventListener('click', async () => {
                        const id = btnCurtir.dataset.id;
                        try {
                            const resp = await fetch(`/comentarios/${id}/curtir`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                            });
                            const data = await resp.json();
                            const icon = btnCurtir.querySelector('i');
                            const label = container.querySelector('.curtidas-total-label');

                            label.style.display = data.total > 0 ? 'inline' : 'none';
                            label.innerHTML = `<span class="curtidas-total">${data.total}</span> curtida${data.total == 1 ? '' : 's'}`;

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
                        const comentarioDiv = btnEditar.closest('.comentario');
                        const textoSpan = comentarioDiv.querySelector('.comentario-texto');
                        const textoAtual = textoSpan.textContent.trim();

                        const input = document.createElement('input');
                        input.type = 'text';
                        input.value = textoAtual;
                        input.maxLength = 500;
                        input.style.cssText = 'font-size:13px; border:1px solid #ccc; border-radius:4px; padding:2px 6px; margin-left:4px; width:70%;';

                        textoSpan.replaceWith(input);
                        input.focus();
                        btnEditar.style.display = 'none';

                        const salvar = async () => {
                            const novoTexto = input.value.trim();
                            if (!novoTexto) return;

                            try {
                                const resp = await fetch(`/comentarios/${btnEditar.dataset.id}`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': getCsrfToken(),
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ texto: novoTexto }),
                                });
                                const data = await resp.json();
                                const novoSpan = document.createElement('span');
                                novoSpan.className = 'comentario-texto';
                                novoSpan.textContent = data.texto;
                                input.replaceWith(novoSpan);
                                btnEditar.style.display = 'inline';
                            } catch (err) {
                                alert('Não foi possível editar o comentário.');
                            }
                        };

                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') salvar();
                        });
                        input.addEventListener('blur', salvar);
                    });
                }

                if (btnApagar) {
                    btnApagar.addEventListener('click', async () => {
                        if (!confirm('Apagar este comentário?')) return;
                        const id = btnApagar.dataset.id;

                        try {
                            await fetch(`/comentarios/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                            });
                            btnApagar.closest('.comentario').remove();
                        } catch (err) {
                            alert('Não foi possível apagar o comentário.');
                        }
                    });
                }
            }

            document.querySelectorAll('.comentario').forEach(ativarEventosComentario);

            // --- Emojis rápidos ---
            document.querySelectorAll('.emoji-bar').forEach(bar => {
                bar.querySelectorAll('.btn-emoji').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const input = bar.nextElementSibling.querySelector('input[name="texto"]');
                        input.value += btn.textContent;
                        input.focus();
                    });
                });
            });
        });
    </script>
    @endpush
</x-layouts::app>