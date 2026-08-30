<?php

namespace App\Enums;

enum SavingGoalStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Sedang Berjalan',
            self::COMPLETED => 'Tercapai',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::ACTIVE => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            self::COMPLETED => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            self::CANCELLED => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
        };
    }
}
