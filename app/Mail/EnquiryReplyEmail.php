<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryReplyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address("support@planetofmedicine.com", "POM (Planet Of Medicine) Customer Support"),
            subject: 'POM - ' . $this->data['title'] ?? "Answering Your Enquiry",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiry-reply',
            with: [
                'title' => $this->data['title'] ?? null,
                'body'  => $this->data['body'] ?? ""
            ]
        );
    }

    /**
     * Get the attachments for the message.
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
