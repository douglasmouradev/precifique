<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email_low_stock' => ['sometimes', 'boolean'],
            'email_trial' => ['sometimes', 'boolean'],
            'email_payment_failed' => ['sometimes', 'boolean'],
            'in_app' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, bool> */
    public function preferences(): array
    {
        return [
            'email_low_stock' => $this->boolean('email_low_stock'),
            'email_trial' => $this->boolean('email_trial'),
            'email_payment_failed' => $this->boolean('email_payment_failed'),
            'in_app' => $this->boolean('in_app'),
        ];
    }
}
