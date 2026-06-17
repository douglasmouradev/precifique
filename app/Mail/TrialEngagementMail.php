<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEngagementMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly int $day,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.trial_engagement.subject', ['day' => $this->day]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trial-engagement',
        );
    }
}
