<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSavingGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'target_amount' => ['required', 'numeric', 'min:1000'],
            'target_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:25'],
        ];
    }
}