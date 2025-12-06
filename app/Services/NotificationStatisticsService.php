<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Complaint;
use App\Models\Media;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NotificationStatisticsService
{
    /**
     * Get daily statistics for police officer
     */
    public function getPoliceDailyStats($policeId)
    {
        $today = now()->toDateString();
        $cacheKey = "police_stats_{$policeId}_{$today}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function() use ($policeId, $today) {
            return [
                'daily_case_counts' => $this->getDailyCaseCounts($policeId),
                'pending_cases' => $this->getPendingCases($policeId),
                'high_priority_alerts' => $this->getHighPriorityAlerts($policeId),
                'evidence_upload_counts' => $this->getEvidenceUploadCounts($policeId),
                'forwarded_cases' => $this->getForwardedCases($policeId),
                'notification_stats' => $this->getNotificationStats($policeId),
            ];
        });
    }
    
    /**
     * Get daily case counts
     */
    private function getDailyCaseCounts($policeId)
    {
        return [
            'today' => Complaint::where('assigned_to', $policeId)
                ->whereDate('created_at', today())
                ->count(),
            'yesterday' => Complaint::where('assigned_to', $policeId)
                ->whereDate('created_at', today()->subDay())
                ->count(),
            'this_week' => Complaint::where('assigned_to', $policeId)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => Complaint::where('assigned_to', $policeId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }
    
    /**
     * Get pending cases
     */
    private function getPendingCases($policeId)
    {
        return Complaint::where('assigned_to', $policeId)
            ->whereIn('status', ['pending', 'under_review', 'investigating'])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
    
    /**
     * Get high priority alerts
     */
    private function getHighPriorityAlerts($policeId)
    {
        return [
            'high_priority' => Complaint::where('assigned_to', $policeId)
                ->where('severity', 'high')
                ->whereIn('status', ['pending', 'under_review'])
                ->count(),
            'overdue' => Complaint::where('assigned_to', $policeId)
                ->whereIn('status', ['pending', 'under_review'])
                ->where('created_at', '<', now()->subDays(3))
                ->count(),
            'urgent_evidence' => Media::where('user_id', $policeId)
                ->where('status', 'pending')
                ->where('created_at', '<', now()->subDays(1))
                ->count(),
        ];
    }
    
    /**
     * Get evidence upload counts
     */
    private function getEvidenceUploadCounts($policeId)
    {
        return [
            'total' => Media::where('user_id', $policeId)->count(),
            'today' => Media::where('user_id', $policeId)
                ->whereDate('created_at', today())
                ->count(),
            'by_type' => Media::where('user_id', $policeId)
                ->select('file_type', DB::raw('COUNT(*) as count'))
                ->groupBy('file_type')
                ->pluck('count', 'file_type')
                ->toArray(),
        ];
    }
    
    /**
     * Get forwarded cases
     */
    private function getForwardedCases($policeId)
    {
        return DB::table('complaint_status_logs')
            ->where('police_officer', $policeId)
            ->where('status', 'forwarded')
            ->select(
                DB::raw('DATE(changed_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(changed_at)'))
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->toArray();
    }
    
    /**
     * Get notification statistics
     */
    private function getNotificationStats($policeId)
    {
        return [
            'sent_today' => Notification::where('user_id', $policeId)
                ->whereDate('created_at', today())
                ->count(),
            'unread' => Notification::where('user_id', $policeId)
                ->whereNull('read_at')
                ->count(),
            'by_type' => Notification::where('user_id', $policeId)
                ->select('type', DB::raw('COUNT(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }
    
    /**
     * Get statistics for notification
     */
    public function getNotificationSummary($policeId)
    {
        $stats = $this->getPoliceDailyStats($policeId);
        
        $summary = "📊 Daily Statistics:\n";
        $summary .= "• Cases today: " . ($stats['daily_case_counts']['today'] ?? 0) . "\n";
        $summary .= "• Pending cases: " . array_sum($stats['pending_cases'] ?? []) . "\n";
        $summary .= "• High priority: " . ($stats['high_priority_alerts']['high_priority'] ?? 0) . "\n";
        $summary .= "• Evidence uploaded: " . ($stats['evidence_upload_counts']['today'] ?? 0) . "\n";
        $summary .= "• Cases forwarded: " . count($stats['forwarded_cases'] ?? []) . "\n";
        
        // Add alerts if any high priority
        if (($stats['high_priority_alerts']['high_priority'] ?? 0) > 0) {
            $summary .= "\n⚠️ **ALERT**: High priority cases need attention!";
        }
        
        return $summary;
    }
}