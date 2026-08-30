<?php

namespace App\Http\Controllers;

use App\Services\BudgetService;
use App\Services\DashboardService;
use App\Services\SavingGoalService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected TransactionService $transactionService,
        protected BudgetService $budgetService,
        protected SavingGoalService $savingGoalService
    ) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $metrics = $this->dashboardService->getMetrics($userId, $year, $month);
        $recentTransactions = $this->transactionService->getRecentTransactions($userId, 6);
        $budgetSummary = $this->budgetService->getBudgetsWithActuals($userId, $year, $month);
        $savingGoals = $this->savingGoalService->getGoals($userId)->take(3);

        $monthlyTrends = $this->dashboardService->getMonthlyTrendChart($userId, 6);
        $expenseCategories = $this->dashboardService->getExpenseCategoryBreakdown($userId, $year, $month);

        return view('dashboard.index', compact(
            'metrics',
            'recentTransactions',
            'budgetSummary',
            'savingGoals',
            'monthlyTrends',
            'expenseCategories',
            'month',
            'year'
        ));
    }
}