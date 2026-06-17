<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\AIAssistantService;
use App\Services\AiUsageLimiter;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    public function __construct(
        private readonly AIAssistantService $ai,
        private readonly AuditService $audit,
        private readonly AiUsageLimiter $aiUsage,
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $tenant = current_tenant();
        $this->aiUsage->assertCanUse($tenant);

        $data = $request->validate(['question' => ['required', 'string', 'max:1000']]);

        $answer = $this->ai->helpChat(
            $data['question'],
            $tenant->niche->value ?? (string) $tenant->niche
        );

        $this->audit->log($tenant, 'ai.chat', null, [
            'question_length' => strlen($data['question']),
        ], $request);

        return response()->json(['answer' => $answer]);
    }
}
