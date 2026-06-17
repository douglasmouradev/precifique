<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Support\TenantNicheMapper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAccountProfileRequest extends FormRequest
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
        $tenantId = current_tenant()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('tenants', 'email')->ignore($tenantId)],
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
            'niche_other' => ['nullable', 'string', 'max:255'],
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
                'email' => $this->validated('email'),
            ],
        );
    }
}
