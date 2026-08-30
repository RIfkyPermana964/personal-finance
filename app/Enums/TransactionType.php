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
        return match($this) {
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
            self::SAVING_DEPOSIT => 'Alokasi Tabungan',
            self::SAVING_WITHDRAW => 'Penarikan Tabungan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INCOME => 'emerald',
            self::EXPENSE => 'rose',
            self::SAVING_DEPOSIT => 'amber',
            self::SAVING_WITHDRAW => 'cyan',
        };
    }
}
