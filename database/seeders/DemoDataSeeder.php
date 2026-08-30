<?php

namespace Database\Seeders;

use App\Enums\SavingGoalStatus;
use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\MonthlyBalance;
use App\Models\PaymentMethod;
use App\Models\SalaryAllocation;
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

        $incomeGaji = Category::where('name', 'Gaji Bulanan')->first();
        $incomeSide = Category::where('name', 'Freelance & Side Job')->first();

        $catMakan = Category::where('name', 'Makan & Minum Harian')->first();
        $catSembako = Category::where('name', 'Belanja Sembako / Pasar')->first();
        $catKos = Category::where('name', 'Sewa Kos / Kontrakan')->first();
        $catListrik = Category::where('name', 'Listrik PLN')->first();
        $catInternet = Category::where('name', 'Internet & WiFi')->first();
        $catBensin = Category::where('name', 'Bensin / BBM')->first();
        $catCafe = Category::where('name', 'Nongkrong & Kafe')->first();
        $catOrtu = Category::where('name', 'Kirim Orang Tua')->first();

        $payCash = PaymentMethod::where('name', 'Tunai (Cash Wallet)')->first();
        $payBCA = PaymentMethod::where('name', 'Bank BCA')->first();
        $payQRIS = PaymentMethod::where('name', 'QRIS (Semua Pembayaran)')->first();

        // 1. Target Tabungan
        $goal1 = SavingGoal::create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 15000000.00,
            'current_amount' => 6000000.00,
            'target_date' => Carbon::now()->addMonths(6)->toDateString(),
            'status' => SavingGoalStatus::ACTIVE,
            'notes' => 'Disimpan di tabungan terpisah',
            'color' => '#10b981',
        ]);

        $goal2 = SavingGoal::create([
            'user_id' => $user->id,
            'name' => 'Tabungan Masa Depan',
            'target_amount' => 10000000.00,
            'current_amount' => 3500000.00,
            'target_date' => Carbon::now()->addMonths(10)->toDateString(),
            'status' => SavingGoalStatus::ACTIVE,
            'notes' => 'Alokasi bulanan',
            'color' => '#3b82f6',
        ]);

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 2. Kesimpulan Kas & Saldo Awal Bulan
        MonthlyBalance::create([
            'user_id' => $user->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'cash_initial' => 500000.00,
            'bank_initial' => 2500000.00,
            'total_salary' => 6000000.00,
        ]);

        // 3. Pembagian Gaji (Salary Allocations dengan status UNPAID / PAID)
        $allocations = [
            ['name' => 'Sewa Kosan', 'amount' => 1200000.00, 'category_id' => $catKos?->id, 'status' => 'paid', 'paid_date' => Carbon::now()->toDateString()],
            ['name' => 'Uang Belanja & Makan', 'amount' => 1500000.00, 'category_id' => $catMakan?->id, 'status' => 'unpaid', 'paid_date' => null],
            ['name' => 'Listrik PLN & WiFi', 'amount' => 500000.00, 'category_id' => $catListrik?->id, 'status' => 'paid', 'paid_date' => Carbon::now()->toDateString()],
            ['name' => 'Bensin & Transport', 'amount' => 400000.00, 'category_id' => $catBensin?->id, 'status' => 'unpaid', 'paid_date' => null],
            ['name' => 'Bantu Orang Tua', 'amount' => 1000000.00, 'category_id' => $catOrtu?->id, 'status' => 'paid', 'paid_date' => Carbon::now()->toDateString()],
            ['name' => 'Alokasi Tabungan', 'amount' => 1000000.00, 'category_id' => null, 'status' => 'unpaid', 'paid_date' => null],
            ['name' => 'Lifestyle / Hiburan', 'amount' => 400000.00, 'category_id' => $catCafe?->id, 'status' => 'unpaid', 'paid_date' => null],
        ];

        foreach ($allocations as $item) {
            SalaryAllocation::create([
                'user_id' => $user->id,
                'month' => $currentMonth,
                'year' => $currentYear,
                'item_name' => $item['name'],
                'amount' => $item['amount'],
                'category_id' => $item['category_id'],
                'status' => $item['status'],
                'paid_date' => $item['paid_date'],
            ]);
        }

        // 4. Budget Bulanan
        $parentPokok = Category::where('name', 'Kebutuhan Pokok & Makan')->first();
        $parentTagihan = Category::where('name', 'Tagihan & Rumah Tangga')->first();
        $parentTransport = Category::where('name', 'Transportasi')->first();
        $parentLifestyle = Category::where('name', 'Lifestyle & Hiburan')->first();

        if ($parentPokok) Budget::create(['user_id' => $user->id, 'category_id' => $parentPokok->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 1800000.00]);
        if ($parentTagihan) Budget::create(['user_id' => $user->id, 'category_id' => $parentTagihan->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 1700000.00]);
        if ($parentTransport) Budget::create(['user_id' => $user->id, 'category_id' => $parentTransport->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 500000.00]);
        if ($parentLifestyle) Budget::create(['user_id' => $user->id, 'category_id' => $parentLifestyle->id, 'month' => $currentMonth, 'year' => $currentYear, 'amount' => 500000.00]);

        // 5. Riwayat Transaksi 3 Bulan
        for ($m = 2; $m >= 0; $m--) {
            $dateMonth = Carbon::now()->subMonths($m);
            $salaryDate = $dateMonth->copy()->startOfMonth()->addDays(24);

            // Gaji Masuk
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::INCOME,
                'category_id' => $incomeGaji?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 6000000.00,
                'transaction_date' => $salaryDate->toDateString(),
                'description' => 'Gaji Bulan ' . $salaryDate->translatedFormat('F Y'),
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

            // Pengeluaran Kosan
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catKos?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 1200000.00,
                'transaction_date' => $salaryDate->copy()->addDays(1)->toDateString(),
                'description' => 'Bayar Sewa Kosan',
            ]);

            // Pengeluaran Listrik
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catListrik?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 200000.00,
                'transaction_date' => $salaryDate->copy()->addDays(2)->toDateString(),
                'description' => 'Token Listrik PLN',
            ]);

            // Pengeluaran Makan
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catMakan?->id,
                'payment_method_id' => $payQRIS?->id,
                'amount' => 650000.00,
                'transaction_date' => $salaryDate->copy()->addDays(3)->toDateString(),
                'description' => 'Makan & Minum Harian',
            ]);

            // Pengeluaran Bensin
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catBensin?->id,
                'payment_method_id' => $payCash?->id,
                'amount' => 150000.00,
                'transaction_date' => $salaryDate->copy()->addDays(4)->toDateString(),
                'description' => 'Bensin Motor',
            ]);

            // Kirim Orang Tua
            Transaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::EXPENSE,
                'category_id' => $catOrtu?->id,
                'payment_method_id' => $payBCA?->id,
                'amount' => 1000000.00,
                'transaction_date' => $salaryDate->copy()->addDays(2)->toDateString(),
                'description' => 'Kirim Orang Tua',
            ]);
        }
    }
}