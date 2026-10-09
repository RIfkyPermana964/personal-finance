<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionHistoryController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $month = $request->filled('month') ? (int) $request->input('month') : null;
        $year = $request->filled('year') ? (int) $request->input('year') : null;

        $filters = [
            'search' => $request->input('search'),
            'type' => $request->input('type'),
            'category_id' => $request->input('category_id'),
            'payment_method_id' => $request->input('payment_method_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'month' => $month,
            'year' => $year,
            'sort' => $request->input('sort', 'transaction_date'),
            'order' => $request->input('order', 'desc'),
        ];

        $transactions = $this->transactionService->getPaginatedTransactions($userId, $filters, 20);

        // Compute summary KPIs respecting active filters
        $summaryQuery = Transaction::where('user_id', $userId);
        if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
            $summaryQuery->whereBetween('transaction_date', [$filters['start_date'], $filters['end_date']]);
        } elseif (! empty($filters['month']) && ! empty($filters['year'])) {
            $summaryQuery->whereYear('transaction_date', $filters['year'])
                ->whereMonth('transaction_date', $filters['month']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $summaryQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('paymentMethod', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }
        if (! empty($filters['category_id'])) {
            $summaryQuery->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['payment_method_id'])) {
            $summaryQuery->where('payment_method_id', $filters['payment_method_id']);
        }

        $totalIncome = (clone $summaryQuery)->where('type', TransactionType::INCOME)->sum('amount');
        $totalExpense = (clone $summaryQuery)->where('type', TransactionType::EXPENSE)->sum('amount');
        $netCashflow = $totalIncome - $totalExpense;

        $categories = Category::forUser($userId)->where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $expenseCategories = Category::with('children')->forUser($userId)->where('type', CategoryType::EXPENSE)->whereNull('parent_id')->where('is_active', true)->get();
        $incomeCategories = Category::forUser($userId)->where('type', CategoryType::INCOME)->where('is_active', true)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::forUser($userId)->where('is_active', true)->get();

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'expenseCategories',
            'incomeCategories',
            'paymentMethods',
            'filters',
            'totalIncome',
            'totalExpense',
            'netCashflow',
            'month',
            'year'
        ));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['type'] = $data['type'] ?? TransactionType::EXPENSE->value;

        $this->transactionService->createTransaction($request->user()->id, $data);

        $typeLabel = $data['type'] === TransactionType::INCOME->value ? 'pemasukan' : 'pengeluaran';

        return redirect()->route('transactions.index')
            ->with('success', "Data {$typeLabel} berhasil dicatat!");
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        abort_if($transaction->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $this->transactionService->updateTransaction($transaction, $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_if($transaction->user_id !== $request->user()->id, 403);

        $this->transactionService->deleteTransaction($transaction);

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
