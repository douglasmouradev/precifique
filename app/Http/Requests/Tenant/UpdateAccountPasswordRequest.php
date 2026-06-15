<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdateAccountPasswordRequest extends FormRequest
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
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $tenant = Auth::guard('tenant')->user();
            if (! Hash::check((string) $this->input('current_password'), $tenant->password)) {
                $validator->errors()->add('current_password', 'Senha atual incorreta.');
            }
        });
    }
}
