<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
            'type' => ['nullable', Rule::enum(TransactionType::class)],
            'amount' => ['required', 'numeric', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'saving_goal_id' => ['nullable', 'exists:saving_goals,id'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.min' => 'Nominal transaksi minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
        ];
    }
}
