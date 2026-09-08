<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $categoria_id
 * @property string $content
 * @property string|null $media_path
 * @property string|null $media_type
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $endereco
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Categoria|null $categoria
 */
#[Fillable(['user_id', 'categoria_id', 'content', 'media_path', 'media_type', 'latitude', 'longitude', 'endereco'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->media_path ? asset('storage/'.$this->media_path) : null;
    }
    public function comentarios(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(Comentario::class)->latest();
}
}