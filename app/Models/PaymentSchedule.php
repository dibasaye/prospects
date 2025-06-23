<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'installment_number',
        'amount',
        'due_date',
        'is_paid',
        'paid_date',
        'payment_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'is_paid' => 'boolean',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_paid', false)
                    ->where('due_date', '<', now());
    }

    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('is_paid', false)
                    ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }
}