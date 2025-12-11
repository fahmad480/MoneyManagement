<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;
use App\Models\ApiLog;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key is required. Please provide X-API-Key in header.'
            ], 401);
        }

        $key = ApiKey::where('key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive API Key.'
            ], 401);
        }

        // Update last used timestamp
        $key->updateLastUsed();

        // Store API key and user in request for later use
        $request->merge([
            'api_key_id' => $key->id,
            'user_id' => $key->user_id
        ]);

        // Process request
        $response = $next($request);

        // Calculate response time
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds

        // Log the request
        ApiLog::create([
            'api_key_id' => $key->id,
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'status_code' => $response->status(),
            'request_body' => json_encode($request->except(['password', 'password_confirmation'])),
            'response_body' => $response->getContent(),
            'response_time' => $responseTime,
        ]);

        return $response;
    }
}

