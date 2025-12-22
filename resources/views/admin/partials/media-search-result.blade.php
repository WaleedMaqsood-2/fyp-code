<tr>
  <!-- File Name -->
 
  <td>
      @php
                                        $extension = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                        $fileType = 'document';
                                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                            $fileType = 'image';
                                        } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                            $fileType = 'video';
                                        } elseif (in_array($extension, ['mp3', 'wav', 'aac'])) {
                                            $fileType = 'audio';
                                        } elseif ($extension === 'pdf') {
                                            $fileType = 'pdf';
                                        }
                                    @endphp
    <div class="file-info">
                                    
                                    <div class="file-type {{ $fileType }}">
                                        @if($fileType === 'image')
                                        <i class="fas fa-image"></i>
                                        @elseif($fileType === 'video')
                                        <i class="fas fa-video"></i>
                                        @elseif($fileType === 'audio')
                                        <i class="fas fa-music"></i>
                                        @elseif($fileType === 'pdf')
                                        <i class="fas fa-file-pdf"></i>
                                        @else
                                        <i class="fas fa-file-alt"></i>
                                        @endif
                                    </div>
                                    <div></div>
                                     <span class="file-name"> {{ \Illuminate\Support\Str::limit(basename($file->file_path), 5) }}
  </span>
                                        
   
  </td>

  <!-- File Type -->
   <td>
                                <span class="badge bg-light text-dark">
                                    {{ strtoupper($extension) }}
                                </span>
                            </td>
  
  <!-- Complaint Track ID -->
   <td>
                                @if($file->complaint)
                                <span class="badge bg-info ">
                                    {{ $file->complaint->track_id ?? 'N/A' }}
                                </span>
                                @else
                                <span class="text-muted">No case</span>
                                @endif
                            </td>
  
  <!-- Created Date -->
    <td>
                                <span class="text-muted meta-small">
                                    {{ $file->created_at->format('d M') }}
                                </span>
                            </td>
 
  <!-- Status -->
  <td>
    @if($file->status == 'approved')
      
      <span class="badge-processed d-flex"><i class="fas fa-check-circle me-1"></i>Approved</span>
    @elseif($file->status == 'pending')
    
      <span class="badge-pending d-flex"><i class="fas fa-clock me-1"></i>Pending</span>
    @elseif ($file->status == 'rejected')
    
  
      <span class="badge bg-danger d-flex"><i class="fas fa-times-circle me-1"></i>Rejected</span>
    @else
      <span class="badge bg-secondary">{{ ucfirst($file->status) }}</span>
    @endif
  </td>

                     <!-- Actions Dropdown -->
  <td>
    <div class="dropdown ">
      <button class="btn btn-sm btn-outline-secondary  preview preview-btn dropdown-toggle d-flex align-items-center gap-1" 
              type="button" id="dropdownMenuButton{{ $file->id }}" 
              data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-gear"></i> Action
      </button>

      <ul class="dropdown-menu shadow border-0 rounded-3 p-2 " aria-labelledby="dropdownMenuButton{{ $file->id }}">
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


 
