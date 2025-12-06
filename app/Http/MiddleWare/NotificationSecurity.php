<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationSecurity
{
    /**
     * Handle notification security checks
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Check if user has permission to send notifications
        if (!$this->hasNotificationPermission($user, $request)) {
            Log::warning("Unauthorized notification attempt", [
                'user_id' => $user->id ?? 'guest',
                'action' => $request->route()->getActionName(),
                'ip' => $request->ip()
            ]);
            
            // Don't send notification, but continue request
            $request->merge(['skip_notification' => true]);
        }
        
        // Sanitize notification data
        if ($request->has('notification_data')) {
            $request->merge([
                'notification_data' => $this->sanitizeNotificationData($request->notification_data)
            ]);
        }
        
        return $next($request);
    }
    
    /**
     * Check if user has notification permission
     */
    private function hasNotificationPermission($user, $request)
    {
        if (!$user) {
            return false;
        }
        
        // Police officers can only send notifications for their own cases
        if ($user->role_id == 2) { // Police role
            if ($request->has('case_id')) {
                $case = \App\Models\Complaint::find($request->case_id);
                if (!$case || $case->assigned_to != $user->id) {
                    return false;
                }
            }
        }
        
        // Check if user is active and verified
        if ($user->status !== 'active' || !$user->is_verified) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Sanitize notification data to prevent sensitive info leaks
     */
    private function sanitizeNotificationData($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        
        // Remove sensitive fields
        $sensitiveFields = [
            'password', 'token', 'secret', 'key', 'credit_card',
            'ssn', 'cnic', 'phone', 'address', 'email'
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        
        // Truncate long messages
        if (isset($data['message']) && strlen($data['message']) > 500) {
            $data['message'] = substr($data['message'], 0, 497) . '...';
        }
        
        return $data;
    }
}