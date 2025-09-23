@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/manage-public-alerts.css') }}">

@endpush

@section('title', 'Manage Public Alerts')


@section('content')
<div class="container">
     <div class="ms-2 mt-4">
       @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif
    <!-- Header -->
    <div class="page-header d-block ">
        <h2 class="fw-bold">📢 Public Alert Management</h2>
        <p class="text-muted">Create, edit, and publish alerts to keep the public informed.</p>
    </div>

   

    <div class="row gx-0">

        <!-- Form Column -->
        <div class="col-lg-3">
            <div class="card p-4">
                <h5 class="mb-3 fw-bold">➕ Create New Alert</h5>
                <form method="POST" action="{{ route('admin.public.alerts.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="notice">Notice</option>
                            <option value="crime_alert">Crime Alert</option>
                            <option value="helpline">Helpline</option>
                            <option value="Informational">Informational</option>
                            <option value="Warning">Warning</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Media</label>
                        <input type="file" name="media[]" class="form-control" multiple>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Visible Until</label>
                        <input type="datetime-local" name="visible_until" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-light me-2">Cancel</button>
                        <button type="submit" class="btn btn-primary">Publish</button>
                    </div>
                </form>
            </div>
        </div>

   <!-- Alerts Table -->
<div class="col-lg-9">
    <!-- Filter Form -->


    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between py-2">
            <h6 class="mb-0 fw-bold"><i class="bi bi-bell-fill me-2"></i> Public Alerts</h6>
            <span class="badge bg-light text-dark">{{ count($alerts) }} Total</span>
        </div>
      
        <div class="card-body p-3">

            <div class="">
                  <div class="mb-3 mt-2">
    <form method="GET" action="{{ route('admin.public.alerts') }}" class="row g-2">
        <div class="col-md-4">
            <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                <option value="Critical" {{ request('type') == 'Critical' ? 'selected' : '' }}>Critical</option>
                <option value="Warning" {{ request('type') == 'Warning' ? 'selected' : '' }}>Warning</option>
                <option value="Informational" {{ request('type') == 'Informational' ? 'selected' : '' }}>Informational</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <a href="{{ route('admin.public.alerts') }}" class="btn btn-sm btn-secondary">
                Reset
            </a>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </form>
