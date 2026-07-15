<?php

namespace App\Http\Middleware;

use App\Services\Settings\SettingsManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    /**
     * Handle an incoming request.
     * Validates API key from X-API-Key header or api_key query parameter (local environment only).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $this->getApiKeyFromRequest($request);

        if (! $apiKey) {
            return $this->unauthorizedResponse('API key is required');
        }

        $validKey = $this->settings->get('app.api_key');

        if (! $validKey || ! hash_equals($validKey, $apiKey)) {
            return $this->unauthorizedResponse('Invalid API key');
        }

        return $next($request);
    }

    /**
     * Extract API key from request (X-API-Key header or query param in local environment only).
     */
    protected function getApiKeyFromRequest(Request $request): ?string
    {
        // Check X-API-Key header (primary method)
        if ($request->header('X-API-Key')) {
            return $request->header('X-API-Key');
        }

        // Check query parameter (only allowed in local development environment when APP_ENV=local)
        if ($request->has('api_key') && $this->isLocalEnvironment()) {
            return $request->input('api_key');
        }

        return null;
    }

    /**
     * Check if application is running in local environment.
     */
    protected function isLocalEnvironment(): bool
    {
        return app()->environment('local');
    }

    /**
     * Return unauthorized JSON response.
     */
    protected function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => $message,
        ], 401);
    }
}
