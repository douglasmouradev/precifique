<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Support\TenantApiAbilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApiTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $abilities = array_keys(TenantApiAbilities::all());

        return [
            'name' => ['required', 'string', 'max:100'],
            'abilities' => ['nullable', 'array', 'min:1'],
            'abilities.*' => ['string', Rule::in($abilities)],
        ];
    }
}
