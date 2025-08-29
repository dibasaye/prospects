<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PaymentSchedule;

class PaymentReceiptNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $schedule;

    public function __construct(PaymentSchedule $schedule)
    {
        $this->schedule = $schedule;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $pdf = Pdf::loadView('emails.receipt_pdf', ['schedule' => $this->schedule]);

        return (new MailMessage)
            ->subject('Reçu de paiement - Contrat #' . $this->schedule->contract->contract_number)
            ->greeting('Bonjour ' . $notifiable->full_name)
            ->line('Nous confirmons la réception de votre paiement.')
            ->line('Veuillez trouver ci-joint votre reçu.')
            ->attachData($pdf->output(), 'recu_paiement.pdf', [
                'mime' => 'application/pdf',
            ])
            ->line('Merci pour votre confiance.');
    }
}
