<?php

namespace App\Models;

use App\Enums\SavingGoalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'status',
        'notes',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'status' => SavingGoalStatus::class,
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'target_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0.0;
        }

        $percentage = ($this->current_amount / $this->target_amount) * 100;
        return (float) min(100, round($percentage, 1));
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) max(0, $this->target_amount - $this->current_amount);
    }

    public function scopeActive($query)
    {
        return $query->where('status', SavingGoalStatus::ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', SavingGoalStatus::COMPLETED);
    }
}
