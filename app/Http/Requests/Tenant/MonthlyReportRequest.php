<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'year' => ['sometimes', 'integer', 'min:2020', 'max:2100'],
            'month' => ['sometimes', 'integer', 'min:1', 'max:12'],
        ];
    }

    public function year(): int
    {
        return (int) $this->input('year', now()->year);
    }

    public function month(): int
    {
        return (int) $this->input('month', now()->month);
    }
}
