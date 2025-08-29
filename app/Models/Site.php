<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'total_area',
        'total_lots',
        'base_price_per_sqm',
        'reservation_fee',
        'membership_fee',
        'payment_plan',
        'amenities',
        'status',
        'image_url',
        'gallery_images',
        'latitude',
        'longitude',
        'is_active',
        'enable_12',
        'enable_24',
        'enable_cash',
        'price_12_months',
        'price_24_months',
        'price_cash',
        'enable_36',
        'price_36_months', 

    ];

    protected $casts = [
        'amenities' => 'array',
        'gallery_images' => 'array',
        'total_area' => 'decimal:2',
        'base_price_per_sqm' => 'decimal:2',
        'reservation_fee' => 'decimal:2',
        'membership_fee' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'interested_site_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function availableLots(): HasMany
    {
        return $this->hasMany(Lot::class)->where('status', 'disponible');
    }

    public function soldLots(): HasMany
    {
        return $this->hasMany(Lot::class)->where('status', 'vendu');
    }

    public function reservedLots(): HasMany
    {
        return $this->hasMany(Lot::class)->whereIn('status', ['reserve_temporaire', 'reserve']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}