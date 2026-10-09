<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordDebtPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $this->amount);
            $this->merge([
                'amount' => $cleaned !== '' ? (float) $cleaned : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'record_as_transaction' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal pembayaran/cicilan wajib diisi.',
            'amount.min' => 'Nominal pembayaran minimal Rp 1.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
        ];
    }
}
