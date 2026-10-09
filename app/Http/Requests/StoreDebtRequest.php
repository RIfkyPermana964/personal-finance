<?php

namespace App\Http\Requests;

use App\Enums\DebtType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('total_amount')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $this->total_amount);
            $this->merge([
                'total_amount' => $cleaned !== '' ? (float) $cleaned : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(DebtType::class)],
            'name' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'rent_type' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Jenis transaksi (Hutang/Piutang/Sewa) wajib dipilih.',
            'name.required' => 'Nama pihak / item wajib diisi.',
            'total_amount.required' => 'Total nominal wajib diisi.',
            'total_amount.min' => 'Total nominal minimal Rp 1.',
        ];
    }
}
