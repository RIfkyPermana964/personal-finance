<?php

namespace App\Enums;

enum DebtStatus: string
{
    case UNPAID = 'unpaid';
    case NYICIL = 'nyicil';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Belum Dibayar',
            self::NYICIL => 'Sedang Dicicil',
            self::PAID => 'Lunas',
        };
    }

    public function code(): string
    {
        return match ($this) {
            self::UNPAID => 'UNPAID',
            self::NYICIL => 'NYICIL',
            self::PAID => 'PAID',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'rose',
            self::NYICIL => 'amber',
            self::PAID => 'emerald',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::UNPAID => 'bg-rose-50 text-rose-700 border-rose-200',
            self::NYICIL => 'bg-amber-50 text-amber-700 border-amber-200',
            self::PAID => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
