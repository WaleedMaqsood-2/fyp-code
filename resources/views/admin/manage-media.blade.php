  @extends('layouts.master')

  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin/manage-media.css') }}">
  @endpush
  

  @section('content')
  <div class="container">
      <div class="ms-2 mt-4">
          @if ($errors->any())
          <div class="alert alert-danger">{{ $errors->first() }}</div>
          @endif
          @if (session('success'))
          <div class="alert alert-success mt-2">{{ session('success') }}</div>
          @endif

          <div class="container container-main">
              <!-- Header -->
              <header class="d-flex justify-content-between align-items-center mb-4">
                  <h1 class="h3 fw-bold">📂 Media Management</h1>
              </header>

              <div class="row g-4">
                  <!-- Table -->
                  <div class="col-lg-8">
                      <div class="mb-3 position-relative">
                        
                          <input type="text" class="form-control search-input"
                              placeholder="Search files, keywords, or case ID...">
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
      @include('admin.partials.media-search-result', ['file' => $file])
  @endforeach
</tbody>

</table>
</div>
</div>
<div class="d-flex justify-content-center mt-3">
    {{ $media->links('pagination::bootstrap-5') }}
</div>

</div>



                  
                  <!-- Preview Panel -->
                  <div class="col-lg-4">
                      <div class="preview-panel">
                          <h2 class="h5 fw-bold mb-3">Preview</h2>
                          <div class="preview-media mb-3"
                              style="height:260px; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.2); border-radius:8px; color:#aaa;">
                              <span>No media selected</span>
                          </div>
                          <h5 class="fw-semibold preview-title">---</h5>
                          <p class="meta-small mb-3 preview-case">---</p>
                          <div class="row g-2 small text-muted">
                              <div class="col-6"><span class="meta-small">Type:</span> <strong class="preview-type">---</strong></div>
                              <div class="col-6"><span class="meta-small">Date:</span> <strong class="preview-date">---</strong></div>
                              <div class="col-6"><span class="meta-small">Size:</span> <strong class="preview-size">---</strong></div>
                              <div class="col-6"><span class="meta-small">Status:</span> <span class="preview-status badge bg-secondary">---</span></div>
                          </div>
                          <div class="mt-4 d-flex gap-2">
                              <button class="icon-btn w-50 download-btn" disabled>
                                  <span class="material-symbols-outlined">download</span> Download
                              </button>
                              <button class="icon-btn secondary w-50 share-btn" disabled>
                                  <span class="material-symbols-outlined">share</span> Share
                              </button>
                          </div>
                      </div>
                  </div>
              </div>

             

              
          </div>
      </div>
  </div>



  <!-- Change Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header">
        <h5 class="modal-title">Change Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="statusForm" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <p>Select new status:</p>
          <select name="status" id="statusSelect" class="form-select">
            <option value="pending">Pending</option>
          
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>





<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>Are you sure you want to delete this file?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>





{{-- share model --}}
  <div id="shareModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
      background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; padding:20px; border-radius:8px; width:300px; text-align:center;">
      <h5>Share File</h5>
      <div class="d-flex flex-column gap-2 mt-3">
        <a id="shareWhatsApp" target="_blank" class="btn btn-success">📱 WhatsApp</a>
        <a id="shareTelegram" target="_blank" class="btn btn-info text-white">📨 Telegram</a>
        <a id="shareEmail" target="_blank" class="btn btn-warning">✉️ Email</a>
        <a id="shareFacebook" target="_blank" class="btn btn-primary">📘 Facebook</a>
      </div>
  <button class="btn btn-secondary mt-3" onclick="document.getElementById('shareModal').style.display='none'">Close</button>

    </div>
  </div>




  <!-- Download Modal -->
<div id="downloadModal" class="modal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
     background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
  <div class="modal-content" style="background:#fff; padding:20px; border-radius:8px; width:320px; text-align:center;">
    <h5>Select Download Format</h5>
    <div class="d-flex flex-column gap-2 mt-3">
      <a id="downloadPdf" class="btn btn-danger">📄 Download as PDF</a>
      <a id="downloadWord" class="btn btn-primary">📝 Download as Word</a>
      <a id="downloadPpt" class="btn btn-warning">📊 Download as PPT</a>
      <a id="downloadOriginal" class="btn btn-success">📂 Download</a>
    </div>
    <button class="btn btn-secondary mt-3" onclick="document.getElementById('downloadModal').style.display='none'">Close</button>
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
  // 📂 media.js

