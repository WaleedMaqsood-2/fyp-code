@extends('layouts.master')

@push('styles')
<style>
/* Modern Public Alerts Styles */
.alerts-container {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.alerts-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    color: white;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
}

.header-content {
    flex: 1;
}

.header-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-title i {
    font-size: 2.2rem;
    opacity: 0.9;
}

.header-subtitle {
    opacity: 0.9;
    margin: 0;
    font-size: 1rem;
}

.header-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-item {
    background: rgba(255, 255, 255, 0.2);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 180px;
}

.stat-item i {
    font-size: 2rem;
    opacity: 0.9;
}

.stat-item h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.stat-item span {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Alerts */
.alert-modern {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}

.alert-modern.alert-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.alert-modern.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
}

.alert-content {
    flex: 1;
}

.alert-content h6 {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.alert-close {
    background: none;
    border: none;
    color: inherit;
    opacity: 0.7;
    cursor: pointer;
    transition: opacity 0.3s;
    padding: 0;
}

.alert-close:hover {
    opacity: 1;
}

/* Main Layout */
.alerts-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 1024px) {
    .alerts-layout {
        grid-template-columns: 1fr;
    }
}

/* Create Alert Card */
.create-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #eef2f7;
    height: fit-content;
    position: sticky;
    top: 1.5rem;
}

.card-header-create {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eef2f7;
}

.card-header-create i {
    font-size: 1.5rem;
    color: #667eea;
}

.card-header-create h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    color: #2d3748;
}

/* Form Styles */
.alert-form .form-group {
    margin-bottom: 1.25rem;
}

.alert-form label {
    display: block;
    font-weight: 500;
    color: #475569;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.alert-form input,
.alert-form textarea,
.alert-form select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: white;
}

.alert-form input:focus,
.alert-form textarea:focus,
.alert-form select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.alert-form textarea {
    min-height: 100px;
    resize: vertical;
}

.file-upload {
    position: relative;
}

.file-upload input[type="file"] {
    padding: 0.5rem;
    border: 2px dashed #cbd5e1;
    background: #f8fafc;
    cursor: pointer;
}

.file-upload input[type="file"]:hover {
    border-color: #667eea;
    background: #f1f5f9;
}

.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    flex: 1;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    flex: 1;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(102, 126, 234, 0.2);
}

/* Alerts List Card */
.alerts-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #eef2f7;
}

