<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationRateLimit
{
    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next, $type, $limit = 5, $timeframe = 60)
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return $next($request);
        }
        
        $key = "notification_rate_{$type}_{$userId}";
        $now = now()->timestamp;
        
        // Get existing attempts
        $attempts = session()->get($key, []);
        
        // Clean old attempts (older than timeframe)
        $attempts = array_filter($attempts, function($timestamp) use ($now, $timeframe) {
            return ($now - $timestamp) < $timeframe;
        });
        
        // Check if limit exceeded
        if (count($attempts) >= $limit) {
            Log::warning("Notification rate limit exceeded", [
                'user_id' => $userId,
                'type' => $type,
                'attempts' => count($attempts),
                'limit' => $limit,
                'timeframe' => $timeframe
            ]);
            
            // You can either:
            // 1. Return error response
            // return response()->json(['error' => 'Rate limit exceeded. Please try again later.'], 429);
            
            // 2. Or just skip notification (silently fail)
            $request->merge(['skip_notification' => true]);
            
            return $next($request);
        }
        
        // Add current attempt
        $attempts[] = $now;
        session()->put($key, $attempts);
        
        return $next($request);
    }
    
    /**
     * Terminate request - clean up old session data
     */
    public function terminate($request, $response)
    {
        // Clean up session data older than 24 hours
        $userId = auth()->id();
        if ($userId) {
            $prefix = "notification_rate_";
            foreach (session()->all() as $key => $value) {
                if (str_starts_with($key, $prefix)) {
                    // If it's an array of timestamps, clean old ones
                    if (is_array($value)) {
                        $cleaned = array_filter($value, function($timestamp) {
                            return (now()->timestamp - $timestamp) < 86400; // 24 hours
                        });
                        
                        if (empty($cleaned)) {
                            session()->forget($key);
                        } else {
                            session()->put($key, $cleaned);
                        }
                    }
                }
            }
        }
    }
}