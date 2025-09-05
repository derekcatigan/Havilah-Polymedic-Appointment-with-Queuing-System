<?php

namespace App\Mail;

use App\Models\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QueueStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $queue;
    public $statusMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Queue $queue, string $statusMessage)
    {
        $this->queue = $queue;
        $this->statusMessage = $statusMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Queue Status Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.queue-status',
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
