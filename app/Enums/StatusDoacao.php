<?php

namespace App\Enums;

enum StatusDoacao: string
{
    case Disponivel = 'disponivel';
    case Reservado = 'reservado';
    case Coletado = 'coletado';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Disponivel => 'Disponível',
            self::Reservado => 'Reservado',
            self::Coletado => 'Coletado',
            self::Cancelado => 'Cancelado',
        };
    }

    public function classes(): string
    {
        return match ($this) {
            self::Disponivel => 'bg-blue-100 text-blue-700',
            self::Reservado => 'bg-yellow-100 text-yellow-700',
            self::Coletado => 'bg-green-100 text-green-700',
            self::Cancelado => 'bg-red-100 text-red-700',
        };
    }
}
