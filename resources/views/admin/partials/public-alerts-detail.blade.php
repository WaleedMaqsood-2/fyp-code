@extends('layouts.master')

@push('styles')
<link href="{{ asset('css/admin/public-alerts-detail.css') }}" rel="stylesheet">
@endpush
@section('title', 'Edit Public Alert')

@section('content')
<div class="container mt-4">
    <div class="card p-4">
        <h5 class="fw-bold mb-3">✏️ Edit Alert</h5>

        <form method="POST" action="{{ route('admin.public.alerts.update', $alert->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" value="{{ old('title', $alert->title) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="3" required>{{ old('message', $alert->message) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="notice" {{ $alert->type == 'notice' ? 'selected' : '' }}>Notice</option>
                    <option value="crime_alert" {{ $alert->type == 'crime_alert' ? 'selected' : '' }}>Crime Alert</option>
                    <option value="helpline" {{ $alert->type == 'helpline' ? 'selected' : '' }}>Helpline</option>
                    <option value="Informational" {{ $alert->type == 'Informational' ? 'selected' : '' }}>Informational</option>
                    <option value="Warning" {{ $alert->type == 'Warning' ? 'selected' : '' }}>Warning</option>
                    <option value="Critical" {{ $alert->type == 'Critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload New Media (optional)</label>
                <input type="file" name="media[]" class="form-control" multiple>
            </div>

            @if($alert->media)
                @php $mediaFiles = json_decode($alert->media, true); @endphp
                <div class="mb-3">
                    <label class="form-label">Existing Media</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($mediaFiles as $file)
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-file-earmark"></i> {{ basename($file) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Visible Until</label>
                <input type="datetime-local" name="visible_until" 
                       value="{{ old('visible_until', \Carbon\Carbon::parse($alert->visible_until)->format('Y-m-d\TH:i')) }}" 
                       class="form-control" required>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.public.alerts.index') }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Update Alert</button>
            </div>
        </form>
    </div>
</div>
@endsection
