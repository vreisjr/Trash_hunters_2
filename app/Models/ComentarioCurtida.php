<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $comentario_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ComentarioCurtida extends Model
{
    protected $fillable = ['comentario_id', 'user_id'];

    /**
     * @return BelongsTo<Comentario, $this>
     */
    public function comentario(): BelongsTo
    {
        return $this->belongsTo(Comentario::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
