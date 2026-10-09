<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $methods = PaymentMethod::forUser($userId)->get();

        return view('payment-methods.index', compact('methods'));
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = true;

        PaymentMethod::create($data);

        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan!');
    }

    public function update(StorePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        // Boleh edit semua metode termasuk data bawaan (global)
        $data = $request->validated();
        $data['is_active'] = true;

        $paymentMethod->update($data);

        return redirect()->route('payment-methods.index')->with('success', "Metode '{$paymentMethod->name}' berhasil diperbarui!");
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->transactions()->exists()) {
            // Ada transaksi terkait — nonaktifkan saja, jangan hapus (jaga integritas)
            $paymentMethod->update(['is_active' => false]);
            $msg = "'{$paymentMethod->name}' dinonaktifkan karena sudah memiliki riwayat transaksi. Data historis tetap aman.";
        } else {
            $paymentMethod->delete();
            $msg = "Metode pembayaran '{$paymentMethod->name}' berhasil dihapus.";
        }

        return redirect()->route('payment-methods.index')->with('success', $msg);
    }
}
