<?php

namespace App\Enums;

enum PublicStatus: string
{
    case Berjalan = 'berjalan';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Berjalan => 'Berjalan',
            self::Selesai  => 'Selesai',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($c) => [$c->value => $c->label()])
            ->all();
    }
}
