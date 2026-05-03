<?php

namespace App\Enums;

enum UserRole: string
{
    case Sales        = 'sales';
    case ManajerSales = 'manajer_sales';
    case Superadmin   = 'superadmin';

    public function label(): string
    {
        return match ($this) {
            self::Sales        => 'Sales',
            self::ManajerSales => 'Manajer Sales',
            self::Superadmin   => 'Superadmin',
        };
    }

    public function canPublish(): bool
    {
        return in_array($this, [self::ManajerSales, self::Superadmin], true);
    }
}
