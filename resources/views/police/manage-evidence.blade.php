@extends('police.layouts.main')
<style>
body {
    background-color: #f4f6f9;
}
.card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.evidence-thumb {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
}
.upload-box {
    border: 2px dashed #0d6efd;
    border-radius: 10px;
    background: #f9fbff;
    padding: 40px;
    text-align: center;
    transition: all 0.3s;
}
.upload-box:hover {
    background: #e9f2ff;
}
.material-icons {
    vertical-align: middle;
}
</style>
@section('title', 'Manage Evidence - Police Module')
@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold text-primary">Manage Digital Evidence</h2>

    <!-- Upload Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Upload Evidence</h5>
            <div class="upload-box mb-3">
                <span class="material-icons fs-1 text-primary">cloud_upload</span>
                <p class="mt-2 text-muted">Drag & drop files here or click to browse</p>
                <input type="file" multiple class="form-control mt-3" id="evidenceFiles">
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Case ID</label>
                    <input type="text" class="form-control" placeholder="Enter Case ID">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Evidence Type</label>
                    <select class="form-select">
                        <option>Photo</option>
                        <option>Video</option>
                        <option>Audio</option>
                        <option>Document</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <span class="material-icons">upload_file</span> Upload Files
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Evidence List -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h5 class="card-title mb-0">Uploaded Evidence</h5>
                <div>
                    <button class="btn btn-outline-secondary btn-sm">Filter</button>
                    <button class="btn btn-outline-success btn-sm">
                        <span class="material-icons">send</span> Forward to Forensic
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <!-- Evidence Item -->
                <div class="col-md-3">
                    <div class="card h-100">
                        <img src="{{ asset('assets/images/sample-evidence.jpg') }}" class="evidence-thumb" alt="Evidence">
                        <div class="card-body p-2">
                            <h6 class="fw-semibold mb-1">CCTV Footage</h6>
                            <small class="text-muted">Case ID: #P-102</small><br>
                            <small class="text-muted">Type: Video</small><br>
                            <small class="text-muted">Uploaded: 08 Oct 2025</small>
                        </div>
                        <div class="card-footer text-end bg-light">
                            <button class="btn btn-sm btn-outline-primary"><span class="material-icons fs-6">visibility</span></button>
                            <button class="btn btn-sm btn-outline-danger"><span class="material-icons fs-6">delete</span></button>
                        </div>
                    </div>
                </div>
                <!-- More Evidence Cards can be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection
