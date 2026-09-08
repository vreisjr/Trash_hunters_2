<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comentario extends Model
{
    protected $fillable = ['post_id', 'user_id', 'texto'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function curtidas(): HasMany
    {
        return $this->hasMany(ComentarioCurtida::class);
    }

    public function curtidoPor(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return $this->curtidas->contains('user_id', $userId);
    }
}
