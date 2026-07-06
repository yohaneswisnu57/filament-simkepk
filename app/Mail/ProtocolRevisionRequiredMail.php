<?php

namespace App\Mail;

use App\Models\Protocol;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProtocolRevisionRequiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $protocol;

    /**
     * Create a new message instance.
     */
    public function __construct(Protocol $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Revisi Diperlukan untuk Protokol KEPK: ' . $this->protocol->perihal_pengajuan,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.protocols.revision_required',
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
