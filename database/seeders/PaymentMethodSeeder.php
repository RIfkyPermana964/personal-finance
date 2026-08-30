<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodType;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Tunai (Cash Wallet)', 'type' => PaymentMethodType::CASH, 'account_number' => null],
            ['name' => 'Bank BCA', 'type' => PaymentMethodType::BANK, 'account_number' => '1234567890'],
            ['name' => 'Bank Mandiri', 'type' => PaymentMethodType::BANK, 'account_number' => '9876543210'],
            ['name' => 'Bank Jago', 'type' => PaymentMethodType::BANK, 'account_number' => '500123456'],
            ['name' => 'GoPay', 'type' => PaymentMethodType::EWALLET, 'account_number' => '08123456789'],
            ['name' => 'OVO', 'type' => PaymentMethodType::EWALLET, 'account_number' => '08123456789'],
            ['name' => 'DANA', 'type' => PaymentMethodType::EWALLET, 'account_number' => '08123456789'],
            ['name' => 'QRIS (Semua Pembayaran)', 'type' => PaymentMethodType::QRIS, 'account_number' => null],
            ['name' => 'Kartu Debit BCA', 'type' => PaymentMethodType::CARD, 'account_number' => '**** 1234'],
        ];

        foreach ($methods as $item) {
            PaymentMethod::create([
                'user_id' => null, // Global default
                'name' => $item['name'],
                'type' => $item['type'],
                'account_number' => $item['account_number'],
                'is_active' => true,
            ]);
        }
    }
}
