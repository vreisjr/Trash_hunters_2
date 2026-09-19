<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nome
 * @property string $cor
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Categoria extends Model
{
    protected $fillable = ['nome', 'cor'];

    /**
     * @return BelongsToMany<Post, $this>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_categoria');
    }
}
