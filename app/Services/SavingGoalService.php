<?php

namespace App\Services;

use App\Enums\SavingGoalStatus;
use App\Enums\TransactionType;
use App\Models\SavingGoal;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class SavingGoalService
{
    public function getGoals(int $userId)
    {
        return SavingGoal::with(['transactions' => function ($q) {
            $q->latest('transaction_date')->limit(5);
        }])
            ->where('user_id', $userId)
            ->orderByRaw("CASE WHEN status = 'active' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            ->orderBy('target_date', 'asc')
            ->get();
    }

    public function createGoal(int $userId, array $data): SavingGoal
    {
        return SavingGoal::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'target_amount' => $data['target_amount'],
            'current_amount' => 0.00,
            'target_date' => $data['target_date'] ?? null,
            'status' => SavingGoalStatus::ACTIVE,
            'notes' => $data['notes'] ?? null,
            'color' => $data['color'] ?? '#10b981',
        ]);
    }

    public function updateGoal(SavingGoal $goal, array $data): SavingGoal
    {
        $goal->update([
            'name' => $data['name'] ?? $goal->name,
            'target_amount' => $data['target_amount'] ?? $goal->target_amount,
            'target_date' => $data['target_date'] ?? $goal->target_date,
            'notes' => $data['notes'] ?? $goal->notes,
            'color' => $data['color'] ?? $goal->color,
            'status' => $data['status'] ?? $goal->status,
        ]);

        return $goal;
    }

    public function deposit(SavingGoal $goal, float $amount, ?int $paymentMethodId, string $date, ?string $notes = null): Transaction
    {
        return DB::transaction(function () use ($goal, $amount, $paymentMethodId, $date, $notes) {
            $tx = Transaction::create([
                'user_id' => $goal->user_id,
                'type' => TransactionType::SAVING_DEPOSIT,
                'saving_goal_id' => $goal->id,
                'payment_method_id' => $paymentMethodId,
                'amount' => $amount,
                'transaction_date' => $date,
                'description' => $notes ?: 'Setoran Tabungan: '.$goal->name,
            ]);

            app(TransactionService::class)->syncSavingGoalBalance($goal->id);

            return $tx;
        });
    }

    public function withdraw(SavingGoal $goal, float $amount, ?int $paymentMethodId, string $date, ?string $notes = null): Transaction
    {
        return DB::transaction(function () use ($goal, $amount, $paymentMethodId, $date, $notes) {
            $tx = Transaction::create([
                'user_id' => $goal->user_id,
                'type' => TransactionType::SAVING_WITHDRAW,
                'saving_goal_id' => $goal->id,
                'payment_method_id' => $paymentMethodId,
                'amount' => $amount,
                'transaction_date' => $date,
                'description' => $notes ?: 'Penarikan Tabungan: '.$goal->name,
            ]);

            app(TransactionService::class)->syncSavingGoalBalance($goal->id);

            return $tx;
        });
    }

    public function deleteGoal(SavingGoal $goal): bool
    {
        return (bool) $goal->delete();
    }
}
