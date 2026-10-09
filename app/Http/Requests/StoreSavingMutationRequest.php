<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSavingMutationRequest extends FormRequest
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
            'type' => ['required', 'in:deposit,withdraw'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
