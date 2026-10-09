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

class IncomeController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $filters = [
            'type' => TransactionType::INCOME->value,
            'search' => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'payment_method_id' => $request->input('payment_method_id'),
            'month' => $month,
            'year' => $year,
        ];

        $transactions = $this->transactionService->getPaginatedTransactions($userId, $filters, 15);
        $categories = Category::forUser($userId)->where('type', CategoryType::INCOME)->where('is_active', true)->get();
        $paymentMethods = PaymentMethod::forUser($userId)->where('is_active', true)->get();

        $totalMonthIncome = Transaction::where('user_id', $userId)
            ->where('type', TransactionType::INCOME)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        return view('income.index', compact('transactions', 'categories', 'paymentMethods', 'totalMonthIncome', 'month', 'year'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['type'] = TransactionType::INCOME->value;

        $this->transactionService->createTransaction($request->user()->id, $data);

        $txDate = Carbon::parse($data['transaction_date']);

        return redirect()->route('income.index', ['month' => $txDate->month, 'year' => $txDate->year])
            ->with('success', 'Data pemasukan berhasil dicatat!');
    }

    public function update(StoreTransactionRequest $request, Transaction $income): RedirectResponse
    {
        abort_if($income->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['type'] = TransactionType::INCOME->value;

        $this->transactionService->updateTransaction($income, $data);

        $txDate = Carbon::parse($data['transaction_date']);

        return redirect()->route('income.index', ['month' => $txDate->month, 'year' => $txDate->year])
            ->with('success', 'Data pemasukan berhasil diperbarui!');
    }

    public function destroy(Request $request, Transaction $income): RedirectResponse
    {
        abort_if($income->user_id !== $request->user()->id, 403);

        $txDate = Carbon::parse($income->transaction_date);
        $this->transactionService->deleteTransaction($income);

        return redirect()->route('income.index', ['month' => $txDate->month, 'year' => $txDate->year])
            ->with('success', 'Data pemasukan berhasil dihapus.');
    }
}
