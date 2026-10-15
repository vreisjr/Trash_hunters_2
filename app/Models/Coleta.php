<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coleta extends Model
{
    protected $fillable = ['doador_id', 'coletor_id', 'post_id', 'item', 'data_hora'];

    protected $casts = ['data_hora' => 'datetime'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function doador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doador_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function coletor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coletor_id');
    }
}
