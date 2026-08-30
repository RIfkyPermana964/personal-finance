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
        $categories = Category::with('children')->forUser($userId)->where('type', CategoryType::EXPENSE)->whereNull('parent_id')->where('is_active', true)->get();
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

        return redirect()->route('expenses.index')->with('success', 'Data pengeluaran berhasil dicatat!');
    }

    public function update(StoreTransactionRequest $request, Transaction $expense): RedirectResponse
    {
        abort_if($expense->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['type'] = TransactionType::EXPENSE->value;

        $this->transactionService->updateTransaction($expense, $data);

        return redirect()->route('expenses.index')->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    public function destroy(Request $request, Transaction $expense): RedirectResponse
    {
        abort_if($expense->user_id !== $request->user()->id, 403);

        $this->transactionService->deleteTransaction($expense);

        return redirect()->route('expenses.index')->with('success', 'Data pengeluaran berhasil dihapus.');
    }
}