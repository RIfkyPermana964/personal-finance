<?php

namespace Database\Seeders;

use App\Enums\SavingGoalStatus;
use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        $incomeGaji = Category::where('name', 'Gaji Pokok NOC')->first();
        $incomeInsentif = Category::where('name', 'Insentif & On-Call Shift')->first();
        $incomeSide = Category::where('name', 'Freelance & Side Project')->first();

        $catMakan = Category::where('name', 'Makan & Minum Harian')->first();
        $catSembako = Category::where('name', 'Belanja Sembako / Pasar')->first();
        $catListrik = Category::where('name', 'Listrik PLN (Token/Pascabayar)')->first();
        $catInternet = Category::where('name', 'Internet Rumah (IndiHome/Biznet/Oxygen)')->first();
        $catBensin = Category::where('name', 'Bensin & BBM')->first();
        $catNOC = Category::where('name', 'Peralatan Jaringan (Crimping/LAN/SFP)')->first();
        $catCafe = Category::where('name', 'Nongkrong & Cafe')->first();

        $payCash = PaymentMethod::where('name', 'Tunai (Cash Wallet)')->first();
        $payBCA = PaymentMethod::where('name', 'Bank BCA')->first();
        $payGopay = PaymentMethod::where('name', 'GoPay')->first();
        $payQRIS = PaymentMethod::where('name', 'QRIS (Semua Pembayaran)')->first();

        // 1. Target Tabungan
        $goal1 = SavingGoal::create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat (6 Bulan Pengeluaran)',
            'target_amount' => 15000000.00,
            'current_amount' => 6500000.00,
            'target_date' => Carbon::now()->addMonths(6)->toDateString(),
            'status' => SavingGoalStatus::ACTIVE,
            'notes' => 'Disimpan di Reksadana Pasar Uang / Tabungan Khusus',
            'color' => '#10b981',
        ]);

        $goal2 = SavingGoal::create([
            'user_id' => $user->id,
            'name' => 'Sertifikasi MikroTik MTCNA & MTCRE',
            'target_amount' => 4500000.00,
            'current_amount' => 2000000.00,
            'target_date' => Carbon::now()->addMonths(3)->toDateString(),
            'status' => SavingGoalStatus::ACTIVE,
            'notes' => 'Training & exam voucher',
            'color' => '#3b82f6',
        ]);

        $goal3 = SavingGoal::create([
            'user_id' => $user->id,
            'name' => 'Upgrade Laptop ThinkPad NOC',
            'target_amount' => 12000000.00,
            'current_amount' => 4000000.00,
            'target_date' => Carbon::now()->addMonths(8)->toDateString(),
            'status' => SavingGoalStatus::ACTIVE,
            'notes' => 'Untuk troubleshooting di lapangan & simulasi GNS3/EVE-NG',
            'color' => '#8b5cf6',
        ]);

        // 2. Budget Bulanan (Bulan Berjalan)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $parentPokok = Category::where('name', 'Kebutuhan Pokok & Makan')->first();
        $parentTagihan = Category::where('name', 'Tagihan & Utilitas')->first();
        $parentTransport = Category::where('name', 'Transportasi & Operasional')->first();
        $parentLifestyle = Category::where('name', 'Lifestyle & Hiburan')->first();
        $parentNOC = Category::where('name', 'Pekerjaan & Tools NOC')->first();

        if ($parentPokok) {
            Budget::create(['user_id' => $user->id, 'category_id' => $parentPokok->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 1500000.00]);
        }
        if ($parentTagihan) {
            Budget::create(['user_id' => $user->id, 'category_id' => $parentTagihan->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 750000.00]);
        }
        if ($parentTransport) {
            Budget::create(['user_id' => $user->id, 'category_id' => $parentTransport->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 500000.00]);
        }
        if ($parentLifestyle) {
            Budget::create(['user_id' => $user->id, 'category_id' => $parentLifestyle->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 400000.00]);
        }
        if ($parentNOC) {
            Budget::create(['user_id' => $user->id, 'category_id' => $parentNOC->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 600000.00]);
        }

        // 3. Transaksi Data Realistis (Bulan ini dan 3 bulan ke belakang)
        for ($m = 3; $m >= 0; $m--) {
            $dateMonth = Carbon::now()->subMonths($m);
            $salaryDate = $dateMonth->copy()->startOfMonth()->addDays(24);

            // Gaji Masuk
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::INCOME,
                'category_id' => $incomeGaji?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 6500000.00,
                'transaction_date' => $salaryDate->toDateString(),
                'description' => 'Gaji Pokok NOC Bulan ' . $salaryDate->translatedFormat('F Y'),
            ]);

            // Insentif Shift On-Call
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::INCOME,
                'category_id' => $incomeInsentif?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 750000.00,
                'transaction_date' => $salaryDate->copy()->addDays(2)->toDateString(),
                'description' => 'Insentif On-Call Shift Malam',
            ]);

            // Alokasi ke Dana Darurat
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::SAVING_DEPOSIT,
                'saving_goal_id' => $goal1->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 1000000.00,
                'transaction_date' => $salaryDate->copy()->addDays(1)->toDateString(),
                'description' => 'Setoran Rutin Dana Darurat',
            ]);

            // Pengeluaran Pokok
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catListrik?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 250000.00,
                'transaction_date' => $salaryDate->copy()->addDays(3)->toDateString(),
                'description' => 'Beli Token Listrik PLN',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catInternet?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 350000.00,
                'transaction_date' => $salaryDate->copy()->addDays(4)->toDateString(),
                'description' => 'Tagihan Internet Fiber',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catMakan?->id,
                'payment_method_id' => $payQRIS?->id,
                'amount' => 850000.00,
                'transaction_date' => $salaryDate->copy()->addDays(5)->toDateString(),
                'description' => 'Makan & Minum Harian',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catBensin?->id,
                'payment_method_id' => $payCash?->id,
                'amount' => 200000.00,
                'transaction_date' => $salaryDate->copy()->addDays(7)->toDateString(),
                'description' => 'Bensin Pertamax Motor Operasional',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catCafe?->id,
                'payment_method_id' => $payQRIS?->id,
                'amount' => 120000.00,
                'transaction_date' => $salaryDate->copy()->addDays(8)->toDateString(),
                'description' => 'Ngopi sambil monitoring jaringan NOC',
            ]);
        }
    }
}
