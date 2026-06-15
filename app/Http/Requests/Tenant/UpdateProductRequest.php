<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('tenant')->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'niche_type' => ['required', 'in:alimentos,servico,artesanato'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'remove_photo' => ['sometimes', 'boolean'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $tenant = Auth::guard('tenant')->user();
            $product = $this->route('product');

            if (
                $tenant
                && $tenant->interface_mode === 'artesanato'
                && ! $this->hasFile('photo')
                && ! $this->boolean('remove_photo')
                && ! $product?->photo_path
            ) {
                $validator->errors()->add('photo', 'Foto obrigatória para artesanato.');
            }
        });
    }
}
