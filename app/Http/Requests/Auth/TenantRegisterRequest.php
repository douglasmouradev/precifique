<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\NotDisposableEmail;
use App\Services\TurnstileService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class TenantRegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email', new NotDisposableEmail()],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
            'company_website' => ['prohibited'],
            'cf-turnstile-response' => [
                Rule::requiredIf(fn () => app(TurnstileService::class)->isEnabled()),
                'string',
            ],
        ];
    }
}
