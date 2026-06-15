<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Support\TenantNicheMapper;
use Illuminate\Foundation\Http\FormRequest;

class OnboardingNicheRequest extends FormRequest
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
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
            'niche_other' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function nicheAttributes(): array
    {
        return TenantNicheMapper::map($this->validated());
    }
}
