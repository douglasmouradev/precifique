<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDeliveryLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_webhook_id',
        'event',
        'http_status',
        'success',
        'error_message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(TenantWebhook::class, 'tenant_webhook_id');
    }
}
