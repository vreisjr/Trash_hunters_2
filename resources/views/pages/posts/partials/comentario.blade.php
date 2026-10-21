<div class="comentario" data-comentario-id="{{ $comentario->id }}">
    <img class="comentario-avatar" src="https://ui-avatars.com/api/?name={{ urlencode($comentario->user->name) }}&background=A8E6A3&color=2b3d2f" alt="{{ $comentario->user->name }}">

    <div class="comentario-conteudo">
        <div class="comentario-linha">
            <div class="comentario-mensagem">
                <strong>{{ $comentario->user->name }}</strong>
                <span class="comentario-texto">{{ $comentario->texto }}</span>
            </div>

        </div>

        <div class="comentario-acoes">
            <span>{{ $comentario->created_at->diffForHumans() }}</span>

            <button type="button" class="btn-curtir-comentario" data-id="{{ $comentario->id }}" aria-label="Curtir comentário" style="color:{{ $comentario->curtidoPor(auth()->id()) ? '#e0245e' : '#8e8e8e' }};">
                <i class="fa-{{ $comentario->curtidoPor(auth()->id()) ? 'solid' : 'regular' }} fa-heart"></i>
                <span class="curtidas-total-label" {{ $comentario->curtidas->count() ? '' : 'style=display:none;' }}>
                    <span class="curtidas-total">{{ $comentario->curtidas->count() }}</span>
                </span>
            </button>

            <button type="button" class="btn-responder-comentario" data-id="{{ $comentario->id }}">Responder</button>

            @if ($comentario->user_id === auth()->id())
                <button type="button" class="btn-editar-comentario" data-id="{{ $comentario->id }}">Editar</button>
                <button type="button" class="btn-apagar-comentario" data-id="{{ $comentario->id }}">Apagar</button>
            @endif
        </div>

        <form class="form-resposta" data-post-id="{{ $comentario->post_id }}" data-parent-id="{{ $comentario->id }}" style="display:none;">
            @csrf
            <input type="text" name="texto" placeholder="Responda a este comentário..." maxlength="500" required autocomplete="off">
            <button type="submit">Publicar</button>
        </form>

        @if ($comentario->filhos->isNotEmpty())
            <div class="respostas-lista">
                <button type="button" class="btn-ver-respostas" aria-expanded="true">
                    <i class="fa-solid fa-caret-down"></i>
                    {{ $comentario->filhos->count() }} {{ $comentario->filhos->count() === 1 ? 'resposta' : 'respostas' }}
                </button>
                @foreach ($comentario->filhos as $resposta)
                    @include('pages.posts.partials.comentario', ['comentario' => $resposta])
                @endforeach
            </div>
        @else
            <div class="respostas-lista"></div>
        @endif
    </div>
</div>