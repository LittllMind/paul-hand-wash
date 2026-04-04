<?php

namespace App\Mail;

use App\Models\Evenement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InscriptionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public Evenement $evenement;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Evenement $evenement, User $user)
    {
        $this->evenement = $evenement;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Confirmation d\'inscription - ' . $this->evenement->titre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            markdown: 'emails.inscription-confirmation',
            with: [
                'evenement' => $this->evenement,
                'user' => $this->user,
            ],
        );
    }
}
