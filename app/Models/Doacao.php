<?php

namespace App\Models;

use App\Enums\StatusDoacao;
use Database\Factories\DoacaoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $coletor_id
 * @property string $nome
 * @property string|null $descricao
 * @property StatusDoacao $status
 * @property Carbon|null $reservado_em
 * @property Carbon|null $coletado_em
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $doador
 * @property-read User|null $coletor
 */
#[Fillable(['user_id', 'coletor_id', 'nome', 'descricao', 'status', 'reservado_em', 'coletado_em'])]
class Doacao extends Model
{
    /** @use HasFactory<DoacaoFactory> */
    use HasFactory;

    protected $table = 'doacoes';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusDoacao::class,
            'reservado_em' => 'datetime',
            'coletado_em' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function doador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function coletor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coletor_id');
    }

    public function getDataColetaAttribute(): ?Carbon
    {
        return $this->coletado_em ?? $this->reservado_em;
    }
}
