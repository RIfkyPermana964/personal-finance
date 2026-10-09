<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case SAVING_DEPOSIT = 'saving_deposit';
    case SAVING_WITHDRAW = 'saving_withdraw';

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
            self::SAVING_DEPOSIT => 'Alokasi Tabungan',
            self::SAVING_WITHDRAW => 'Penarikan Tabungan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INCOME => 'emerald',
            self::EXPENSE => 'rose',
            self::SAVING_DEPOSIT => 'amber',
            self::SAVING_WITHDRAW => 'cyan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::INCOME => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::EXPENSE => 'bg-rose-50 text-rose-700 border-rose-200',
            self::SAVING_DEPOSIT => 'bg-amber-50 text-amber-700 border-amber-200',
            self::SAVING_WITHDRAW => 'bg-sky-50 text-sky-700 border-sky-200',
        };
    }

    public function isPositive(): bool
    {
        return in_array($this, [self::INCOME, self::SAVING_WITHDRAW], true);
    }
}
