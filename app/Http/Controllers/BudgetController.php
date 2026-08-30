<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(protected BudgetService $budgetService) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $budgets = $this->budgetService->getBudgetsWithActuals($userId, $year, $month);
        $categories = Category::forUser($userId)->where('type', CategoryType::EXPENSE)->whereNull('parent_id')->where('is_active', true)->get();

        return view('budgets.index', compact('budgets', 'categories', 'month', 'year'));
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->budgetService->setBudget(
            $request->user()->id,
            $validated['category_id'],
            $validated['month'],
            $validated['year'],
            (float) $validated['amount']
        );

        return redirect()->route('budgets.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Pagu anggaran bulanan berhasil disimpan!');
    }

    public function destroy(Request $request, Budget $budget): RedirectResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403);

        $this->budgetService->deleteBudget($budget);

        return redirect()->back()->with('success', 'Pagu anggaran berhasil dihapus.');
    }
}