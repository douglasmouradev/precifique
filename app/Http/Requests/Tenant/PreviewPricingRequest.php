<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class PreviewPricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'profit_margin_percent' => ['required', 'numeric', 'min:0'],
            'materials' => ['nullable', 'array'],
            'materials.*.material_name' => ['nullable', 'string', 'max:255'],
            'materials.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'materials.*.unit' => ['nullable', 'string', 'max:20'],
            'materials.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
            'variable_costs' => ['nullable', 'array'],
            'variable_costs.*.name' => ['nullable', 'string', 'max:255'],
            'variable_costs.*.amount' => ['nullable', 'numeric', 'min:0'],
            'additional_costs' => ['nullable', 'array'],
            'additional_costs.*.name' => ['nullable', 'string', 'max:255'],
            'additional_costs.*.amount' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'hours_spent' => ['nullable', 'numeric', 'min:0'],
            'margins' => ['nullable', 'array'],
            'margins.*' => ['numeric', 'min:0'],
        ];
    }
}
