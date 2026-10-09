<?php

namespace App\Http\Controllers;

use App\Enums\DebtStatus;
use App\Enums\DebtType;
use App\Enums\TransactionType;
use App\Http\Requests\RecordDebtPaymentRequest;
use App\Http\Requests\StoreDebtRequest;
use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DebtController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $selectedType = $request->query('type', 'all');
        $selectedStatus = $request->query('status', 'all');
        $search = $request->query('search', '');

        // Query Utama
        $query = $user->debts()->with('payments.paymentMethod');

        if ($selectedType !== 'all') {
            $query->where('type', $selectedType);
        }

        if ($selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('rent_type', 'like', "%{$search}%");
            });
        }

        $debts = $query->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Rekap Statistik Global (Seluruh Data Aktif User)
        $allDebts = $user->debts()->get();

        $totalReceivableUnpaid = $allDebts->where('type', DebtType::RECEIVABLE)
            ->where('status', '!=', DebtStatus::PAID)
            ->sum('remaining_amount');

        $totalDebtUnpaid = $allDebts->where('type', DebtType::DEBT)
            ->where('status', '!=', DebtStatus::PAID)
            ->sum('remaining_amount');

        $totalRentUnpaid = $allDebts->where('type', DebtType::RENT)
            ->where('status', '!=', DebtStatus::PAID)
            ->sum('remaining_amount');

        $totalPaidOverall = $allDebts->sum('paid_amount');

        $counts = [
            'all' => $allDebts->count(),
            'receivable' => $allDebts->where('type', DebtType::RECEIVABLE)->count(),
            'debt' => $allDebts->where('type', DebtType::DEBT)->count(),
            'rent' => $allDebts->where('type', DebtType::RENT)->count(),
            'unpaid' => $allDebts->where('status', DebtStatus::UNPAID)->count(),
            'nyicil' => $allDebts->where('status', DebtStatus::NYICIL)->count(),
            'paid' => $allDebts->where('status', DebtStatus::PAID)->count(),
        ];

        $paymentMethods = $user->paymentMethods()->where('is_active', true)->orderBy('name')->get();

        return view('debts.index', compact(
            'debts',
            'selectedType',
            'selectedStatus',
            'search',
            'totalReceivableUnpaid',
            'totalDebtUnpaid',
            'totalRentUnpaid',
            'totalPaidOverall',
            'counts',
            'paymentMethods'
        ));
    }

    public function store(StoreDebtRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $totalAmount = (float) $request->total_amount;

        $debt = $user->debts()->create([
            'type' => $request->type,
            'name' => $request->name,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'remaining_amount' => $totalAmount,
            'start_date' => $request->start_date ?: now()->toDateString(),
            'due_date' => $request->due_date,
            'status' => DebtStatus::UNPAID,
            'rent_type' => $request->rent_type,
            'notes' => $request->notes,
        ]);

        $label = $debt->type->label();

        return redirect()->route('debts.index', ['type' => $debt->type->value])
            ->with('success', "{$label} '{$debt->name}' berhasil ditambahkan!");
    }

    public function update(StoreDebtRequest $request, Debt $debt): RedirectResponse
    {
        $this->authorizeDebt($debt);

        $debt->update([
            'type' => $request->type,
            'name' => $request->name,
            'total_amount' => (float) $request->total_amount,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'rent_type' => $request->rent_type,
            'notes' => $request->notes,
        ]);

        $debt->recalculate();

        return back()->with('success', "Data '{$debt->name}' berhasil diperbarui!");
    }

    public function destroy(Debt $debt): RedirectResponse
    {
        $this->authorizeDebt($debt);
        $name = $debt->name;
        $debt->delete();

        return back()->with('success', "Data '{$name}' beserta seluruh riwayat cicilannya berhasil dihapus.");
    }

    public function recordPayment(RecordDebtPaymentRequest $request, Debt $debt): RedirectResponse
    {
        $this->authorizeDebt($debt);

        $amount = (float) $request->amount;
        $paymentDate = $request->payment_date ?: now()->toDateString();
        $paymentMethodId = $request->payment_method_id;
        $notes = $request->notes;

        DB::transaction(function () use ($debt, $amount, $paymentDate, $paymentMethodId, $notes, $request) {
            // 1. Simpan cicilan / pembayaran
            $debt->payments()->create([
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'payment_method_id' => $paymentMethodId,
                'notes' => $notes,
            ]);

            // 2. Hitung ulang status & sisa
            $debt->recalculate();

            // 3. Opsional: Sinkronisasi ke Buku Kas (Transactions)
            if ($request->boolean('record_as_transaction')) {
                $user = Auth::user();

                if ($debt->type === DebtType::RECEIVABLE) {
                    // Menerima uang pelunasan dari peminjam -> PEMASUKAN
                    $category = $user->categories()->firstOrCreate(
                        ['name' => 'Pelunasan Piutang', 'type' => 'income'],
                        ['color' => '#10b981', 'icon' => 'banknotes']
                    );

                    $user->transactions()->create([
                        'type' => TransactionType::INCOME,
                        'amount' => $amount,
                        'transaction_date' => $paymentDate,
                        'category_id' => $category->id,
                        'payment_method_id' => $paymentMethodId,
                        'description' => "Terima Piutang: {$debt->name}".($notes ? " ({$notes})" : ''),
                    ]);
                } else {
                    // Membayar hutang kita / bayar sewa -> PENGELUARAN
                    $catName = $debt->type === DebtType::RENT ? 'Tagihan Sewa (Rent)' : 'Pembayaran Hutang';
                    $category = $user->categories()->firstOrCreate(
                        ['name' => $catName, 'type' => 'expense'],
                        ['color' => '#f43f5e', 'icon' => 'credit-card']
                    );

                    $user->transactions()->create([
                        'type' => TransactionType::EXPENSE,
                        'amount' => $amount,
                        'transaction_date' => $paymentDate,
                        'category_id' => $category->id,
                        'payment_method_id' => $paymentMethodId,
                        'description' => "Bayar {$debt->type->shortLabel()}: {$debt->name}".($notes ? " ({$notes})" : ''),
                    ]);
                }
            }
        });

        $formatted = number_format($amount, 0, ',', '.');

        return back()->with('success', "Pembayaran Rp {$formatted} untuk '{$debt->name}' berhasil dicatat! Status sekarang: {$debt->status->label()}.");
    }

    public function destroyPayment(Debt $debt, DebtPayment $payment): RedirectResponse
    {
        $this->authorizeDebt($debt);

        if ($payment->debt_id !== $debt->id) {
            abort(404);
        }

        $formatted = number_format($payment->amount, 0, ',', '.');
        $payment->delete();
        $debt->recalculate();

        return back()->with('success', "Riwayat pembayaran Rp {$formatted} berhasil dihapus.");
    }

    private function authorizeDebt(Debt $debt): void
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
