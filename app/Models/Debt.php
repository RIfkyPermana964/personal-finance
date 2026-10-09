<?php

namespace App\Models;

use App\Enums\DebtStatus;
use App\Enums\DebtType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'start_date',
        'due_date',
        'status',
        'rent_type',
        'notes',
    ];

    protected $casts = [
        'type' => DebtType::class,
        'status' => DebtStatus::class,
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class)->orderBy('payment_date', 'desc');
    }

    /**
     * Hitung ulang status & sisa setelah pembayaran/cicilan
     */
    public function recalculate(): void
    {
        $totalPaid = (float) $this->payments()->sum('amount');
        $total = (float) $this->total_amount;
        $remaining = max(0, $total - $totalPaid);

        $status = DebtStatus::UNPAID;
        if ($remaining <= 0) {
            $status = DebtStatus::PAID;
        } elseif ($totalPaid > 0) {
            $status = DebtStatus::NYICIL;
        }

        $this->update([
            'paid_amount' => $totalPaid,
            'remaining_amount' => $remaining,
            'status' => $status,
        ]);
    }

    /**
     * Persentase pelunasan
     */
    public function progressPercentage(): int
    {
        if ((float) $this->total_amount <= 0) {
            return 100;
        }

        $percent = ((float) $this->paid_amount / (float) $this->total_amount) * 100;

        return (int) min(100, max(0, round($percent)));
    }
}
