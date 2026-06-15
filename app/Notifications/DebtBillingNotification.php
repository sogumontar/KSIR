<?php

namespace App\Notifications;

use App\Mail\DebtBillingMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DebtBillingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public float $totalDebt;
    public array $groupDebts;

    /**
     * Create a new notification instance.
     */
    public function __construct(float $totalDebt, array $groupDebts)
    {
        $this->totalDebt = $totalDebt;
        $this->groupDebts = $groupDebts;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new DebtBillingMail($this->totalDebt, $this->groupDebts))
                    ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'total_debt' => $this->totalDebt,
            'message' => "Monthly reminder: You have an outstanding total debt of $" . number_format($this->totalDebt, 2),
        ];
    }
}
