<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'lot_number',
        'area',
        'position',
        'status',
        'base_price',
        'position_supplement',
        'final_price',
        'client_id',
        'reserved_until',
        'description',
        'coordinates',
        'has_utilities',
        'features',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'base_price' => 'decimal:2',
        'position_supplement' => 'decimal:2',
        'final_price' => 'decimal:2',
        'reserved_until' => 'datetime',
        'coordinates' => 'array',
        'features' => 'array',
        'has_utilities' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Prospect::class, 'client_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    public function scopeReserved($query)
    {
        return $query->whereIn('status', ['reserve_temporaire', 'reserve']);
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'vendu');
    }

    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'disponible';
    }

    public function isReserved(): bool
    {
        return in_array($this->status, ['reserve_temporaire', 'reserve']);
    }

    public function isSold(): bool
    {
        return $this->status === 'vendu';
    }
}