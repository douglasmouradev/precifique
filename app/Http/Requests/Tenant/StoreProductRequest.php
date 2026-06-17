<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('tenant')->check() || Auth::guard('tenant_member')->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'niche_type' => ['required', 'in:alimentos,servico,artesanato'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $tenant = current_tenant();
            if ($tenant && $tenant->interface_mode === 'artesanato' && ! $this->hasFile('photo')) {
                $validator->errors()->add('photo', 'Foto obrigatória para artesanato.');
            }
        });
    }
}
