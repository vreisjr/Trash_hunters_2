<x-layouts::app :title="__('Editar post')">

    <div class="preview-container">

        @if ($errors->any())
            <div style="background:#ffe8ec; color:#d81b60; padding:14px 18px; border-radius:12px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- EDITAR POST --}}
        <section class="new-post">

            <div class="post-header">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=A8E6A3&color=2b3d2f"
                    alt="{{ $post->user->name }}"
                >

                <textarea
                    name="content"
                    id="content"
                    form="form-editar-postagem"
                    placeholder="{{ __('Edite o conteúdo da sua denúncia...') }}"
                    required
                >{{ old('content', $post->content) }}</textarea>

            </div>

            {{-- MÍDIA ORIGINAL --}}
            @if ($post->media_path)
                <div style="margin:10px 0;">

                    <p style="font-size:13px; color:#777; margin-bottom:8px;">
                        {{ __('Foto/Vídeo da publicação') }}
                    </p>

                    @if ($post->media_type === 'video')

                        <video
                            src="{{ $post->media_url }}"
                            controls
                            style="max-width:100%; border-radius:12px;"
                        ></video>

                    @else

                        <img
                            src="{{ $post->media_url }}"
                            alt="Mídia da postagem"
                            style="max-width:100%; border-radius:12px;"
                        >

                    @endif

                    <p style="font-size:12px; color:#888; margin-top:6px;">
                        {{ __('A foto ou vídeo não pode ser alterado.') }}
                    </p>

                </div>
            @endif

            {{-- LOCALIZAÇÃO --}}
            @if ($post->latitude && $post->longitude)

                <div style="margin:10px 0;">

                    <a
                        href="https://www.google.com/maps?q={{ $post->latitude }},{{ $post->longitude }}"
                        target="_blank"
                        style="display:inline-block; background:#e3f2fd; color:#1565c0; padding:6px 12px; border-radius:999px; font-size:12px; text-decoration:none;"
                    >
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $post->endereco ?? 'Ver localização no mapa' }}
                    </a>

                </div>

            @endif

            {{-- CATEGORIAS --}}
            @if ($post->categorias->count())

                <div style="margin:10px 0; display:flex; gap:10px; flex-wrap:wrap;">

                    @foreach ($post->categorias as $categoria)

                        <span
                            style="background:{{ $categoria->cor }}22; color:{{ $categoria->cor }}; border:1px solid {{ $categoria->cor }}; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600;"
                        >
                            <i class="fa-solid fa-recycle"></i>
                            {{ $categoria->nome }}
                        </span>

                    @endforeach

                </div>

            @endif

            {{-- FORMULÁRIO --}}
            <form
                method="POST"
                action="{{ route('posts.update', $post) }}"
                id="form-editar-postagem"
            >

                @csrf
                @method('PUT')

                <div
                    class="post-actions"
                    style="position:relative; margin-top:15px;"
                >

                    <a
                        href="{{ route('posts.index') }}"
                        style="display:inline-block; padding:10px 18px; border:1px solid #ddd; border-radius:8px; text-decoration:none; color:#555; background:#fff;"
                    >
                        {{ __('Cancelar') }}
                    </a>

                    <button
                        type="submit"
                        class="publish"
                    >
                        {{ __('Salvar alteração') }}
                    </button>

                </div>

            </form>

        </section>

    </div>

</x-layouts::app>