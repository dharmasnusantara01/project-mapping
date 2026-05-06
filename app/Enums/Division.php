<?php

namespace App\Enums;

enum Division: string
{
    case Government = 'government';
    case Enterprise = 'enterprise';
    case Sme        = 'sme';

    public function label(): string
    {
        return match ($this) {
            self::Government => 'Government',
            self::Enterprise => 'Enterprise',
            self::Sme        => 'SME',
        };
    }

    public function shortCode(): string
    {
        return match ($this) {
            self::Government => 'G',
            self::Enterprise => 'E',
            self::Sme        => 'SME',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($c) => [$c->value => $c->label()])
            ->all();
    }
}
