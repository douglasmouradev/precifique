<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TenantApiToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function index(): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();

        $tokens = TenantApiToken::query()
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'abilities', 'last_used_at', 'expires_at', 'created_at']);

        return response()->json(['data' => $tokens]);
    }

    public function destroy(TenantApiToken $token): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();

        if ($token->tenant_id !== $tenant->id) {
            return response()->json(['message' => 'Token não encontrado.'], 404);
        }

        $token->delete();

        return response()->json(['message' => 'Token revogado.']);
    }
}
