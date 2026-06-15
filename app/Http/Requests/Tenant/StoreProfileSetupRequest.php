<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Support\TenantNicheMapper;
use Illuminate\Foundation\Http\FormRequest;

class StoreProfileSetupRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function profileAttributes(): array
    {
        return array_merge(
            TenantNicheMapper::map($this->validated()),
            [
                'name' => $this->validated('name'),
                'profile_setup_completed' => true,
            ],
        );
    }
}
