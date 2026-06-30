<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Enums\ProfitMargin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdatePricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_custom_order' => ['boolean'],
            'production_time_minutes' => ['nullable', 'integer', 'min:0'],
            'profit_margin_percent' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
            'materials' => ['nullable', 'array'],
            'variable_costs' => ['nullable', 'array'],
            'additional_costs' => ['nullable', 'array'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'hours_spent' => ['nullable', 'numeric', 'min:0'],
            'niche_fields' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $margin = (float) $this->input('profit_margin_percent');
            $tenant = current_tenant();

            if ($margin === (float) ProfitMargin::HundredFifty->value && ! $tenant?->isPremium()) {
                $validator->errors()->add('profit_margin_percent', __('messages.pricing.premium_margin_only'));
            }

            $allowed = array_map(fn (ProfitMargin $m) => $m->value, ProfitMargin::forPlan(
                $tenant?->isPremium() ? 'premium' : 'basic'
            ));

            if (! in_array((int) $margin, $allowed, true)) {
                $validator->errors()->add('profit_margin_percent', __('messages.pricing.invalid_margin'));
            }
        });
    }
}
