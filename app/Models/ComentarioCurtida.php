<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComentarioCurtida extends Model
{
    protected $fillable = ['comentario_id', 'user_id'];

    public function comentario(): BelongsTo
    {
        return $this->belongsTo(Comentario::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
