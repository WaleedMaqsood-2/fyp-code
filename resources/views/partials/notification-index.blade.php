@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Notifications</h4>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="markAllAsRead()">
                            <i class="fas fa-check-double me-1"></i> Mark all as read
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="clearAllNotifications()">
                            <i class="fas fa-trash me-1"></i> Clear all
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <a href="{{ $notification->link ?? '#' }}" 
                               class="list-group-item list-group-item-action {{ $notification->is_unread ? 'bg-light' : '' }}
"

                               data-id="{{ $notification->id }}">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="d-flex align-items-start flex-grow-1">
                                        <div class="me-3">
                                            <i class="{{ $notification->icon }} fa-lg {{ $notification->css_class }} p-2 rounded"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $notification->title }}</h6>
                                            <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>{{ $notification->time_ago }}
                                                </small>
                                                @if($notification->module)
                                                    <span class="badge bg-secondary">{{ ucfirst($notification->module) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($notification->is_unread)
                                        <button class="btn btn-sm btn-outline-primary mark-read-btn" 
                                                onclick="markSingleAsRead(event, this)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No notifications</h5>
                                <p class="text-muted">You don't have any notifications yet.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($notifications->hasPages())
                        <div class="card-footer bg-white">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function markSingleAsRead(event, button) {
        event.preventDefault();
    event.stopPropagation();
    
    const notificationItem = button.closest('.list-group-item');
    const notificationId = notificationItem.getAttribute('data-id');
    
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            notificationItem.classList.remove('bg-light');
            button.remove();
            updateHeaderBadge();
        }
    });
}

function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;
    
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
}

function clearAllNotifications() {
    if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) return;
    
    fetch('/notifications/clear-all', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
}

function updateHeaderBadge() {
    // Update the badge in header if we're on the same page
    if (typeof updateNotificationBadge === 'function') {
        updateNotificationBadge();
    }
}
</script>
@endsection