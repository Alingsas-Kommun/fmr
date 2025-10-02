<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use function App\Core\setting;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if API is enabled
        if (!setting('enable_api', false)) {
            return response()->json([
                'error' => 'API access disabled',
                'message' => 'API access is currently disabled'
            ], 403);
        }

        // Get API key from request (header or query parameter)
        $apiKey = $request->header('API-Key') ?? $request->query('api_key');
        
        if (!$apiKey) {
            return response()->json([
                'error' => 'API key required',
                'message' => 'Please provide an API key via API-Key header or api_key query parameter'
            ], 401);
        }

        // Get the configured API key from settings
        $configuredApiKey = setting('api_key');
        
        if (!$configuredApiKey || $apiKey !== $configuredApiKey) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid'
            ], 401);
        }

        return $next($request);
    }
}
