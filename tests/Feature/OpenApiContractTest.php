<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class OpenApiContractTest extends TestCase
{
    public function test_openapi_paths_match_registered_api_routes(): void
    {
        $yaml = (string) file_get_contents(public_path('openapi.yaml'));
        preg_match_all('/^  (\/[^\s:]+):/m', $yaml, $matches);
        $documentedPaths = $matches[1] ?? [];

        $registered = collect(Route::getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'api/v1/'))
            ->map(function ($route) {
                $path = preg_replace('#^api/v1#', '', $route->uri()) ?? $route->uri();

                return '/'.ltrim($path, '/');
            })
            ->map(fn (string $path) => preg_replace('/\{[^}]+\}/', '{id}', $path) ?? $path)
            ->unique()
            ->values()
            ->all();

        foreach ($documentedPaths as $path) {
            $normalized = preg_replace('/\{[^}]+\}/', '{id}', $path) ?? $path;
            $this->assertContains(
                $normalized,
                $registered,
                "OpenAPI path {$path} is not registered in routes/api.php",
            );
        }
    }
}
