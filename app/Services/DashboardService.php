<?php

namespace App\Services;

use App\Enums\SavingGoalStatus;
use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\SavingGoal;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getMetrics(int $userId, int $year, int $month): array
    {
        // 1. Pemasukan bulan ini
        $incomeMonth = (float) Transaction::where('user_id', $userId)
            ->where('type', TransactionType::INCOME)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        // 2. Pengeluaran konsumtif bulan ini
        $expenseMonth = (float) Transaction::where('user_id', $userId)
            ->where('type', TransactionType::EXPENSE)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        // 3. Alokasi Tabungan bulan ini
        $savingDepositMonth = (float) Transaction::where('user_id', $userId)
            ->where('type', TransactionType::SAVING_DEPOSIT)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        $savingWithdrawMonth = (float) Transaction::where('user_id', $userId)
            ->where('type', TransactionType::SAVING_WITHDRAW)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        // 4. Surplus Bersih (Net Cashflow) = Income - Expense
        $netCashflowMonth = $incomeMonth - $expenseMonth;

        // 5. Total Akumulasi Uang Kas Sepanjang Waktu (Free Liquid Cash)
        $allIncome = (float) Transaction::where('user_id', $userId)->where('type', TransactionType::INCOME)->sum('amount');
        $allExpense = (float) Transaction::where('user_id', $userId)->where('type', TransactionType::EXPENSE)->sum('amount');
        $allSavingDeposit = (float) Transaction::where('user_id', $userId)->where('type', TransactionType::SAVING_DEPOSIT)->sum('amount');
        $allSavingWithdraw = (float) Transaction::where('user_id', $userId)->where('type', TransactionType::SAVING_WITHDRAW)->sum('amount');

        $freeCashBalance = max(0, $allIncome - $allExpense - $allSavingDeposit + $allSavingWithdraw);

        // 6. Total Tabungan di Seluruh Target
        $totalSavings = (float) SavingGoal::where('user_id', $userId)->sum('current_amount');
        $totalTargetSavings = (float) SavingGoal::where('user_id', $userId)->where('status', '!=', SavingGoalStatus::CANCELLED)->sum('target_amount');
        
        $savingProgressPct = $totalTargetSavings > 0 ? min(100, round(($totalSavings / $totalTargetSavings) * 100, 1)) : 0;
        $savingRateMonth = $incomeMonth > 0 ? min(100, round(($savingDepositMonth / $incomeMonth) * 100, 1)) : 0;

        return [
            'income_month' => $incomeMonth,
            'expense_month' => $expenseMonth,
            'net_cashflow_month' => $netCashflowMonth,
            'saving_deposit_month' => $savingDepositMonth,
            'saving_withdraw_month' => $savingWithdrawMonth,
            'free_cash_balance' => $freeCashBalance,
            'total_savings' => $totalSavings,
            'total_target_savings' => $totalTargetSavings,
            'saving_progress_pct' => $savingProgressPct,
            'saving_rate_month' => $savingRateMonth,
        ];
    }

    public function getMonthlyTrendChart(int $userId, int $months = 6): array
    {
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $savingData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $year = $dt->year;
            $month = $dt->month;

            $labels[] = $dt->translatedFormat('M Y');

            $inc = (float) Transaction::where('user_id', $userId)
                ->where('type', TransactionType::INCOME)
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');

            $exp = (float) Transaction::where('user_id', $userId)
                ->where('type', TransactionType::EXPENSE)
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');

            $sav = (float) Transaction::where('user_id', $userId)
                ->where('type', TransactionType::SAVING_DEPOSIT)
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');

            $incomeData[] = $inc;
            $expenseData[] = $exp;
            $savingData[] = $sav;
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
            'saving' => $savingData,
        ];
    }

    public function getExpenseCategoryBreakdown(int $userId, int $year, int $month): array
    {
        $transactions = Transaction::with('category.parent')
            ->where('user_id', $userId)
            ->where('type', TransactionType::EXPENSE)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->get();

        $grouped = [];
        $colors = [];

        foreach ($transactions as $tx) {
            $categoryName = $tx->category?->parent?->name ?? $tx->category?->name ?? 'Lainnya';
            $categoryColor = $tx->category?->parent?->color ?? $tx->category?->color ?? '#64748b';

            if (!isset($grouped[$categoryName])) {
                $grouped[$categoryName] = 0;
                $colors[$categoryName] = $categoryColor;
            }
            $grouped[$categoryName] += (float) $tx->amount;
        }

        arsort($grouped);

        return [
            'labels' => array_keys($grouped),
            'data' => array_values($grouped),
            'colors' => array_values($colors),
        ];
    }
}