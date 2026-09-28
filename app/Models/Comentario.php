<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $texto
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 * @property-read User $user
 * @property-read Comentario|null $pai
 * @property-read Collection<int, Comentario> $filhos
 * @property-read Collection<int, ComentarioCurtida> $curtidas
 */
class Comentario extends Model
{
    protected $fillable = ['post_id', 'user_id', 'parent_id', 'texto'];

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Comentario, $this>
     */
    public function pai(): BelongsTo
    {
        return $this->belongsTo(Comentario::class, 'parent_id');
    }

    /**
     * @return HasMany<Comentario, $this>
     */
    public function filhos(): HasMany
    {
        return $this->hasMany(Comentario::class, 'parent_id')->latest();
    }

    /**
     * @return HasMany<ComentarioCurtida, $this>
     */
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
