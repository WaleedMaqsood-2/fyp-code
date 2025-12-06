@php
    $user = auth()->user();
    $notifications = $user->notifications()->take(10)->get();
    $unreadCount = $user->unreadNotificationsCount();
@endphp


<li class="nav-item dropdown">
    <a class="nav-link position-relative" href="#" id="notificationDropdown" 
       role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell fa-lg"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
                <span class="visually-hidden">unread notifications</span>
            </span>
        @endif
    </a>
    
    <ul class="dropdown-menu dropdown-menu-end dropdown-notifications" 
        aria-labelledby="notificationDropdown">
        <li class="dropdown-header">
            <h6 class="mb-0">Notifications</h6>
            @if($unreadCount > 0)
                <a href="javascript:void(0);" class="text-primary small" onclick="markAllAsRead()">
                    Mark all as read
                </a>
            @endif
        </li>

        <li>
            <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                @forelse($notifications as $notification)
                    <a href="{{ $notification->link ?? '#' }}" 
                     class="dropdown-item notification-item{{ $notification->is_unread ? 'unread' : '' }}"

                       data-id="{{ $notification->id }}"
                       onclick="markAsRead(this, event)">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <i class="{{ $notification->icon }} {{ $notification->css_class }} p-2 rounded"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $notification->title }}</h6>
                                <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $notification->time_ago }}</small>
                                    @if($notification->module)
                                        <span class="badge bg-light text-dark">{{ ucfirst($notification->module) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    @if(!$loop->last)
                        <div class="dropdown-divider"></div>
                    @endif
                @empty
                    <div class="dropdown-item text-center py-4">
                        <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No notifications yet</p>
                    </div>
                @endforelse
            </div>
        </li>
        
        @if($notifications->count() > 0)
            <li>
                <div class="dropdown-footer text-center py-2">
                    <a href="{{ route('notifications.index') }}" class="text-primary">
                        View all notifications <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </li>
        @endif
    </ul>
</li>

<style>
.notification-item {
    padding: 12px 15px;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: rgba(13, 110, 253, 0.05);
    border-left: 3px solid #0d6efd;
}

.notification-icon .notif-primary {
    background-color: #e3f2fd;
    color: #1976d2;
}

.notification-icon .notif-success {
    background-color: #e8f5e9;
    color: #388e3c;
}

.notification-icon .notif-warning {
    background-color: #fff8e1;
    color: #f57c00;
}

.notification-icon .notif-danger {
    background-color: #ffebee;
    color: #d32f2f;
}

.notification-icon .notif-info {
    background-color: #e1f5fe;
    color: #0288d1;
}

.dropdown-notifications {
    width: 350px;
    max-width: 90vw;
}

.notification-list {
    scrollbar-width: thin;
}

.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
function markAsRead(element, event) {
    const notificationId = element.getAttribute('data-id');
    
    // If it's already read, don't send request
    if (!element.classList.contains('unread')) {
        return;
    }
    
    // Send AJAX request
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            // Remove unread class
            element.classList.remove('unread');
            // Update badge count
            updateNotificationBadge();
        }
    }).catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            // Remove all unread classes
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            // Hide notification badge
            updateNotificationBadge();
        }
    });
}

function updateNotificationBadge() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.badge.bg-danger');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
}

// Auto-update notifications every 30 seconds
setInterval(() => {
    // You can implement auto-refresh if needed
}, 30000);
</script>