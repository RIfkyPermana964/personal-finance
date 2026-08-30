<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Carbon\Carbon;

class ReportService
{
    public function getReportData(int $userId, string $periodType, ?int $year = null, ?int $month = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $year = $year ?: Carbon::now()->year;
        $month = $month ?: Carbon::now()->month;

        $query = Transaction::with(['category.parent', 'paymentMethod'])
            ->where('user_id', $userId);

        if ($periodType === 'monthly') {
            $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
            $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        } elseif ($periodType === 'yearly') {
            $query->whereYear('transaction_date', $year);
            $periodLabel = "Tahun {$year}";
        } elseif ($periodType === 'custom' && $startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
            $periodLabel = Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
        } else {
            $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
            $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->get();

        $totalIncome = 0;
        $totalExpense = 0;
        $totalSavingDeposit = 0;
        $totalSavingWithdraw = 0;

        $incomeByCategory = [];
        $expenseByCategory = [];
        $expenseByPaymentMethod = [];

        foreach ($transactions as $tx) {
            $amt = (float) $tx->amount;
            $catName = $tx->category?->parent?->name ? ($tx->category->parent->name . ' → ' . $tx->category->name) : ($tx->category?->name ?? 'Lainnya');
            $payName = $tx->paymentMethod?->name ?? 'Lainnya';

            if ($tx->type === TransactionType::INCOME) {
                $totalIncome += $amt;
                $incomeByCategory[$catName] = ($incomeByCategory[$catName] ?? 0) + $amt;
            } elseif ($tx->type === TransactionType::EXPENSE) {
                $totalExpense += $amt;
                $expenseByCategory[$catName] = ($expenseByCategory[$catName] ?? 0) + $amt;
                $expenseByPaymentMethod[$payName] = ($expenseByPaymentMethod[$payName] ?? 0) + $amt;
            } elseif ($tx->type === TransactionType::SAVING_DEPOSIT) {
                $totalSavingDeposit += $amt;
            } elseif ($tx->type === TransactionType::SAVING_WITHDRAW) {
                $totalSavingWithdraw += $amt;
            }
        }

        arsort($incomeByCategory);
        arsort($expenseByCategory);
        arsort($expenseByPaymentMethod);

        $netBalance = $totalIncome - $totalExpense;
        $savingRate = $totalIncome > 0 ? round(($totalSavingDeposit / $totalIncome) * 100, 1) : 0;

        return [
            'period_type' => $periodType,
            'period_label' => $periodLabel,
            'year' => $year,
            'month' => $month,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'total_saving_deposit' => $totalSavingDeposit,
            'total_saving_withdraw' => $totalSavingWithdraw,
            'net_balance' => $netBalance,
            'saving_rate' => $savingRate,
            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
            'expense_by_payment_method' => $expenseByPaymentMethod,
            'transaction_count' => $transactions->count(),
            'transactions' => $transactions,
        ];
    }
}