.card-header-list {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header-list h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.badge-count {
    background: white;
    color: #667eea;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Filter Section */
.filter-section {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #eef2f7;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-size: 0.9rem;
    font-weight: 500;
    color: #475569;
}

.filter-select {
    padding: 0.5rem 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

.filter-actions .btn {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
    padding: 0;
}

.alerts-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.alerts-table thead {
    background: #f1f5f9;
}

.alerts-table th {
    padding: 1rem 1.5rem;
    text-align: left;
    font-weight: 600;
    color: #475569;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
}

.alerts-table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #eef2f7;
    vertical-align: middle;
}

.alerts-table tbody tr {
    transition: background-color 0.2s ease;
}

.alerts-table tbody tr:hover {
    background-color: #f8fafc;
}

.alerts-table tbody tr:last-child td {
    border-bottom: none;
}

/* Alert Type Badges */
.type-badge {
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.type-critical {
    background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
    color: #991b1b;
    border: 1px solid #f87171;
}

.type-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #fbbf24;
}

.type-informational {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #60a5fa;
}

.type-notice {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #34d399;
}

.type-crime-alert {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    color: #5b21b6;
    border: 1px solid #8b5cf6;
}

.type-helpline {
    background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
    color: #9d174d;
    border: 1px solid #ec4899;
}

/* Status Badges */
.status-badge {
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.status-active {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #10b981;
}

.status-expired {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #6b7280;
    border: 1px solid #d1d5db;
}

/* Media Preview */
.media-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.media-btn {
    background: #e0e7ff;
    color: #3730a3;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s;
}

.media-btn:hover {
    background: #c7d2fe;
    transform: translateY(-2px);
}

/* Actions */
.actions-cell {
    min-width: 120px;
}

.actions-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #64748b;
    border: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.action-btn.edit:hover {
    background: #3b82f6;
    color: white;
}

.action-btn.delete:hover {
    background: #ef4444;
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #64748b;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h4 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: #475569;
}

.empty-state p {
    margin: 0;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px 16px 0 0;
    padding: 1rem 1.5rem;
    border: none;
}

.modal-title {
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.modal-body {
    padding: 1.5rem;
    max-height: 60vh;
    overflow-y: auto;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
}

.media-item {
    border-radius: 8px;
    overflow: hidden;
    background: #f8fafc;
    transition: transform 0.3s;
}

.media-item:hover {
    transform: translateY(-4px);
}

.media-content {
    padding: 0.5rem;
}

.media-content img,
.media-content video {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 6px;
}

.media-footer {
    padding: 0.5rem;
    background: #f1f5f9;
    text-align: center;
}

.media-footer small {
    color: #64748b;
    font-size: 0.8rem;
}

/* Edit Form Styles */
.edit-form .form-group {
    margin-bottom: 1rem;
}

.edit-form label {
    font-weight: 500;
    color: #475569;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
}

.edit-form input,
.edit-form textarea,
.edit-form select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
}

.edit-form input:focus,
.edit-form textarea:focus,
.edit-form select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.existing-media {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.existing-media a {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #f1f5f9;
    color: #475569;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.3s;
}

.existing-media a:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .alerts-container {
        padding: 1rem;
    }
    
    .alerts-header {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
        padding: 1.25rem;
    }
    
    .header-stats {
        width: 100%;
        justify-content: center;
    }
    
    .filter-form {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .actions-buttons {
        justify-content: center;
    }
    
    .media-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .stat-item {
        min-width: auto;
        flex: 1;
    }
    
    .header-title {
        font-size: 1.5rem;
    }
    
    .alert-form input,
    .alert-form textarea,
    .alert-form select {
        font-size: 16px; /* Prevent zoom on mobile */
    }
}
</style>
@endpush

@section('title', 'Manage Public Alerts')

@section('content')
<div class="alerts-container mt-5">
    <!-- Header -->
    <div class="alerts-header mt-5">
        <div class="header-content">
            <h1 class="header-title">
                <i class="fas fa-bullhorn"></i>
                Public Alert Management
            </h1>
            <p class="header-subtitle">Create, edit, and publish alerts to keep the public informed</p>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <i class="fas fa-bell"></i>
                <div>
                    <h3>{{ count($alerts) }}</h3>
                    <span>Total Alerts</span>
                </div>
            </div>
            <div class="stat-item">
                <i class="fas fa-clock"></i>
                <div>
                    <h3>{{ $alerts->where('visible_until', '>', now())->count() }}</h3>
                    <span>Active Alerts</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="alert-section">
        @if ($errors->any())
        <div class="alert-modern alert-danger">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="alert-content">
                <h6>Error</h6>
                <p>{{ $errors->first() }}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
        
        @if (session('success'))
        <div class="alert-modern alert-success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-content">
                <h6>Success</h6>
                <p>{{ session('success') }}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
    </div>

    <!-- Main Layout -->
    <div class="alerts-layout">
        <!-- Create Alert Card -->
        <div class="create-card">
            <div class="card-header-create">
                <i class="fas fa-plus-circle"></i>
                <h3>Create New Alert</h3>
            </div>
            
            <form method="POST" action="{{ route('admin.public.alerts.store') }}" enctype="multipart/form-data" class="alert-form">
                @csrf

                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter alert title" required>
                </div>

                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Enter alert message..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="">Select Alert Type</option>
                        <option value="notice">Notice</option>
                        <option value="crime_alert">Crime Alert</option>
                        <option value="helpline">Helpline</option>
                        <option value="Informational">Informational</option>
                        <option value="Warning">Warning</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>

                <div class="form-group file-upload">
                    <label>Upload Media (Optional)</label>
                    <input type="file" name="media[]" class="form-control" multiple>
                    <small class="text-muted">Supported: Images, Videos, PDFs (Multiple files allowed)</small>
                </div>

                <div class="form-group">
                    <label>Visible Until *</label>
                    <input type="datetime-local" name="visible_until" class="form-control" required>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Publish Alert
                    </button>
                </div>
            </form>
        </div>

        <!-- Alerts List Card -->
        <div class="alerts-card">
            <div class="card-header-list">
                <h3>
                    <i class="fas fa-list-alt"></i>
                    Public Alerts
                </h3>
                <span class="badge-count">{{ count($alerts) }} Total</span>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('admin.public.alerts') }}" class="filter-form">
                    <div class="filter-group">
                        <label>Type</label>
                        <select name="type" class="filter-select">
                            <option value="">All Types</option>
                            <option value="Critical" {{ request('type') == 'Critical' ? 'selected' : '' }}>Critical</option>
                            <option value="Warning" {{ request('type') == 'Warning' ? 'selected' : '' }}>Warning</option>
                            <option value="Informational" {{ request('type') == 'Informational' ? 'selected' : '' }}>Informational</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    
                    <div class="filter-actions">
                        <a href="{{ route('admin.public.alerts') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="alerts-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Visible Until</th>
                            <th>Media</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alerts as $alert)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $alert->title }}</div>
                                <small class="text-muted">{{ Str::limit($alert->message, 50) }}</small>
                            </td>
                            <td>
                                @switch($alert->type)
                                    @case('Critical')
                                        <span class="type-badge type-critical">
                                            <i class="fas fa-exclamation-circle"></i> Critical
                                        </span>
                                        @break
                                    @case('Warning')
                                        <span class="type-badge type-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Warning
                                        </span>
                                        @break
                                    @case('Informational')
                                        <span class="type-badge type-informational">
                                            <i class="fas fa-info-circle"></i> Informational
                                        </span>
                                        @break
                                    @case('notice')
                                        <span class="type-badge type-notice">
                                            <i class="fas fa-bell"></i> Notice
                                        </span>
                                        @break
                                    @case('crime_alert')
                                        <span class="type-badge type-crime-alert">
                                            <i class="fas fa-shield-alt"></i> Crime Alert
                                        </span>
                                        @break
                                    @case('helpline')
                                        <span class="type-badge type-helpline">
                                            <i class="fas fa-phone-alt"></i> Helpline
                                        </span>
                                        @break
                                    @default
                                        <span class="type-badge type-informational">
                                            <i class="fas fa-info-circle"></i> {{ $alert->type }}
                                        </span>
                                @endswitch
                            </td>
                            <td>
                                @if(now()->lt($alert->visible_until))
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @else
                                    <span class="status-badge status-expired">
                                        <i class="fas fa-clock"></i> Expired
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm">
                                    {{ \Carbon\Carbon::parse($alert->visible_until)->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($alert->visible_until)->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                @if($alert->media)
                                    @php 
                                        $mediaFiles = json_decode($alert->media, true);
                                        $mediaCount = is_array($mediaFiles) ? count($mediaFiles) : 0;
                                    @endphp
                                    @if($mediaCount > 0)
                                        <button class="media-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#mediaModal{{ $alert->id }}">
                                            <i class="fas fa-images"></i>
                                            {{ $mediaCount }} file{{ $mediaCount > 1 ? 's' : '' }}
                                        </button>
                                    @else
                                        <span class="text-muted small">No media</span>
                                    @endif
                                @else
                                    <span class="text-muted small">No media</span>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <div class="actions-buttons">
                                    <button class="action-btn edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editAlertModal{{ $alert->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.public.alerts.delete', $alert->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this alert?')"
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-bell-slash"></i>
                                    <h4>No alerts available</h4>
                                    <p>Create your first alert using the form on the left</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals for each alert -->
@foreach($alerts as $alert)
<!-- Media Modal -->
<div class="modal fade" id="mediaModal{{ $alert->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images"></i> Media Files - {{ $alert->title }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($alert->media)
                    @php $mediaFiles = json_decode($alert->media, true); @endphp
                    <div class="media-grid">
                        @foreach($mediaFiles as $file)
                            <div class="media-item">
                                <div class="media-content">
                                    @if(Str::endsWith($file, ['jpg','jpeg','png','gif']))
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $file) }}" 
                                                 alt="Media"
                                                 class="img-fluid">
                                        </a>
                                    @elseif(Str::endsWith($file, ['mp4','mov','avi']))
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                            <video class="img-fluid" controls muted>
                                                <source src="{{ asset('storage/' . $file) }}">
                                            </video>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $file) }}" 
                                           class="btn btn-outline-secondary w-100" 
                                           target="_blank">
                                            <i class="fas fa-paperclip"></i> View File
                                        </a>
                                    @endif
                                </div>
                                <div class="media-footer">
                                    <small>{{ basename($file) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAlertModal{{ $alert->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Alert - {{ $alert->title }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form method="POST" action="{{ route('admin.public.alerts.update', $alert->id) }}" enctype="multipart/form-data" class="edit-form">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Title *</label>
                            <input type="text" name="title" value="{{ $alert->title }}" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label>Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="notice" {{ $alert->type == 'notice' ? 'selected' : '' }}>Notice</option>
                                <option value="crime_alert" {{ $alert->type == 'crime_alert' ? 'selected' : '' }}>Crime Alert</option>
                                <option value="helpline" {{ $alert->type == 'helpline' ? 'selected' : '' }}>Helpline</option>
                                <option value="Informational" {{ $alert->type == 'Informational' ? 'selected' : '' }}>Informational</option>
                                <option value="Warning" {{ $alert->type == 'Warning' ? 'selected' : '' }}>Warning</option>
                                <option value="Critical" {{ $alert->type == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label>Message *</label>
                            <textarea name="message" class="form-control" rows="3" required>{{ $alert->message }}</textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label>Upload New Media</label>
                            <input type="file" name="media[]" class="form-control" multiple>
                            <small class="text-muted">Select multiple files if needed</small>
                        </div>
                        
                        @if($alert->media)
                            @php $mediaFiles = json_decode($alert->media, true); @endphp
                            <div class="col-md-6">
                                <label>Existing Media</label>
                                <div class="existing-media">
                                    @foreach($mediaFiles as $file)
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                            <i class="fas fa-file"></i> {{ basename($file) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="col-md-6">
                            <label>Visible Until *</label>
                            <input type="datetime-local" name="visible_until" 
                                   value="{{ \Carbon\Carbon::parse($alert->visible_until)->format('Y-m-d\TH:i') }}" 
                                   class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-modern');
    alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 300);
    });
}, 5000);

// File upload preview (optional enhancement)
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const files = e.target.files;
        const preview = this.nextElementSibling;
        
        if (files.length > 0) {
            if (!preview || !preview.classList.contains('file-preview')) {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'file-preview mt-2';
                this.parentNode.insertBefore(previewDiv, this.nextSibling);
            }
            
            const previewDiv = this.nextElementSibling;
            previewDiv.innerHTML = `<small class="text-success"><i class="fas fa-check-circle"></i> ${files.length} file${files.length > 1 ? 's' : ''} selected</small>`;
        }
    });
});
</script>
@endsection