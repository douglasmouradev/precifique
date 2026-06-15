<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantApiToken;
use App\Support\TenantApiAbilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function token(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
            'abilities' => ['nullable', 'array', 'min:1'],
            'abilities.*' => ['string', 'in:'.implode(',', array_keys(TenantApiAbilities::all()))],
        ]);

        $tenant = Tenant::query()->where('email', $data['email'])->first();
        if (! $tenant || ! Hash::check($data['password'], $tenant->password)) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        if (! $tenant->is_active) {
            return response()->json(['message' => 'Conta inativa.'], 403);
        }

        if (! $tenant->hasVerifiedEmail()) {
            return response()->json(['message' => 'E-mail não verificado.'], 403);
        }

        $abilities = $data['abilities'] ?? TenantApiAbilities::defaultForLogin();

        $plain = TenantApiToken::issue($tenant, $data['device_name'], $abilities);

        return response()->json([
            'token' => $plain,
            'token_type' => 'Bearer',
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'email' => $tenant->email,
            ],
        ]);
    }
}
