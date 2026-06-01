<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AiPricingSuggestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('tenant')->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'profit_margin_percent' => ['nullable', 'numeric', 'min:0'],
            'materials' => ['nullable', 'array'],
            'variable_costs' => ['nullable', 'array'],
            'additional_costs' => ['nullable', 'array'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'hours_spent' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
