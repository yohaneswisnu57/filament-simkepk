<?php

namespace App\Mail;

use App\Models\Protocol;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewAssignmentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $protocol;

    public ?User $reviewer;

    /**
     * Create a new message instance.
     */
    public function __construct(Protocol $protocol, ?User $reviewer = null)
    {
        //
        $this->protocol = $protocol;
        $this->reviewer = $reviewer;
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tugas Reviewer Protokol Baru',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.protocol_assignment_reviewer',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
