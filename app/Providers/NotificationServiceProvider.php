<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share notifications with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Get unread notifications count
                $unreadCount = Notification::where('user_id', $user->id)
                    ->where('read_at', null)
                    ->count();
                
                // Get latest notifications (5-10)
                $notifications = Notification::where('user_id', $user->id)
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'title' => $notification->title,
                            'description' => $notification->description,
                            'type' => $notification->type,
                            'icon' => $this->getIconByType($notification->type),
                            'link' => $notification->link,
                            'time' => $notification->created_at->diffForHumans(),
                            'read' => $notification->read_at !== null
                        ];
                    });
                
                $view->with([
                    'notificationUnreadCount' => $unreadCount,
                    'notifications' => $notifications
                ]);
            } else {
                $view->with([
                    'notificationUnreadCount' => 0,
                    'notifications' => collect()
                ]);
            }
        });
    }
    
    private function getIconByType($type)
    {
        $icons = [
            'case_assigned' => 'fa fa-folder',
            'case_updated' => 'fa fa-edit',
            'evidence_ready' => 'fa fa-box',
            'report_ready' => 'fa fa-file-text',
            'system' => 'fa fa-cog',
            'alert' => 'fa fa-exclamation-triangle',
            'success' => 'fa fa-check-circle',
            'info' => 'fa fa-info-circle',
            'warning' => 'fa fa-exclamation-circle',
            'error' => 'fa fa-times-circle'
        ];
        
        return $icons[$type] ?? 'fa fa-bell';
    }
}