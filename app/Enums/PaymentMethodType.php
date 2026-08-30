<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case CASH = 'cash';
    case BANK = 'bank';
    case EWALLET = 'ewallet';
    case CARD = 'card';
    case QRIS = 'qris';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'Tunai (Cash)',
            self::BANK => 'Transfer Bank',
            self::EWALLET => 'E-Wallet',
            self::CARD => 'Kartu Debit / Kredit',
            self::QRIS => 'QRIS',
            self::OTHER => 'Lainnya',
        };
    }
}
