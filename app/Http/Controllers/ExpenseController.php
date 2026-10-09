<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $filters = [
            'type' => TransactionType::EXPENSE->value,
            'search' => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'payment_method_id' => $request->input('payment_method_id'),
            'month' => $month,
            'year' => $year,
        ];

        $transactions = $this->transactionService->getPaginatedTransactions($userId, $filters, 15);
        $categories = Category::with(['children' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->forUser($userId)
            ->where('type', CategoryType::EXPENSE)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $allCategories = Category::forUser($userId)->where('type', CategoryType::EXPENSE)->where('is_active', true)->get();
        $paymentMethods = PaymentMethod::forUser($userId)->where('is_active', true)->get();

        $totalMonthExpense = Transaction::where('user_id', $userId)
            ->where('type', TransactionType::EXPENSE)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        return view('expenses.index', compact('transactions', 'categories', 'allCategories', 'paymentMethods', 'totalMonthExpense', 'month', 'year'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['type'] = TransactionType::EXPENSE->value;

        $this->transactionService->createTransaction($request->user()->id, $data);

        $txDate = Carbon::parse($data['transaction_date']);

        return redirect()->route('expenses.index', ['month' => $txDate->month, 'year' => $txDate->year])
            ->with('success', 'Data pengeluaran berhasil dicatat!');
    }

    public function update(StoreTransactionRequest $request, Transaction $expense): RedirectResponse
    {
        abort_if($expense->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['type'] = TransactionType::EXPENSE->value;

        $this->transactionService->updateTransaction($expense, $data);

        $txDate = Carbon::parse($data['transaction_date']);

        return redirect()->route('expenses.index', ['month' => $txDate->month, 'year' => $txDate->year])
            ->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    public function destroy(Request $request, Transaction $expense): RedirectResponse
    {
        abort_if($expense->user_id !== $request->user()->id, 403);

        $txDate = Carbon::parse($expense->transaction_date);
        $this->transactionService->deleteTransaction($expense);

        return redirect()->route('expenses.index', ['month' => $txDate->month, 'year' => $txDate->year])
            ->with('success', 'Data pengeluaran berhasil dihapus.');
    }
}
