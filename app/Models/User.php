<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'district', 'tipo_perfil'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Tipos de perfil disponíveis na plataforma, com o nome
     * bonito que deve ser exibido para cada valor salvo no banco.
     *
     * @var array<string, string>
     */
    public const TIPOS_PERFIL = [
        'denunciante' => 'Denunciante',
        'doador' => 'Doador',
        'reciclador' => 'Reciclador',
    ];

    /**
     * Get the friendly label for the user's tipo_perfil
     * (ex.: "reciclador" -> "Reciclador").
     */
    public function tipoPerfilLabel(): ?string
    {
        return self::TIPOS_PERFIL[$this->tipo_perfil] ?? null;
    }

    /**
     * Get a small emoji icon representing the user's tipo_perfil.
     */
    public function tipoPerfilIcone(): ?string
    {
        return match ($this->tipo_perfil) {
            'denunciante' => '📢',
            'doador' => '🎁',
            'reciclador' => '♻️',
            default => null,
        };
    }

    /**
     * Get the posts authored by the user.
     *
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

