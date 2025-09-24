@extends('layouts.master')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin/manage-media.css') }}">

@endpush
<style>

</style>

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
      <div class="container container-main">

  <!-- Header -->
  <header class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold text-white">📂 Media Management</h1>
    <div>
      <button class="btn btn-outline-light btn-sm me-2"><i class="fa fa-filter"></i> Filters</button>
      <button class="btn btn-outline-light btn-sm"><i class="fa fa-plus"></i> Upload</button>
    </div>
  </header>

  <div class="row g-4">
    <!-- Table -->
    <div class="col-lg-8 bg-dark">
      <div class="mb-3 position-relative">
        <span class="material-symbols-outlined search-icon">search</span>
        <input type="text" class="form-control search-input" placeholder="Search files, keywords, or case ID...">
      </div>

      <div class="panel p-2">
        <div class="table-responsive">
          <table class="table table-borderless mb-0 table-dark-custom align-middle">
            <thead>
              <tr>
                <th>File Name</th>
                <th>Type</th>
                <th>Case</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
           <tbody>
  @foreach($media as $file)
    <tr class="bg-dark">
      <td>
        <div class="d-flex align-items-center gap-3 ">
          @if($file->file_type == 'image')
            <span class="material-symbols-outlined text-muted">image</span>
          @elseif($file->file_type == 'video')
            <span class="material-symbols-outlined text-muted">videocam</span>
          @else
            <span class="material-symbols-outlined text-muted">description</span>
          @endif
          {{ $file->description }}
        </div>
      </td>
      <td><span class="meta-small">{{ ucfirst($file->file_type) }}</span></td>
      <td><span class="meta-small">{{ $file->complaint_id ?? 'N/A' }}</span></td>
      <td><span class="meta-small">{{ $file->uploaded_at }}</span></td>
      <td>
        @if($file->status == 'processed')
          <span class="badge-processed">Processed</span>
        @elseif($file->status == 'pending')
          <span class="badge-pending">Pending</span>
        @else
          <span class="badge bg-secondary">{{ ucfirst($file->status) }}</span>
        @endif
      </td>
      <td>
        <button class="btn btn-link text-primary fw-semibold p-0 preview-btn" 
                data-path="{{ asset($file->file_path) }}" 
                data-type="{{ $file->file_type }}" 
                data-desc="{{ $file->description }}" 
                data-date="{{ $file->uploaded_at }}" 
                data-status="{{ $file->status }}" 
                data-complaint="{{ $file->complaint_id }}">
          Preview
        </button>
      </td>
    </tr>
  @endforeach
</tbody>

          </table>
        </div>
      </div>
    </div>


    <!-- Preview Panel -->
    <div class="col-lg-4">
      <div class="preview-panel">
        <h2 class="h5 fw-bold mb-3">Preview</h2>
        <div class="preview-media mb-3" style="height:260px; display:flex; align-items:center; justify-content:center;">
          <video controls style="width:100%; height:100%; object-fit:contain;">
            <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
          </video>
        </div>
        <h5 class="fw-semibold">Video of suspect</h5>
        <p class="meta-small mb-3">CASE-00124</p>
        <div class="row g-2 small">
          <div class="col-6"><span class="meta-small">Type:</span> <strong>Video</strong></div>
          <div class="col-6"><span class="meta-small">Date:</span> <strong>2024-02-20</strong></div>
          <div class="col-6"><span class="meta-small">Size:</span> <strong>15.7 MB</strong></div>
          <div class="col-6"><span class="meta-small">Status:</span> <span class="badge-pending">Pending</span></div>
        </div>
        <div class="mt-4 d-flex gap-2">
          <button class="icon-btn w-50"><span class="material-symbols-outlined">download</span> Download</button>
          <button class="icon-btn secondary w-50"><span class="material-symbols-outlined">share</span> Share</button>
        </div>
      </div>
    </div>
  </div>
    <div class="mt-3">
  {{ $media->links('pagination::bootstrap-5') }}
</div>
</div>
    </div>
</div>
@endsection

@php
  $searchConfig = [
    'endpoint' => route('media.search'),
    'suggestionKey' => 'media',
    'resultKey' => 'media',
  ];
@endphp

<script>
  window.searchConfig = @json($searchConfig);
</script>

@php
  $searchAction = route('media.search');
  $searchPlaceholder = 'Search Media...';
@endphp


<script>
document.addEventListener("DOMContentLoaded", function () {
  const previewBtns = document.querySelectorAll(".preview-btn");
  const previewPanel = document.querySelector(".preview-panel");

  previewBtns.forEach(btn => {
    btn.addEventListener("click", function () {
      let path = this.dataset.path;
      let type = this.dataset.type;
      let desc = this.dataset.desc;
      let date = this.dataset.date;
      let status = this.dataset.status;
      let complaint = this.dataset.complaint;

      let previewMedia = "";
      if (type === "video") {
        previewMedia = `<video controls style="width:100%; height:100%; object-fit:contain;">
                          <source src="${path}" type="video/mp4">
                        </video>`;
      } else if (type === "image") {
        previewMedia = `<img src="${path}" style="width:100%; height:100%; object-fit:contain;">`;
      } else {
        previewMedia = `<iframe src="${path}" style="width:100%; height:100%;"></iframe>`;
      }

      previewPanel.querySelector(".preview-media").innerHTML = previewMedia;
      previewPanel.querySelector("h5").innerText = desc;
      previewPanel.querySelector("p").innerText = complaint;
      previewPanel.querySelector(".col-6:nth-child(2) strong").innerText = date;
      previewPanel.querySelector(".col-6:nth-child(4) span").innerText = status;
    });
  });
});
</script>
