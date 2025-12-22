@extends('police.layouts.main')
<link rel="stylesheet" href="{{ asset('css/police/dashboard.css') }}">
@section('title', 'Police Dashboard')

@php
    if (!auth()->check()) {
        header('Location: ' . route('login'));
        exit;
    }
@endphp

@section('content')
<div class="police-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="greeting">
                <h1 class="page-title">
                    <i class="fas fa-shield-alt"></i>
                    Assigned Cases
                </h1>
                <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}! Here's an overview of your active cases.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('police.cases') }}" class="btn btn-view-all">
                    <i class="fas fa-list"></i> View All Cases
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $cases->total() }}</h3>
                    <p>Total Cases</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $cases->where('status', 'under_review')->count() }}</h3>
                    <p>Under Review</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $cases->where('status', 'resolved')->count() }}</h3>
                    <p>Resolved</p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $cases->where('severity', 'High')->count() }}</h3>
                    <p>High Priority</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cases Table -->
    <div class="cases-section">
        <div class="section-header">
            <h3><i class="fas fa-clipboard-list"></i> Recent Assigned Cases</h3>
            <div class="section-filters">
                <span class="filter-badge">{{ $cases->count() }} cases</span>
            </div>
        </div>
        
        <div class="cases-card">
            <div class="table-container">
                <table class="cases-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tracking ID</th>
                            <th>Complainant</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Severity</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cases as $case)
                        <tr class="case-row" data-id="{{ $case->id }}">
                            <td class="case-id">{{ $loop->iteration }}</td>
                            <td class="tracking-id">
                                <span >{{ $case->track_id }}</span>
                            </td>
                            <td class="complainant">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($case->user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $case->user?->name ?? 'N/A' }}</strong>
                                        <small>{{ $case->user?->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="case-title">
                                <strong>{{ $case->subject }}</strong>
                                <small class="text-muted">{{ Str::limit($case->description, 30) }}</small>
                            </td>
                            <td class="case-type">
                                <span class="type-badge">{{ $case->incident_type }}</span>
                            </td>
                            <td class="status">
                                @switch($case->status)
                                    @case('under_review')
                                        <span class="status-badge status-review">
                                            <i class="fas fa-search"></i> Under Review
                                        </span>
                                        @break
                                    @case('received')
                                        <span class="status-badge status-received">
                                            <i class="fas fa-inbox"></i> Received
                                        </span>
                                        @break
                                    @case('resolved')
                                        <span class="status-badge status-resolved">
                                            <i class="fas fa-check-circle"></i> Resolved
                                        </span>
                                        @break
                                    @default
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock"></i> {{ ucfirst($case->status) }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="severity">
                                @switch($case->severity)
                                    @case('High')
                                        <span class="severity-badge severity-high">
                                            <i class="fas fa-exclamation-circle"></i> High
                                        </span>
                                        @break
                                    @case('Medium')
                                        <span class="severity-badge severity-medium">
                                            <i class="fas fa-exclamation-triangle"></i> Medium
                                        </span>
                                        @break
                                    @default
                                        <span class="severity-badge severity-low">
                                            <i class="fas fa-check-circle"></i> Low
                                        </span>
                                @endswitch
                            </td>
                            <td class="date">
                                <div class="date-badge">
                                    <i class="fas fa-calendar"></i>
                                    {{ $case->created_at->format('M y') }}
                                </div>
                            </td>
                            <td class="actions">
                                <div class="action-buttons">
                                    <button class="action-btn primary view-details" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewCaseModal{{ $case->id }}">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </button>
                                    <button class="action-btn secondary edit-case" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCaseModal{{ $case->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-3x"></i>
                                    <h4>No cases assigned yet</h4>
                                    <p>You'll see assigned cases here when they become available</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($cases->hasPages())
            <div class="pagination-section">
                <div class="pagination-info">
                    Showing {{ $cases->firstItem() }} to {{ $cases->lastItem() }} of {{ $cases->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $cases->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="row">
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="chart-header">
                        <h4><i class="fas fa-chart-bar"></i> Cases by Type</h4>
                        <div class="chart-legend">
                            <span class="legend-item"></span> Current Month
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="casesByTypeChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h4><i class="fas fa-chart-pie"></i> Case Status</h4>
                    </div>
                    <div class="chart-body">
                        <canvas id="caseStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($cases as $case)
<!-- View Case Modal -->
<div class="modal fade" id="viewCaseModal{{ $case->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt"></i> Case #{{ $case->id }} Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="case-details">
                    <!-- Basic Info -->
                    <div class="details-grid">
                        <div class="detail-item">
                            <label><i class="fas fa-hashtag"></i> Tracking ID</label>
                            <span class="detail-value">{{ $case->track_id }}</span>
                        </div>
                        <div class="detail-item">
                            <label><i class="fas fa-heading"></i> Title</label>
                            <span class="detail-value">{{ $case->subject }}</span>
                        </div>
                        <div class="detail-item">
                            <label><i class="fas fa-tag"></i> Status</label>
                            <span class="detail-value">
                                @if($case->status == 'under_review')
                                    <span class="badge bg-warning">Under Review</span>
                                @elseif($case->status == 'received')
                                    <span class="badge bg-success">Received</span>
                                @elseif($case->status == 'resolved')
                                    <span class="badge bg-secondary">Closed</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($case->status) }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <label><i class="fas fa-exclamation"></i> Severity</label>
                            <span class="detail-value">
                                @if($case->severity == 'High')
                                    <span class="badge bg-danger">High</span>
                                @elseif($case->severity == 'Medium')
                                    <span class="badge bg-warning">Medium</span>
                                @else
                                    <span class="badge bg-success">Low</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <label><i class="fas fa-calendar"></i> Date Reported</label>
                            <span class="detail-value">{{ $case->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($case->location)
                        <div class="detail-item">
                            <label><i class="fas fa-map-marker-alt"></i> Location</label>
                            <span class="detail-value">{{ $case->location }}</span>
                        </div>
                        @endif
                        @if($case->note)
                        <div class="detail-item">
                            <label><i class="fas fa-sticky-note"></i> Police Note</label>
                            <span class="detail-value">{{ $case->note }}</span>
                        </div>
                        @endif
                        @if($case->incident_type)
                        <div class="detail-item">
                            <label><i class="fas fa-bullhorn"></i> Incident Type</label>
                            <span class="detail-value">{{ $case->incident_type }}</span>
                        </div>
                        @endif
                        @if($case->description)
                        <div class="detail-item full-width">
                            <label><i class="fas fa-align-left"></i> Description</label>
                            <span class="detail-value">{{ $case->description }}</span>
                        </div>
                        @endif
                        @if($case->transcription)
                        <div class="detail-item full-width">
                            <label><i class="fas fa-microphone-alt"></i> Transcription</label>
                            <span class="detail-value">{{ $case->transcription }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Evidence Section -->
                    <div class="evidence-section">
                        <h6><i class="fas fa-folder-open"></i> Attached Evidence</h6>
                        <div class="media-grid">
                            @forelse($case->media as $media)
                            <div class="media-item">
                                <a href="{{ asset('storage/'.$media->file_path) }}" target="_blank" class="media-link">
                                    @php
                                        $ext = strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION));
                                    @endphp
                                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                        <img src="{{ asset('storage/'.$media->file_path) }}" 
                                             alt="Evidence" 
                                             class="media-preview">
                                    @elseif(in_array($ext, ['mp4','mov','avi','mkv']))
                                        <div class="media-icon">
                                            <i class="fas fa-video"></i>
                                            <span>Video</span>
                                        </div>
                                    @elseif(in_array($ext, ['mp3','wav','aac']))
                                        <div class="media-icon">
                                            <i class="fas fa-music"></i>
                                            <span>Audio</span>
                                        </div>
                                    @elseif($ext == 'pdf')
                                        <div class="media-icon">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>PDF</span>
                                        </div>
                                    @elseif(in_array($ext, ['doc','docx']))
                                        <div class="media-icon">
                                            <i class="fas fa-file-word"></i>
                                            <span>Document</span>
                                        </div>
                                    @else
                                        <div class="media-icon">
                                            <i class="fas fa-file"></i>
                                            <span>File</span>
                                        </div>
                                    @endif
                                    <small class="media-name">{{ basename($media->file_path) }}</small>
                                </a>
                            </div>
                            @empty
                            <div class="empty-media">
                                <i class="fas fa-folder-open"></i>
                                <p>No evidence attached</p>
                            </div>
                            @endforelse
                            
                            <!-- Audio File -->
                            @if(!empty($case->audio_file))
                            <div class="media-item">
                                <a href="{{ asset('storage/'.$case->audio_file) }}" target="_blank" class="media-link">
                                    <div class="media-icon">
                                        <i class="fas fa-microphone-alt"></i>
                                        <span>Audio</span>
                                    </div>
                                    <small class="media-name">{{ basename($case->audio_file) }}</small>
                                    <div class="audio-player">
                                        <audio controls>
                                            <source src="{{ asset('storage/'.$case->audio_file) }}" 
                                                    type="audio/{{ pathinfo($case->audio_file, PATHINFO_EXTENSION) }}">
                                        </audio>
                                    </div>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editCaseModal{{ $case->id }}"
                        data-bs-dismiss="modal">
                    <i class="fas fa-edit"></i> Edit Case
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Case Modal -->
<div class="modal fade" id="editCaseModal{{ $case->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Case #{{ $case->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('police.cases.update', $case->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="received" {{ $case->status == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="under_review" {{ $case->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="resolved" {{ $case->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Police Note / Update</label>
                        <textarea name="note" class="form-control" rows="4" 
                                  placeholder="Add case notes or updates...">{{ $case->note }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // ==== Dynamic Data from Controller ====
    const chartData = @json($chartData);

    // === BAR CHART (Cases by Type) ===
    const ctx1 = document.getElementById('casesByTypeChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: chartData.types.labels,
            datasets: [{
                label: 'Cases',
                data: chartData.types.data,
                backgroundColor: [
                    '#667eea', '#764ba2', '#f472b6', '#f59e0b', 
                    '#10b981', '#3b82f6', '#8b5cf6'
                ],
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    },
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // === DOUGHNUT CHART (Case Status) ===
    const ctx2 = document.getElementById('caseStatusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: chartData.status.labels,
            datasets: [{
                data: chartData.status.data,
                backgroundColor: [
                    '#f59e0b', // Under Review
                    '#10b981', // Received
                    '#94a3b8', // Resolved
                    '#ef4444', // Other
                    '#3b82f6'  // Pending
                ],
                hoverOffset: 15,
                borderWidth: 3,
                borderColor: 'white'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Row hover effects
    const caseRows = document.querySelectorAll('.case-row');
    caseRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // Auto-refresh stats every 30 seconds (optional)
    setInterval(() => {
        // You can implement live updates here if needed
        console.log('Auto-refreshing dashboard...');
    }, 30000);
});
</script>
@endsection