<tr>
  <!-- File Name -->
  <td>
    <div class="d-flex align-items-center gap-0" style="font-size: 12px">
        @if($file->file_type == 'image')
        <span class="material-symbols-outlined text-muted">image</span>
        @elseif($file->file_type == 'video')
        <span class="material-symbols-outlined text-muted">videocam</span>
        @else
        <span class="material-symbols-outlined text-muted">description</span>
        @endif
        {{ \Illuminate\Support\Str::limit(basename($file->file_path), 5) }}
    </div>
  </td>

  <!-- File Type -->
  <td><span class="meta-small">{{ ucfirst($file->file_type) }}</span></td>

  <!-- Complaint Track ID -->
  <td><span class="meta-small">{{ $file->complaint->track_id ?? 'N/A' }}</span></td>

  <!-- Created Date -->
  <td><span class="meta-small">{{ $file->created_at->format('d M') }}</span></td>

  <!-- Status -->
  <td>
    @if($file->status == 'approved')
      <span class="badge-processed">Approved</span>
    @elseif($file->status == 'pending')
      <span class="badge-pending">Pending</span>
    @elseif ($file->status == 'rejected')
      <span class="badge bg-danger">Rejected</span>
    @else
      <span class="badge bg-secondary">{{ ucfirst($file->status) }}</span>
    @endif
  </td>

  <!-- Actions Dropdown -->
  <td>
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" 
              type="button" id="dropdownMenuButton{{ $file->id }}" 
              data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-gear"></i> Action
      </button>

      <ul class="dropdown-menu shadow border-0 rounded-3 p-2" aria-labelledby="dropdownMenuButton{{ $file->id }}">
          <!-- Preview -->
          <li>
              <button type="button" 
                  class="dropdown-item d-flex align-items-center gap-2 preview-btn" 
                  data-path="{{ Storage::url($file->file_path) }}"
                  data-type="{{ $file->file_type }}"
                  data-desc="{{ $file->description }}"
                  data-date="{{ $file->created_at->format('d M') }}"
                  data-status="{{ $file->status }}"
                  data-complaint="{{ $file->complaint->track_id }}"
                  data-size="{{ $file->size }}">
                  
                  <i class="bi bi-eye text-info"></i> Preview
              </button>
          </li>

          <!-- Change Status -->
          <li>
              <button type="button" 
                      class="dropdown-item text-warning open-status-modal" 
                      data-id="{{ $file->id }}" 
                      data-status="{{ $file->status }}">
                <i class="bi bi-arrow-repeat"></i> Change Status
              </button>
          </li>

          <!-- Delete -->
          <li>
              <button type="button" 
                      class="dropdown-item text-danger open-delete-modal" 
                      data-id="{{ $file->id }}">
                <i class="bi bi-trash"></i> Delete
              </button>
          </li>
      </ul>
    </div>
  </td>
</tr>
