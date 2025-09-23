<div class="row g-4">
@forelse($alerts as $alert)
    <div class="col-12 col-md-6 col-lg-4">
        <div class="custom-card d-flex flex-column" data-bs-toggle="modal" data-bs-target="#alertModal{{ $alert->id }}" style="cursor:pointer; height:350px;">
            
            <div class="position-relative" style="flex: 0 0 200px;">
                @php
                    $imageExtensions = ['jpg','jpeg','png','gif','webp'];
                    $imageFile = null;
                    if(!empty($alert->media) && is_array($alert->media)) {
                        foreach($alert->media as $file){
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if(in_array($ext,$imageExtensions)){
                                $imageFile = $file;
                                break;
                            }
                        }
                    }
                @endphp

                @if($imageFile)
                    <div style="background:url('{{ asset('storage/'.$imageFile) }}') center/cover no-repeat; width:100%; height:200px; border-radius:10px;"></div>
                @else
                    <div style="background:#ddd;width:100%;height:200px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="text-muted">No Image</span>
                    </div>
                @endif

                <div class="card-badge 
                       @if($alert->type == 'helpline') bg-primary
                       @elseif($alert->type == 'safety') bg-warning
                       @elseif($alert->type == 'notice') bg-success
                       @elseif($alert->type == 'missing person') bg-info
                       @elseif($alert->type == 'crime_alert') bg-danger
                       @elseif($alert->type == 'Critical') bg-dark
                       @elseif($alert->type == 'Warning') bg-warning
                       @else bg-secondary @endif">
                    {{ strtoupper($alert->type) }}
                </div>
            </div>

            <div class="p-3" style="flex: 1; overflow: hidden;">
                <h5 class="fw-bold">{{ $alert->title }}</h5>
                <!-- Message preview: 4 lines max -->
                <p class="small text-secondary" style="
                    display: -webkit-box;
                    -webkit-line-clamp: 4;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    text-overflow: ellipsis;
                ">
                    {{ $alert->message }}
                </p>
            </div>
        </div>
    </div>


   <!-- Modal -->
<div class="modal fade" id="alertModal{{ $alert->id }}" tabindex="-1" aria-labelledby="alertModalLabel{{ $alert->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header d-flex align-items-center justify-content-between">
        <h5 class="modal-title" id="alertModalLabel{{ $alert->id }}">{{ $alert->title }}</h5>
        <div class="d-flex align-items-center gap-2">
            <!-- Type badge -->
            <span class="badge 
                  @if($alert->type == 'helpline') bg-primary
                  @elseif($alert->type == 'safety') bg-warning
                  @elseif($alert->type == 'notice') bg-success
                  @elseif($alert->type == 'missing person') bg-info
                  @elseif($alert->type == 'crime_alert') bg-danger
                  @elseif($alert->type == 'Critical') bg-dark
                  @elseif($alert->type == 'Warning') bg-warning
                  @else bg-secondary @endif">
                {{ strtoupper($alert->type) }}
            </span>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <!-- All media files -->
        @if(!empty($alert->media) && is_array($alert->media))
            <div class="mb-3 row g-3">
                @foreach($alert->media as $file)
                    @php
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    @endphp
                    <div class="col-md-3 mb-2">
                        @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                            <!-- Image file -->
                            <a href="{{ asset('storage/'.$file) }}" target="_blank">
                                <img src="{{ asset('storage/'.$file) }}" class="img-fluid rounded" alt="Alert Image" style="width:100%; height:200px; object-fit:cover;">
                            </a>
                        @else
                            <!-- Non-image file (PDF, doc, etc.) -->
                            <a href="{{ asset('storage/'.$file) }}" target="_blank" class="d-block p-3 border rounded text-center text-truncate" style="height:200px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                                <span class="material-symbols-outlined me-1">description</span> {{ Str::limit($file, 15) }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Full message -->
        <p>{{ $alert->message }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@empty
    <p class="text-center">No alerts available.</p>
@endforelse
</div>


    @if (Route::is('public.alerts'))
        
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4 " id="paginationLinks">
        {{ $alerts->links() }}
    </div>
    @endif



