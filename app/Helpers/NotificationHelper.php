<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    // ==================== ROLE-BASED NOTIFICATION METHODS ====================
    
    /**
     * Get role name from role_id
     */
    private static function getRoleName($roleId)
    {
        $role = Role::find($roleId);
        return $role ? strtolower($role->role_name) : 'user';
    }
    
    /**
     * Get user role name
     */
    private static function getUserRole($userId)
    {
        $user = User::find($userId);
        return $user ? self::getRoleName($user->role_id) : 'user';
    }
    
    // ==================== ADMIN ROLE NOTIFICATIONS ====================
    
    /**
     * For admin role (role_id = 1 typically)
     */
    public static function adminNewUserRegistration($adminId, $userId, $userName)
    {
        $roleName = self::getUserRole($userId);
        return self::createForUser(
            $adminId,
            "New User Registration",
            "{$userName} has registered as {$roleName}",
            'user_registered',
            route('admin.users.show', $userId),
            'admin'
        );
    }
    
    public static function adminCaseEscalated($adminId, $caseId, $caseTitle, $fromUserId, $toUserId)
    {
        $fromRole = self::getUserRole($fromUserId);
        $toRole = self::getUserRole($toUserId);
        
        return self::createForUser(
            $adminId,
            "Case Escalation",
            "Case #{$caseId}: {$caseTitle} escalated from {$fromRole} to {$toRole}",
            'case_escalated',
            route('admin.cases.show', $caseId),
            'admin'
        );
    }
    
    // ==================== POLICE ROLE NOTIFICATIONS ====================
    
    /**
     * For police role
     */
    public static function policeNewFIR($policeId, $firId, $firNumber, $complainantName)
    {
        return self::createForUser(
            $policeId,
            "New FIR Assignment",
            "FIR #{$firNumber} - Complainant: {$complainantName}",
            'fir_assigned',
            route('police.cases', $firId),
            'police'
        );
    }
    
    public static function policeEvidenceSubmitted($policeId, $caseId, $evidenceType, $count)
    {
        return self::createForUser(
            $policeId,
            "Evidence Submitted",
            "{$count} {$evidenceType} items submitted for Case #{$caseId}",
            'evidence_submitted',
            route('police.cases.evidence', $caseId),
            'police'
        );
    }
    
    // ==================== FORENSIC ANALYST ROLE NOTIFICATIONS ====================
    
    /**
     * For forensic analyst role
     */
    public static function forensicCaseAssigned($analystId, $caseId, $caseTitle, $priority = 'normal')
    {
        return self::createForUser(
            $analystId,
            "New Case Assigned",
            "Case #{$caseId}: {$caseTitle} ({$priority} priority)",
            'case_assigned',
            route('forensic.assigned-cases.show', $caseId),
            'forensic'
        );
    }
    
    public static function forensicAnalysisComplete($analystId, $analysisId, $analysisType, $result)
    {
        return self::createForUser(
            $analystId,
            "Analysis Complete",
            "{$analysisType} analysis completed. Result: {$result}",
            'analysis_complete',
            route('forensic.analysis.show', $analysisId),
            'forensic'
        );
    }
    
    // ==================== PUBLIC USER ROLE NOTIFICATIONS ====================
    
    /**
     * For public user role
     */
    public static function publicFIRSubmitted($userId, $firId, $firNumber)
    {
        return self::createForUser(
            $userId,
            "FIR Submitted Successfully",
            "Your FIR #{$firNumber} has been registered. Case ID: {$firId}",
            'fir_submitted',
            route('public.fir.tracking', $firId),
            'public'
        );
    }
    
    public static function publicCaseStatusUpdate($userId, $caseId, $status)
    {
        return self::createForUser(
            $userId,
            "Case Status Updated",
            "Your case #{$caseId} is now '{$status}'",
            'status_update',
            route('public.complaints.track', $caseId),
            'public'
        );
    }
    
    // ==================== BROADCAST NOTIFICATIONS ====================
    
    /**
     * Broadcast to all users with specific role_id
     */
    public static function broadcastToRole($roleId, $title, $message, $type = 'info', $link = null)
    {
        $users = User::where('role_id', $roleId)
                    ->where('status', 'active')
                    ->get();
        
        $count = 0;
        foreach ($users as $user) {
            self::createForUser(
                $user->id,
                $title,
                $message,
                $type,
                $link,
                self::getRoleName($roleId)
            );
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Broadcast to all active users
     */
    public static function broadcastToAll($title, $message, $type = 'info', $link = null)
    {
        $users = User::where('status', 'active')->get();
        
        $count = 0;
        foreach ($users as $user) {
            self::createForUser(
                $user->id,
                $title,
                $message,
                $type,
                $link,
                self::getRoleName($user->role_id)
            );
            $count++;
        }
        
        return $count;
    }
    
    // ==================== PRIVATE HELPER METHODS ====================
    
    /**
     * Create notification for user with role-based preferences
     */
    public static function createForUser($userId, $title, $message, $type, $link = null, $module = null)
    {
        $user = User::find($userId);
        
        if (!$user) {
            Log::warning("NotificationHelper: User not found with ID: {$userId}");
            return null;
        }
        
        // Get user's role name
        $roleName = self::getRoleName($user->role_id);
        
        // Check if user should receive this notification based on role
        if (!self::shouldNotifyUser($user, $type, $module)) {
            return null;
        }
        
        try {
            $notification = Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'module' => $module ?: $roleName,
                'link' => $link,
                'read_at' => null,
                'created_at' => now()
            ]);
            
            // Log successful notification
            Log::info("Notification sent to user {$userId}: {$title}");
            
            return $notification;
            
        } catch (\Exception $e) {
            Log::error("NotificationHelper Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if user should receive notification
     */
    private static function shouldNotifyUser($user, $type, $module)
    {
        // Check user status
        if ($user->status !== 'active') {
            return false;
        }
        
        // Check if user is verified
        if (!$user->is_verified) {
            return false;
        }
        
        // Add additional checks here if needed
        // e.g., user notification preferences
        
        return true;
    }
    
    /**
     * Get icon based on notification type
     */
    public static function getIcon($type)
    {
        $icons = [
            // User management
            'user_registered' => 'fas fa-user-plus',
            'user_verified' => 'fas fa-user-check',
            'user_blocked' => 'fas fa-user-slash',
            
            // Case/FIR related
            'fir_submitted' => 'fas fa-file-contract',
            'fir_assigned' => 'fas fa-tasks',
            'case_assigned' => 'fas fa-briefcase',
            'case_escalated' => 'fas fa-level-up-alt',
            'case_completed' => 'fas fa-flag-checkered',
            
            // Evidence/Analysis
            'evidence_submitted' => 'fas fa-box-open',
            'analysis_complete' => 'fas fa-microscope',
            'report_generated' => 'fas fa-file-pdf',
            
            // Status updates
            'status_update' => 'fas fa-sync-alt',
            'deadline_reminder' => 'fas fa-clock',
            
            // System
            'system_alert' => 'fas fa-exclamation-triangle',
            'maintenance' => 'fas fa-tools',
            
            // Default
            'info' => 'fas fa-info-circle',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle',
        ];
        
        return $icons[$type] ?? 'fas fa-bell';
    }
    
    /**
     * Get CSS class based on notification type
     */
    public static function getCssClass($type)
    {
        $classes = [
            // Success types
            'user_verified' => 'notif-success',
            'fir_submitted' => 'notif-success',
            'case_completed' => 'notif-success',
            'analysis_complete' => 'notif-success',
            'success' => 'notif-success',
            
            // Info types
            'user_registered' => 'notif-info',
            'status_update' => 'notif-info',
            'info' => 'notif-info',
            
            // Warning types
            'deadline_reminder' => 'notif-warning',
            'maintenance' => 'notif-warning',
            'warning' => 'notif-warning',
            
            // Danger types
            'user_blocked' => 'notif-danger',
            'system_alert' => 'notif-danger',
            'error' => 'notif-danger',
            
            // Primary types (default)
            'fir_assigned' => 'notif-primary',
            'case_assigned' => 'notif-primary',
            'case_escalated' => 'notif-primary',
            'evidence_submitted' => 'notif-primary',
            'report_generated' => 'notif-primary',
        ];
        
        return $classes[$type] ?? 'notif-primary';
    }
    
    /**
     * Get readable role name
     */
    public static function getReadableRoleName($roleId)
    {
        $roleNames = [
            1 => 'Admin',
            2 => 'Police',
            3 => 'Forensic Analyst',
            4 => 'Public User',
            // Add more roles as needed
        ];
        
        return $roleNames[$roleId] ?? 'User';
    }
}