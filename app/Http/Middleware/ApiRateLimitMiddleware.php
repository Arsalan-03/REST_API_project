<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\RateLimiter as RateLimiterFacade;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'api:' . ($request->ip() ?? 'unknown');
        
        if (RateLimiterFacade::tooManyAttempts($key, 60)) { // 60 запросов в минуту
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiterFacade::availableIn($key)
            ], 429);
        }
        
        RateLimiterFacade::hit($key, 60); // 60 секунд
        
        $response = $next($request);
        
        $response->headers->add([
            'X-RateLimit-Limit' => 60,
            'X-RateLimit-Remaining' => RateLimiterFacade::remaining($key, 60),
            'X-RateLimit-Reset' => time() + RateLimiterFacade::availableIn($key)
        ]);
        
        return $response;
    }
} 