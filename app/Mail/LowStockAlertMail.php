<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LowStockAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @param  Collection<int, Product>  $products */
    public function __construct(
        public readonly Tenant $tenant,
        public readonly Collection $products,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.low_stock.subject'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.low-stock');
    }
}
