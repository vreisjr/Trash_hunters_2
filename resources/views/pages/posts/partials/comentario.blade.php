<div class="comentario" data-comentario-id="{{ $comentario->id }}" style="display:flex; gap:10px; padding:8px 0; align-items:flex-start;">
    <img src="https://ui-avatars.com/api/?name={{ urlencode($comentario->user->name) }}&background=A8E6A3&color=2b3d2f" alt="{{ $comentario->user->name }}" style="width:32px; height:32px; border-radius:50%; flex-shrink:0; margin-top:2px;">

    <div style="flex:1; min-width:0;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
            <div style="font-size:14px; line-height:1.4; word-break:break-word;">
                <strong style="margin-right:5px;">{{ $comentario->user->name }}</strong>
                <span class="comentario-texto">{{ $comentario->texto }}</span>
            </div>

            <button type="button" class="btn-curtir-comentario" data-id="{{ $comentario->id }}" style="background:none; border:none; cursor:pointer; padding:2px; flex-shrink:0; color:{{ $comentario->curtidoPor(auth()->id()) ? '#e0245e' : '#8e8e8e' }};">
                <i class="fa-{{ $comentario->curtidoPor(auth()->id()) ? 'solid' : 'regular' }} fa-heart" style="font-size:13px;"></i>
            </button>
        </div>

        <div style="display:flex; gap:14px; align-items:center; margin-top:4px; flex-wrap:wrap;">
            <span style="font-size:11px; color:#8e8e8e;">{{ $comentario->created_at->diffForHumans() }}</span>

            <span class="curtidas-total-label" style="font-size:11px; color:#8e8e8e; font-weight:600; {{ $comentario->curtidas->count() ? '' : 'display:none;' }}">
                <span class="curtidas-total">{{ $comentario->curtidas->count() }}</span> curtida{{ $comentario->curtidas->count() == 1 ? '' : 's' }}
            </span>

            @if ($comentario->user_id === auth()->id())
                <button type="button" class="btn-editar-comentario" data-id="{{ $comentario->id }}" style="background:none; border:none; cursor:pointer; font-size:11px; color:#8e8e8e; font-weight:600;">Editar</button>
                <button type="button" class="btn-apagar-comentario" data-id="{{ $comentario->id }}" style="background:none; border:none; cursor:pointer; font-size:11px; color:#8e8e8e; font-weight:600;">Apagar</button>
            @endif
        </div>
    </div>
</div>