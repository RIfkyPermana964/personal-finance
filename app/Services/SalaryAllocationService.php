<?php

namespace App\Services;

use App\Enums\PaymentMethodType;
use App\Enums\TransactionType;
use App\Models\MonthlyBalance;
use App\Models\SalaryAllocation;
use App\Models\Transaction;
use Carbon\Carbon;

class SalaryAllocationService
{
    public function getAllocationsForMonth(int $userId, int $year, int $month): array
    {
        $allocations = SalaryAllocation::with('category')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->where('month', $month)
            ->orderBy('id', 'asc')
            ->get();

        $totalAllocated = $allocations->sum('amount');
        $totalPaid = $allocations->where('status', 'paid')->sum('amount');
        $totalUnpaid = $allocations->where('status', 'unpaid')->sum('amount');

        return [
            'items' => $allocations,
            'total_allocated' => (float) $totalAllocated,
            'total_paid' => (float) $totalPaid,
            'total_unpaid' => (float) $totalUnpaid,
        ];
    }

    public function getMonthlyBalanceData(int $userId, int $year, int $month): array
    {
        $balance = MonthlyBalance::firstOrCreate(
            ['user_id' => $userId, 'year' => $year, 'month' => $month],
            ['cash_initial' => 0.00, 'bank_initial' => 0.00, 'total_salary' => 0.00]
        );

        // Calculate Cash and Bank In / Out for this month
        $cashIn = (float) Transaction::where('user_id', $userId)
            ->where('type', TransactionType::INCOME)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereHas('paymentMethod', fn ($q) => $q->where('type', PaymentMethodType::CASH))
            ->sum('amount');

        $cashOut = (float) Transaction::where('user_id', $userId)
            ->whereIn('type', [TransactionType::EXPENSE, TransactionType::SAVING_DEPOSIT])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereHas('paymentMethod', fn ($q) => $q->where('type', PaymentMethodType::CASH))
            ->sum('amount');

        $bankIn = (float) Transaction::where('user_id', $userId)
            ->where('type', TransactionType::INCOME)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereHas('paymentMethod', fn ($q) => $q->where('type', '!=', PaymentMethodType::CASH))
            ->sum('amount');

        $bankOut = (float) Transaction::where('user_id', $userId)
            ->whereIn('type', [TransactionType::EXPENSE, TransactionType::SAVING_DEPOSIT])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereHas('paymentMethod', fn ($q) => $q->where('type', '!=', PaymentMethodType::CASH))
            ->sum('amount');

        $cashFinal = (float) $balance->cash_initial + $cashIn - $cashOut;
        $bankFinal = (float) $balance->bank_initial + $bankIn - $bankOut;

        return [
            'balance_record' => $balance,
            'cash_initial' => (float) $balance->cash_initial,
            'bank_initial' => (float) $balance->bank_initial,
            'cash_final' => $cashFinal,
            'bank_final' => $bankFinal,
            'total_salary' => (float) $balance->total_salary,
        ];
    }

    public function storeAllocation(int $userId, array $data): SalaryAllocation
    {
        return SalaryAllocation::create([
            'user_id' => $userId,
            'month' => $data['month'],
            'year' => $data['year'],
            'item_name' => $data['item_name'],
            'amount' => $data['amount'],
            'category_id' => $data['category_id'] ?? null,
            'status' => $data['status'] ?? 'unpaid',
            'paid_date' => ($data['status'] ?? 'unpaid') === 'paid' ? Carbon::now()->toDateString() : null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function toggleStatus(SalaryAllocation $allocation): SalaryAllocation
    {
        $newStatus = $allocation->status === 'paid' ? 'unpaid' : 'paid';
        $paidDate = $newStatus === 'paid' ? Carbon::now()->toDateString() : null;

        $allocation->update([
            'status' => $newStatus,
            'paid_date' => $paidDate,
        ]);

        return $allocation;
    }

    public function deleteAllocation(SalaryAllocation $allocation): bool
    {
        return (bool) $allocation->delete();
    }

    public function updateMonthlyBalance(int $userId, int $year, int $month, array $data): MonthlyBalance
    {
        $record = MonthlyBalance::firstOrNew(['user_id' => $userId, 'year' => $year, 'month' => $month]);

        if (isset($data['cash_initial'])) {
            $record->cash_initial = $data['cash_initial'];
        }
        if (isset($data['bank_initial'])) {
            $record->bank_initial = $data['bank_initial'];
        }
        if (isset($data['total_salary'])) {
            $record->total_salary = $data['total_salary'];
        }

        $record->save();

        return $record;
    }
}
