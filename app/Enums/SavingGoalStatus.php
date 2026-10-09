<?php

namespace App\Enums;

enum SavingGoalStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Sedang Berjalan',
            self::COMPLETED => 'Tercapai',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-amber-50 text-amber-700 border-amber-200',
            self::COMPLETED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::CANCELLED => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
}
