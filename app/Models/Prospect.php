<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'phone_secondary',
        'email',
        'address',
        'id_document',
        'representative_name',
        'representative_phone',
        'representative_address',
        'status',
        'assigned_to_id',
        'interested_site_id',
        'notes',
        'budget_min',
        'budget_max',
        'contact_date',
        'next_follow_up',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
        'contact_date' => 'date',
        'next_follow_up' => 'date',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function interestedSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'interested_site_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'client_id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class, 'client_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to_id', $userId);
    }
}