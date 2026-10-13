<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coleta extends Model
{
    protected $fillable = ['doador_id', 'coletor_id', 'post_id', 'item', 'data_hora'];

    protected $casts = ['data_hora' => 'datetime'];

    public function doador()
    {
        return $this->belongsTo(User::class, 'doador_id');
    }

    public function coletor()
    {
        return $this->belongsTo(User::class, 'coletor_id');
    }
}