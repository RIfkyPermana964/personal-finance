<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;

class BudgetService
{
    public function getBudgetsWithActuals(int $userId, int $year, int $month): array
    {
        $budgets = Budget::with('category.children')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->where('month', $month)
            ->get();

        $result = [];
        $totalBudget = 0;
        $totalSpent = 0;

        foreach ($budgets as $budget) {
            $categoryIds = [$budget->category_id];
            if ($budget->category && $budget->category->children->isNotEmpty()) {
                $categoryIds = array_merge($categoryIds, $budget->category->children->pluck('id')->toArray());
            }

            $spent = (float) Transaction::where('user_id', $userId)
                ->where('type', TransactionType::EXPENSE)
                ->whereIn('category_id', $categoryIds)
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');

            $amount = (float) $budget->amount;
            $remaining = max(0, $amount - $spent);
            $percentage = $amount > 0 ? round(($spent / $amount) * 100, 1) : 0;
            $isOverBudget = $spent > $amount;

            $totalBudget += $amount;
            $totalSpent += $spent;

            $result[] = [
                'id' => $budget->id,
                'category_id' => $budget->category_id,
                'category_name' => $budget->category?->name ?? 'Kategori Dihapus',
                'category_color' => $budget->category?->color ?? '#3b82f6',
                'category_icon' => $budget->category?->icon ?? 'tag',
                'amount' => $amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'is_over_budget' => $isOverBudget,
            ];
        }

        return [
            'items' => $result,
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'total_remaining' => max(0, $totalBudget - $totalSpent),
            'overall_percentage' => $totalBudget > 0 ? min(100, round(($totalSpent / $totalBudget) * 100, 1)) : 0,
        ];
    }

    public function setBudget(int $userId, int $categoryId, int $month, int $year, float $amount): Budget
    {
        return Budget::updateOrCreate(
            [
                'user_id' => $userId,
                'category_id' => $categoryId,
                'month' => $month,
                'year' => $year,
            ],
            [
                'amount' => $amount,
            ]
        );
    }

    public function deleteBudget(Budget $budget): bool
    {
        return (bool) $budget->delete();
    }
}