<?php

namespace App\Enums;

enum ProjectStage: string
{
    case Qualified = 'qualified';
    case Submit    = 'submit';
    case Win       = 'win';
    case Lost      = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Qualified => 'Qualified',
            self::Submit    => 'Submit',
            self::Win       => 'Win',
            self::Lost      => 'Lost',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Qualified => '#0ea5e9',
            self::Submit    => '#f59e0b',
            self::Win       => '#10b981',
            self::Lost      => '#ef4444',
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::Qualified => self::Submit,
            self::Submit    => self::Win,
            default         => null,
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Win, self::Lost], true);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($c) => [$c->value => $c->label()])
            ->all();
    }
}
