<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\SalaryAllocation;
use App\Services\SalaryAllocationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryAllocationController extends Controller
{
    public function __construct(protected SalaryAllocationService $allocationService) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $allocationsData = $this->allocationService->getAllocationsForMonth($userId, $year, $month);
        $balanceData = $this->allocationService->getMonthlyBalanceData($userId, $year, $month);
        $categories = Category::forUser($userId)->where('type', CategoryType::EXPENSE)->where('is_active', true)->get();

        return view('salary-allocations.index', compact('allocationsData', 'balanceData', 'categories', 'month', 'year'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'item_name' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:1'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:unpaid,paid'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->allocationService->storeAllocation($request->user()->id, $validated);

        return redirect()->route('salary-allocations.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Pos pembagian gaji berhasil ditambahkan!');
    }

    public function toggle(Request $request, SalaryAllocation $salaryAllocation): RedirectResponse
    {
        abort_if($salaryAllocation->user_id !== $request->user()->id, 403);

        $this->allocationService->toggleStatus($salaryAllocation);

        return redirect()->back()->with('success', "Status {$salaryAllocation->item_name} berhasil diubah.");
    }

    public function updateBalance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'cash_initial' => ['required', 'numeric', 'min:0'],
            'bank_initial' => ['required', 'numeric', 'min:0'],
            'total_salary' => ['required', 'numeric', 'min:0'],
        ]);

        $this->allocationService->updateMonthlyBalance($request->user()->id, $validated['year'], $validated['month'], $validated);

        return redirect()->route('salary-allocations.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Data Kesimpulan Saldo & Total Gaji berhasil diperbarui!');
    }

    public function destroy(Request $request, SalaryAllocation $salaryAllocation): RedirectResponse
    {
        abort_if($salaryAllocation->user_id !== $request->user()->id, 403);

        $this->allocationService->deleteAllocation($salaryAllocation);

        return redirect()->back()->with('success', 'Pos pengeluaran gaji berhasil dihapus.');
    }
}