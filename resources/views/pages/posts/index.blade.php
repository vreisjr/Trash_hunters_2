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
            <form method="POST" action="{{ route('posts.store') }}">
                @csrf

                <div class="post-header">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=A8E6A3&color=2b3d2f" alt="{{ auth()->user()->name }}">
                    <textarea name="content" id="nova-postagem-texto" placeholder="{{ __('O que você encontrou hoje? Compartilhe sua missão com a comunidade...') }}" required>{{ old('content') }}</textarea>
                </div>

                <div class="post-actions">
                    <button type="button" disabled title="{{ __('Em breve') }}">
                        <i class="fa-regular fa-image"></i>
                        {{ __('Foto/Vídeo') }}
                    </button>

                    <button type="button" disabled title="{{ __('Em breve') }}">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ __('Local') }}
                    </button>

                    <button type="button" disabled title="{{ __('Em breve') }}">
                        <i class="fa-solid fa-recycle"></i>
                        {{ __('Categoria') }}
                    </button>

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

                    <p>{{ $post->content }}</p>
                </article>
            @empty
                <p class="empty-state">{{ __('Nenhuma postagem ainda. Seja o primeiro a compartilhar uma missão!') }}</p>
            @endforelse
        </section>

    </div>
</x-layouts::app>
