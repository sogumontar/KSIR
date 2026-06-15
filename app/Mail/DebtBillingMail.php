<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DebtBillingMail extends Mailable
{
    use Queueable, SerializesModels;

    public float $totalDebt;
    public array $groupDebts;

    /**
     * Create a new message instance.
     */
    public function __construct(float $totalDebt, array $groupDebts)
    {
        $this->totalDebt = $totalDebt;
        $this->groupDebts = $groupDebts;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Monthly Debt Billing Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly_debt',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
