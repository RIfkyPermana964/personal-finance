<?php

namespace App\Http\Requests;

use App\Enums\SavingGoalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSavingGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('target_amount')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $this->target_amount);
            $this->merge([
                'target_amount' => $cleaned !== '' ? (float) $cleaned : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'target_amount' => ['required', 'numeric', 'min:1000'],
            'target_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::enum(SavingGoalStatus::class)],
            'notes' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:25'],
        ];
    }
}
