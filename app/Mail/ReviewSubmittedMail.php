<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Protocol;

class ReviewSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $protocol;
    public $reviewerName;

    /**
     * Create a new message instance.
     */
    public function __construct(Protocol $protocol, $reviewerName = 'Kelompok Reviewer')
    {
        //
        $this->protocol = $protocol;
        $this->reviewerName = $reviewerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // Subjek yang lebih jelas bagi peneliti
            subject: 'Update Progress: Hasil Telaah Masuk - ' . $this->protocol->perihal_pengajuan,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Kita arahkan ke folder resources/views/emails/review_submitted.blade.php
            view: 'emails.review_submitted',
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
