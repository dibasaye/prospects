<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospect_id',
        'lot_id',
        'reserved_at',
        'expires_at',
        'status', // 'active', 'expired', 'cancelled'
    ];
    protected $casts = [
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function client()
{
    return $this->belongsTo(Prospect::class, 'client_id');
}

}
