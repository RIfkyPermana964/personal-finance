<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'cash_initial',
        'bank_initial',
        'total_salary',
    ];

    protected function casts(): array
    {
        return [
            'cash_initial' => 'decimal:2',
            'bank_initial' => 'decimal:2',
            'total_salary' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
