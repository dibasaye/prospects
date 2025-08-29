<?php

namespace App\Notifications;

use App\Models\Prospect;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ProspectAssigned extends Notification
{
    use Queueable;

    protected $prospect;

    public function __construct(Prospect $prospect)
    {
        $this->prospect = $prospect;
    }

    public function via($notifiable)
    {
        return ['database']; // enregistre la notification en base
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Le prospect : " . $this->prospect->full_name . " vous a été assigné.",
            'prospect_id' => $this->prospect->id,
            'phone' => $this->prospect->phone,
            'email' => $this->prospect->email,
        ];
    }
}
