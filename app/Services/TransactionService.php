<?php

namespace App\Services;

use App\Enums\SavingGoalStatus;
use App\Enums\TransactionType;
use App\Models\SavingGoal;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function getPaginatedTransactions(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::with(['category.parent', 'paymentMethod', 'savingGoal'])
            ->where('user_id', $userId);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('paymentMethod', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['payment_method_id'])) {
            $query->where('payment_method_id', $filters['payment_method_id']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('transaction_date', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['month']) && !empty($filters['year'])) {
            $query->whereYear('transaction_date', $filters['year'])
                  ->whereMonth('transaction_date', $filters['month']);
        }

        $sort = $filters['sort'] ?? 'transaction_date';
        $order = $filters['order'] ?? 'desc';

        if (in_array($sort, ['transaction_date', 'amount', 'created_at'])) {
            $query->orderBy($sort, $order === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createTransaction(int $userId, array $data): Transaction
    {
        return DB::transaction(function () use ($userId, $data) {
            $transaction = Transaction::create([
                'user_id' => $userId,
                'type' => $data['type'],
                'category_id' => $data['category_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'saving_goal_id' => $data['saving_goal_id'] ?? null,
                'amount' => $data['amount'],
                'transaction_date' => $data['transaction_date'],
                'description' => $data['description'] ?? null,
                'receipt_image' => $data['receipt_image'] ?? null,
            ]);

            if (!empty($transaction->saving_goal_id)) {
                $this->syncSavingGoalBalance($transaction->saving_goal_id);
            }

            return $transaction;
        });
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $oldGoalId = $transaction->saving_goal_id;

            $transaction->update([
                'type' => $data['type'] ?? $transaction->type,
                'category_id' => array_key_exists('category_id', $data) ? $data['category_id'] : $transaction->category_id,
                'payment_method_id' => array_key_exists('payment_method_id', $data) ? $data['payment_method_id'] : $transaction->payment_method_id,
                'saving_goal_id' => array_key_exists('saving_goal_id', $data) ? $data['saving_goal_id'] : $transaction->saving_goal_id,
                'amount' => $data['amount'] ?? $transaction->amount,
                'transaction_date' => $data['transaction_date'] ?? $transaction->transaction_date,
                'description' => $data['description'] ?? $transaction->description,
                'receipt_image' => $data['receipt_image'] ?? $transaction->receipt_image,
            ]);

            if ($oldGoalId) {
                $this->syncSavingGoalBalance($oldGoalId);
            }
            if ($transaction->saving_goal_id && $transaction->saving_goal_id !== $oldGoalId) {
                $this->syncSavingGoalBalance($transaction->saving_goal_id);
            }

            return $transaction;
        });
    }

    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $goalId = $transaction->saving_goal_id;
            $deleted = $transaction->delete();

            if ($goalId) {
                $this->syncSavingGoalBalance($goalId);
            }

            return $deleted;
        });
    }

    public function getRecentTransactions(int $userId, int $limit = 5)
    {
        return Transaction::with(['category.parent', 'paymentMethod', 'savingGoal'])
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function syncSavingGoalBalance(int $goalId): void
    {
        $goal = SavingGoal::find($goalId);
        if (!$goal) return;

        $deposits = Transaction::where('saving_goal_id', $goalId)
            ->where('type', TransactionType::SAVING_DEPOSIT)
            ->sum('amount');

        $withdrawals = Transaction::where('saving_goal_id', $goalId)
            ->where('type', TransactionType::SAVING_WITHDRAW)
            ->sum('amount');

        $currentAmount = max(0, (float) ($deposits - $withdrawals));
        $status = $goal->status;

        if ($currentAmount >= (float) $goal->target_amount && $goal->target_amount > 0) {
            $status = SavingGoalStatus::COMPLETED;
        } elseif ($status === SavingGoalStatus::COMPLETED && $currentAmount < (float) $goal->target_amount) {
            $status = SavingGoalStatus::ACTIVE;
        }

        $goal->update([
            'current_amount' => $currentAmount,
            'status' => $status,
        ]);
    }
}