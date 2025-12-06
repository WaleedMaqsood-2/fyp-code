<?php

namespace App\Helpers;

class NotificationTypes
{
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';
    
    // Police-specific types
    const POLICE_FIR_CREATED = 'police_fir_created';
    const POLICE_CASE_FORWARDED = 'police_case_forwarded';
    const POLICE_EVIDENCE_UPLOADED = 'police_evidence_uploaded';
    const POLICE_CASE_UPDATED = 'police_case_updated';
    
    // Color mapping for UI
    const COLORS = [
        'success' => '#10B981',
        'info' => '#3B82F6',
        'warning' => '#F59E0B',
        'danger' => '#EF4444',
        'police_fir_created' => '#10B981',
        'police_case_forwarded' => '#3B82F6',
        'police_evidence_uploaded' => '#8B5CF6',
        'police_case_updated' => '#F59E0B',
    ];
    
    // Icon mapping
    const ICONS = [
        'success' => 'fas fa-check-circle',
        'info' => 'fas fa-info-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'danger' => 'fas fa-exclamation-circle',
        'police_fir_created' => 'fas fa-file-contract',
        'police_case_forwarded' => 'fas fa-share-square',
        'police_evidence_uploaded' => 'fas fa-upload',
        'police_case_updated' => 'fas fa-edit',
    ];
    
    /**
     * Get appropriate type based on action
     */
    public static function getType($action, $data = null)
    {
        $typeMap = [
            // FIR related
            'fir_created' => self::SUCCESS,
            'fir_updated' => self::INFO,
            'fir_deleted' => self::WARNING,
            
            // Case related
            'case_forwarded' => self::POLICE_CASE_FORWARDED,
            'case_status_updated' => self::POLICE_CASE_UPDATED,
            'case_completed' => self::SUCCESS,
            'case_rejected' => self::WARNING,
            
            // Evidence related
            'evidence_uploaded' => self::POLICE_EVIDENCE_UPLOADED,
            'evidence_approved' => self::SUCCESS,
            'evidence_rejected' => self::WARNING,
            'evidence_deleted' => self::DANGER,
            
            // Search & Views
            'search_performed' => self::INFO,
            'dashboard_accessed' => self::INFO,
            'report_viewed' => self::INFO,
            
            // High priority alerts
            'high_priority_case' => self::DANGER,
            'deadline_approaching' => self::WARNING,
            'urgent_action_required' => self::DANGER,
        ];
        
        return $typeMap[$action] ?? self::INFO;
    }
    
    /**
     * Get CSS class for notification type
     */
    public static function getCssClass($type)
    {
        $classes = [
            'success' => 'notif-success',
            'info' => 'notif-info',
            'warning' => 'notif-warning',
            'danger' => 'notif-danger',
            'police_fir_created' => 'notif-success',
            'police_case_forwarded' => 'notif-primary',
            'police_evidence_uploaded' => 'notif-info',
            'police_case_updated' => 'notif-warning',
        ];
        
        return $classes[$type] ?? 'notif-primary';
    }
}