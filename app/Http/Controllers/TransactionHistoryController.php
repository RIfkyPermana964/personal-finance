<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
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
        $filters = [
            'search' => $request->input('search'),
            'type' => $request->input('type'),
            'category_id' => $request->input('category_id'),
            'payment_method_id' => $request->input('payment_method_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'sort' => $request->input('sort', 'transaction_date'),
            'order' => $request->input('order', 'desc'),
        ];

        $transactions = $this->transactionService->getPaginatedTransactions($userId, $filters, 20);
        $categories = Category::forUser($userId)->where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $paymentMethods = PaymentMethod::forUser($userId)->where('is_active', true)->get();

        return view('transactions.index', compact('transactions', 'categories', 'paymentMethods', 'filters'));
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_if($transaction->user_id !== $request->user()->id, 403);

        $this->transactionService->deleteTransaction($transaction);

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}