<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\MonthlyGoal;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyGoalReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly MonthlyGoal $goal,
        public readonly float $revenue,
        public readonly float $progress,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Lembrete de meta — Precifique');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.goal-reminder');
    }
}