// ✅ Format file size into readable units
function formatBytes(bytes) {
    if (!bytes || bytes === 0) return "0 B";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB", "TB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector(".search-input");
    const tableBody = document.querySelector("table tbody");
    const previewPanel = document.querySelector(".preview-panel");

    let timer = null;

    // ===============================
    // 🔍 1. Search with debounce
    // ===============================
    searchInput.addEventListener("keyup", function () {
        clearTimeout(timer);
        let query = this.value.trim();

        timer = setTimeout(() => {
            fetch(`/media/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = data.html; // Blade-rendered rows aa jayein

                    // re-bind buttons because DOM replace hua
                    bindRowActions();
                })
                .catch(err => console.error(err));
        }, 400);
    });

    // ===============================
    // 🔹 2. Preview Panel Handling
    // ===============================
    function bindPreview(btn) {
        btn.addEventListener("click", function () {
            let { size, path, type, desc, date, status, complaint } = this.dataset;

            // 🔸 Status Badge
            let statusBadge = previewPanel.querySelector(".preview-status");
            statusBadge.innerText = status;
            statusBadge.className = "preview-status badge"; // reset
            if (status === "approved") {
                statusBadge.classList.add("bg-success");
            } else if (status === "pending") {
                statusBadge.classList.add("badge-pending");
            } else if (status === "rejected") {
                statusBadge.classList.add("bg-danger");
            } else {
                statusBadge.classList.add("bg-secondary");
            }

            // 🔸 Media Preview
            let previewMedia = "";
            if (type === "video") {
                previewMedia = `<video controls style="width:100%; height:100%; object-fit:contain;">
                                  <source src="${path}" type="video/mp4">
                                </video>`;
            } else if (type === "image") {
                previewMedia = `<img src="${path}" style="width:100%; height:100%; object-fit:contain; border-radius:8px;">`;
            } else if (path.toLowerCase().endsWith(".pdf")) {
                previewMedia = `<embed src="${path}" type="application/pdf" style="width:100%; height:100%; border:none;" />`;
            } else if (path.toLowerCase().endsWith(".txt")) {
                previewMedia = `<iframe src="${path}" style="width:100%; height:100%; border:none;"></iframe>`;
            } else {
                previewMedia = `<div style="color:#aaa; text-align:center; padding:20px;">
                                  <span class="material-symbols-outlined" style="font-size:40px;">description</span>
                                  <p>Preview not supported.<br>
                                  <a href="${path}" target="_blank">Download</a> to view.</p>
                                </div>`;
            }

            // Update Preview Panel
            previewPanel.querySelector(".preview-media").innerHTML = previewMedia;
            previewPanel.querySelector(".preview-title").innerText = desc || "---";
            previewPanel.querySelector(".preview-case").innerText = complaint || "N/A";
            previewPanel.querySelector(".preview-type").innerText = type || "---";
            previewPanel.querySelector(".preview-date").innerText = date || "---";
            previewPanel.querySelector(".preview-size").innerText = formatBytes(size);

            // Enable buttons
            previewPanel.querySelector(".download-btn").disabled = false;
            previewPanel.querySelector(".share-btn").disabled = false;

            // ✅ Download Modal
            previewPanel.querySelector(".download-btn").onclick = () => {
                const modal = document.getElementById("downloadModal");
                modal.style.display = "flex";

                const modalContent = modal.querySelector(".d-flex");
                modalContent.innerHTML = "";

                // Original download
                let downloadBtn = document.createElement("a");
                downloadBtn.href = path;
                downloadBtn.setAttribute("download", desc);
                downloadBtn.className = "btn btn-success";
                downloadBtn.innerText = "📂 Download";
                modalContent.appendChild(downloadBtn);

                // Preview in new tab if supported
                if (["image", "video"].includes(type) || path.toLowerCase().endsWith(".pdf")) {
                    let previewBtn = document.createElement("a");
                    previewBtn.href = path;
                    previewBtn.target = "_blank";
                    previewBtn.className = "btn btn-primary";
                    previewBtn.innerText = "👁 Preview in New Tab";
                    modalContent.appendChild(previewBtn);
                }
            };

            // ✅ Share Modal
            previewPanel.querySelector(".share-btn").onclick = () => {
                if (navigator.share) {
                    navigator.share({
                        title: desc,
                        text: "Check out this media file",
                        url: path
                    }).catch(err => console.error("Share error:", err));
                } else {
                    let shareUrl = encodeURIComponent(path);
                    document.getElementById("shareWhatsApp").href = "https://wa.me/?text=" + shareUrl;
                    document.getElementById("shareTelegram").href = "https://t.me/share/url?url=" + shareUrl;
                    document.getElementById("shareEmail").href = "mailto:?subject=Check this file&body=" + shareUrl;
                    document.getElementById("shareFacebook").href = "https://www.facebook.com/sharer/sharer.php?u=" + shareUrl;
                    document.getElementById("shareModal").style.display = "flex";
                }
            };
        });
    }

    // ===============================
    // 🔹 3. Status Modal
    // ===============================
    function bindStatus(btn) {
        btn.addEventListener("click", function () {
            let fileId = this.dataset.id;
            let status = this.dataset.status;

            let form = document.getElementById("statusForm");
            form.action = `/media/${fileId}/status`;

            document.getElementById("statusSelect").value = status;
            new bootstrap.Modal(document.getElementById("statusModal")).show();
        });
    }

    // ===============================
    // 🔹 4. Delete Modal
    // ===============================
    function bindDelete(btn) {
        btn.addEventListener("click", function () {
            let fileId = this.dataset.id;
            let form = document.getElementById("deleteForm");
            form.action = `/media/${fileId}`;
            new bootstrap.Modal(document.getElementById("deleteModal")).show();
        });
    }

    // ===============================
    // 🔹 5. Bind all row actions
    // ===============================
    function bindRowActions() {
        document.querySelectorAll(".preview-btn").forEach(bindPreview);
        document.querySelectorAll(".open-status-modal").forEach(bindStatus);
        document.querySelectorAll(".open-delete-modal").forEach(bindDelete);
    }

    // Initial bind
    bindRowActions();
});

  </script>