</div>
                <table class="table table-sm table-striped table-hover align-middle text-sm">
                    <thead class="table-light small">
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Visible Until</th>
                            <th>Media</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                    @forelse($alerts as $alert)
                        <tr>
                            <td class="fw-semibold">{{ $alert->title }}</td>
                            <td>
                                @switch($alert->type)
                                    @case('Critical')
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-octagon me-1"></i>{{ $alert->type }}</span>
                                        @break
                                    @case('Warning')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>{{ $alert->type }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-info text-dark"><i class="bi bi-info-circle me-1"></i>{{ $alert->type }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if(now()->lt($alert->visible_until))
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-clock-history me-1"></i>Expired</span>
                                @endif
                            </td>
                            <td  style="font-size: 11px">{{ \Carbon\Carbon::parse($alert->visible_until)->format('d M y') }}</td>
                            <td>
                                @if($alert->media)
                                    @php $mediaFiles = json_decode($alert->media, true); @endphp
                                    @if(is_array($mediaFiles) && count($mediaFiles) > 0)
                                        <button class="btn btn-sm btn-outline-primary btn-sm py-1 px-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#mediaModal{{ $alert->id }}">
                                            <i class="bi bi-images"></i>  ({{ count($mediaFiles) }})
                                        </button>
                                        <!-- Modal -->
<div class="modal fade" id="mediaModal{{ $alert->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title small">
                    <i class="bi bi-images me-2"></i> Alert Media - {{ $alert->title }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <div class="row g-3">
                    @foreach($mediaFiles as $file)
                        <div class="col-6 col-md-3">
                            <div class="card h-100 shadow-sm border-0 media-card">
                                <div class="media-wrapper">
                                  @if(Str::endsWith($file, ['jpg','jpeg','png','gif']))
    <a href="{{ asset('storage/' . $file) }}" target="_blank">
        <img src="{{ asset('storage/' . $file) }}" 
             class="img-fluid rounded clickable-media" 
             alt="Media">
    </a>
@elseif(Str::endsWith($file, ['mp4','mov','avi']))
    <a href="{{ asset('storage/' . $file) }}" target="_blank">
        <video class="rounded clickable-media" muted>
            <source src="{{ asset('storage/' . $file) }}">
        </video>
    </a>
@else
    <a href="{{ asset('storage/' . $file) }}" 
       class="btn btn-outline-secondary w-100 btn-sm mt-3" 
       target="_blank">
        <i class="bi bi-paperclip"></i> File
    </a>
@endif

                                </div>
                                <div class="card-footer text-center py-2">
                                    <small class="text-muted">Media</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

                                    @else
                                        <span class="text-muted small">No Media</span>
                                    @endif
                                @else
                                    <span class="text-muted small">No Media</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <!-- Edit Button -->
<button class="btn btn-sm btn-outline-secondary py-1 px-2" 
        data-bs-toggle="modal" 
        data-bs-target="#editAlertModal{{ $alert->id }}">
    <i class="bi bi-pencil"></i>
</button>
<!-- Edit Modal -->
<div class="modal fade" id="editAlertModal{{ $alert->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <!-- Header -->
            <div class="modal-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="modal-title m-0">
                    <i class="bi bi-pencil-square me-2"></i> Edit Alert - {{ $alert->title }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.public.alerts.update', $alert->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-md-6 col-12 ">
                            <label class="form-label fw-semibold d-flex text-align-left">Title</label>
                            <input type="text" name="title" value="{{ $alert->title }}" class="form-control" required>
                        </div>

                        <!-- Type -->
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold d-flex text-align-left">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="notice" {{ $alert->type == 'notice' ? 'selected' : '' }}>Notice</option>
                                <option value="crime_alert" {{ $alert->type == 'crime_alert' ? 'selected' : '' }}>Crime Alert</option>
                                <option value="helpline" {{ $alert->type == 'helpline' ? 'selected' : '' }}>Helpline</option>
                                <option value="Informational" {{ $alert->type == 'Informational' ? 'selected' : '' }}>Informational</option>
                                <option value="Warning" {{ $alert->type == 'Warning' ? 'selected' : '' }}>Warning</option>
                                <option value="Critical" {{ $alert->type == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div class="col-12">
                            <label class="form-label fw-semibold d-flex text-align-left">Message</label>
                            <textarea name="message" class="form-control" rows="3" required>{{ $alert->message }}</textarea>
                        </div>

                        <!-- Upload Media -->
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold d-flex text-align-left">Upload New Media</label>
                            <input type="file" name="media[]" class="form-control" multiple>
                            <small class="text-muted">You can select multiple files</small>
                        </div>

                        <!-- Existing Media -->
                        @if($alert->media)
                            @php $mediaFiles = json_decode($alert->media, true); @endphp
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold d-flex text-align-left">Existing Media</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($mediaFiles as $file)
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-file-earmark"></i> {{ basename($file) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Visible Until -->
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold d-flex text-align-left">Visible Until</label>
                            <input type="datetime-local" name="visible_until" 
                                   value="{{ \Carbon\Carbon::parse($alert->visible_until)->format('Y-m-d\TH:i') }}" 
                                   class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check2-circle me-1"></i> Update Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




                                <form action="{{ route('admin.public.alerts.delete', $alert->id) }}" method="POST" onsubmit="return confirm('Delete this alert?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-1 px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3 small">
                                <i class="bi bi-bell-slash fs-5 d-block mb-1"></i>
                                No alerts available
                            </td>
                        </tr>
                        
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



    </div>
</div>
</div>
@endsection


