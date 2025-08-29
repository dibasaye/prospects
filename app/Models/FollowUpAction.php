<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUpAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospect_id',
        'user_id',
        'type',       // exemple : appel, whatsapp, rdv
        'notes',
        'follow_up_date',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
