<?php

namespace App\Enums;

enum DebtType: string
{
    case RECEIVABLE = 'receivable'; // Orang meminjam ke kita (Piutang)
    case DEBT = 'debt';             // Pinjaman kita sendiri / Hutang ke orang lain
    case RENT = 'rent';             // Sewa & Tagihan Rutin Berkala

    public function label(): string
    {
        return match ($this) {
            self::RECEIVABLE => 'Piutang (Dipinjam Orang)',
            self::DEBT => 'Hutang (Pinjaman Saya)',
            self::RENT => 'Sewa / Tagihan Rutin',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::RECEIVABLE => 'Piutang',
            self::DEBT => 'Hutang',
            self::RENT => 'Sewa (Rent)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RECEIVABLE => 'emerald',
            self::DEBT => 'rose',
            self::RENT => 'indigo',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::RECEIVABLE => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::DEBT => 'bg-rose-50 text-rose-700 border-rose-200',
            self::RENT => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        };
    }

    public function partyLabel(): string
    {
        return match ($this) {
            self::RECEIVABLE => 'Nama Peminjam (Orang)',
            self::DEBT => 'Nama Pemberi Pinjaman / Bank',
            self::RENT => 'Item / Nama Sewa (Kosan/Kontrakan)',
        };
    }
}
