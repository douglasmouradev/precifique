<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
