<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'client_id',
        'site_id',
        'lot_id',
        'status',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_duration_months',
        'monthly_payment',
        'start_date',
        'end_date',
        'signature_date',
        'contract_file_url',
        'terms_and_conditions',
        'special_clauses',
        'generated_by',
        'signed_by_client',
        'signed_by_agent',
        'notes',
        'content',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'signature_date' => 'date',
        'terms_and_conditions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes pour les statuts
    const STATUS_DRAFT = 'brouillon';
    const STATUS_SIGNED = 'signe';
    const STATUS_CANCELLED = 'annule';
    const STATUS_COMPLETED = 'complete';

    public function client(): BelongsTo
    {
        return $this->belongsTo(Prospect::class, 'client_id');
    }
    
    /**
     * Get the payments for the contract.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function signedByClient(): BelongsTo
    {
        return $this->belongsTo(Prospect::class, 'signed_by_client');
    }

    public function signedByAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_agent');
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signe');
    }

    public function isSigned(): bool
    {
        return $this->status === self::STATUS_SIGNED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Calculer le pourcentage de paiement
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    /**
     * Vérifier si le contrat est entièrement payé
     */
    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    /**
     * Obtenir le montant restant à payer
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    /**
     * Générer un numéro de contrat unique
     */
    public static function generateContractNumber(): string
    {
        do {
            $number = 'CTR-' . now()->format('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('contract_number', $number)->exists());
        
        return $number;
    }

    /**
     * Vérifier si le contenu peut être modifié
     */
    public function canEditContent(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT]);
    }

    /**
     * Marquer le contrat comme signé
     */
    public function markAsSigned(?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_SIGNED,
            'signature_date' => now(),
            'signed_by_agent' => auth()->id(),
            'notes' => $notes
        ]);
    }

    /**
     * Scope pour les contrats actifs
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    /**
     * Scope pour les contrats de ce mois
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
    }
}