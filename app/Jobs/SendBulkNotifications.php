<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Notification;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Log;

class SendBulkNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;
    protected $title;
    protected $message;
    protected $type;
    protected $link;
    protected $module;
    
    public $tries = 3;
    public $timeout = 120;

    public function __construct($userIds, $title, $message, $type = 'info', $link = null, $module = null)
    {
        $this->userIds = is_array($userIds) ? $userIds : [$userIds];
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->link = $link;
        $this->module = $module;
    }

    public function handle()
    {
        $successCount = 0;
        $failCount = 0;
        
        foreach ($this->userIds as $userId) {
            try {
                // Use rate limiting in job
                $rateLimitKey = md5($this->title . $userId . date('Y-m-d'));
                
                NotificationHelper::createForUserWithRateLimit(
                    $userId,
                    $this->title,
                    $this->message,
                    $this->type,
                    $this->link,
                    $this->module,
                    $rateLimitKey
                );
                
                $successCount++;
                
                // Small delay to prevent overwhelming
                if (count($this->userIds) > 10) {
                    usleep(100000); // 0.1 second delay
                }
                
            } catch (\Exception $e) {
                Log::error("Failed to send notification to user {$userId}: " . $e->getMessage());
                $failCount++;
            }
        }
        
        Log::info("Bulk notification job completed", [
            'success' => $successCount,
            'failed' => $failCount,
            'total' => count($this->userIds),
            'title' => $this->title
        ]);
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error("Bulk notification job failed: " . $exception->getMessage(), [
            'title' => $this->title,
            'user_count' => count($this->userIds)
        ]);
    }
}