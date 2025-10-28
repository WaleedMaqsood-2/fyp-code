{{-- <!-- My Profile Modal -->
<div class="modal fade" id="viewProfileModal{{ $user->id }}" tabindex="-1" aria-labelledby="viewProfileModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">

      <!-- Header -->
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e73df, #1cc88a);">
        <h5 class="modal-title fw-bold" id="myProfileModalLabel">
          <i class="fas fa-user-circle me-2"></i> My Profile
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body p-4 bg-light">
        <div class="row g-4">

          <!-- Profile Image + Name -->
          <div class="col-md-4 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
              <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/default-avatar.png') }}"
                   class="rounded-circle border shadow-sm mx-auto"
                   style="width:130px; height:130px; object-fit:cover;" />
              <h5 class="mt-3 fw-bold text-dark">{{ $user->name }}</h5>

              @php
                $roleColors = [
                  'Admin' => 'bg-primary',
                  'Public User' => 'bg-secondary',
                  'Forensic Analyst' => 'bg-warning text-dark',
                  'Police' => 'bg-success',
                  'Viewer' => 'bg-info text-dark',
                ];
                $roleName = $user->role->role_name ?? 'N/A';
                $roleBadge = $roleColors[$roleName] ?? 'bg-secondary';
              @endphp
              <span class="badge {{ $roleBadge }} px-3 py-2 mt-2 shadow-sm rounded-pill">{{ $roleName }}</span>
            </div>
          </div>

          <!-- Profile Details -->
          <div class="col-md-8">
            <div class="row g-3">

              <!-- Reusable card item -->
              @php
                $items = [
                  ['icon'=>'fa-envelope','color'=>'bg-primary','label'=>'Email','value'=>$user->email],
                  ['icon'=>'fa-phone','color'=>'bg-success','label'=>'Contact','value'=>$user->contact_number ?? 'Not Provided'],
                  ['icon'=>'fa-id-card','color'=>'bg-info','label'=>'CNIC','value'=>$user->cnic ?? 'Not Provided'],
                  ['icon'=>'fa-clipboard-check','color'=>'bg-warning','label'=>'Reg-Status','value'=>$user->reg_status ?? 'N/A'],
                  ['icon'=>'fa-toggle-on','color'=>'bg-dark','label'=>'Status','value'=>ucfirst($user->status ?? 'N/A')],
                  ['icon'=>'fa-check-circle','color'=>'bg-success','label'=>'Verified','value'=>$user->is_verified ? 'Yes' : 'No'],
                  ['icon'=>'fa-calendar-alt','color'=>'bg-secondary','label'=>'Member Since','value'=>$user->created_at->format("d M, Y")],
                  ['icon'=>'fa-edit','color'=>'bg-danger','label'=>'Updated At','value'=>$user->updated_at->format("d M, Y h:i A")],
                ];
              @endphp

              @foreach($items as $item)
              <div class="col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 profile-card">
                  <div class="d-flex align-items-center">
                    <div class="icon {{ $item['color'] }} text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                         style="width:45px; height:45px;">
                      <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <div>
                      <small class="text-muted">{{ $item['label'] }}</small>
                      <p class="mb-0 fw-semibold">{{ $item['value'] }}</p>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach

            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer bg-white border-0">
        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
        <button type="button" class="btn btn-gradient rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editProfileModal"
          style="background: linear-gradient(135deg, #4e73df, #1cc88a); color:white;">
          <i class="fas fa-user-edit me-1"></i> Edit Profile
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Extra CSS for hover effect -->
<style>
  .profile-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .profile-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
  }
  .btn-gradient:hover {
    opacity: 0.9;
  }
</style> --}}
<!-- My Profile Modal -->
<div class="bootstrap-scope">
<div class="modal fade" id="viewProfileModal{{ $user->id }}" tabindex="-1" aria-labelledby="viewProfileModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">

      <!-- Header -->
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e73df, #1cc88a);">
        <h5 class="modal-title fw-bold" id="myProfileModalLabel">
          <i class="fas fa-user-circle me-2"></i> My Profile
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body p-4 bg-light">
        <div class="row g-4">

          <!-- Profile Image + Name -->
          <div class="col-md-4 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
              @if(!empty($user->profile_image))
       {{-- Profile Image --}}
              <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/default-avatar.png') }}"
                   class="rounded-circle border shadow-sm mx-auto"
                   style="width:130px; height:130px; object-fit:cover;" />
            
        @else
            {{-- Fallback: Initials --}}
            @php
                $nameParts = explode(' ', trim($user->name ?? ''));
                $initials = strtoupper(substr($nameParts[0] ?? 'N', 0, 1));
                if (count($nameParts) > 1) {
                    $initials .= strtoupper(substr($nameParts[1], 0, 1));
                }
            @endphp
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mx-auto" 
                 style="width:130px; height:130px; font-weight:600; font-size:24px;">
                {{ $initials }}
            </div>
        @endif
              <h5 class="mt-3 fw-bold text-dark">{{ $user->name }}</h5>

              @php
                $roleColors = [
                  'Admin' => 'bg-primary',
                  'Public User' => 'bg-secondary',
                  'Forensic Analyst' => 'bg-warning text-dark',
                  'Police' => 'bg-success',
                  'Viewer' => 'bg-info text-dark',
                ];
                $roleName = $user->role->role_name ?? 'N/A';
                $roleBadge = $roleColors[$roleName] ?? 'bg-secondary';
              @endphp
              <span class="badge {{ $roleBadge }} px-3 py-2 mt-2 shadow-sm rounded-pill">{{ $roleName }}</span>
            </div>
          </div>

          <!-- Profile Details -->
          <div class="col-md-8">
            <div class="row g-3">

              <!-- Reusable card item -->
              @php
                $items = [
                  ['icon'=>'fa-envelope','color'=>'bg-primary','label'=>'Email','value'=>$user->email],
                  ['icon'=>'fa-phone','color'=>'bg-success','label'=>'Contact','value'=>$user->contact_number ?? 'Not Provided'],
                  ['icon'=>'fa-id-card','color'=>'bg-info','label'=>'CNIC','value'=>$user->cnic ?? 'Not Provided'],
                  ['icon'=>'fa-clipboard-check','color'=>'bg-warning','label'=>'Reg-Status','value'=>$user->reg_status ?? 'N/A'],
                  ['icon'=>'fa-toggle-on','color'=>'bg-dark','label'=>'Status','value'=>ucfirst($user->status ?? 'N/A')],
                  ['icon'=>'fa-check-circle','color'=>'bg-success','label'=>'Verified','value'=>$user->is_verified ? 'Yes' : 'No'],
                  ['icon'=>'fa-calendar-alt','color'=>'bg-secondary','label'=>'Member Since','value'=>$user->created_at->format("d M, Y")],
                  ['icon'=>'fa-edit','color'=>'bg-danger','label'=>'Updated At','value'=>$user->updated_at->format("d M, Y h:i A")],
                ];
              @endphp
@foreach($items as $item)
<div class="col-sm-6">
  <div class="card border-0 rounded-4 shadow-sm h-100 p-3 profile-card bg-white">
    <div class="d-flex align-items-start">
      <div class="{{ $item['color'] }} text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3 shadow-sm"
           style="width:45px; height:45px;">
        <i class="fas {{ $item['icon'] }}" style="font-size:16px;"></i>
      </div>
      <div class="text-wrap" style="overflow-wrap: break-word; word-break: break-word; min-width:0;">
        <small class="text-muted d-block mb-1">{{ $item['label'] }}</small>
        <p class="mb-0 fw-semibold text-dark" style="font-size: 14px; white-space: normal;">
          {{ $item['value'] }}
        </p>
      </div>
    </div>
  </div>
</div>
@endforeach


            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer bg-white border-0">
        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
        <button type="button" class="btn btn-gradient rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editProfileModal"
          style="background: linear-gradient(135deg, #4e73df, #1cc88a); color:white;">
          <i class="fas fa-user-edit me-1"></i> Edit Profile
        </button>
      </div>
    </div>
  </div>
</div>
</div>


<!-- Extra CSS -->

<style>
  
  /* Modal fix overrides */
  .modal-content {
    background-color: #fff !important;
    border-radius: 1rem !important;
  }
  .modal-header {
    background: linear-gradient(135deg, #4e73df, #1cc88a) !important;
    color: #fff !important;
  }
  .modal-body, .modal-footer {
    background-color: #f8f9fa !important;
  }
  .profile-card {
    background: #fff !important;
    border-radius: 1rem !important;
  }
  .btn-close {
    filter: invert(1) !important;
  }
  
.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Fix for long text wrapping inside cards */
.profile-card p {
  overflow-wrap: break-word;
  word-break: break-word;
  white-space: normal;
}

/* Keep icons centered */
.profile-card .rounded-circle {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Animation */
.modal-content {
  animation: slideUp 0.4s ease;
}
 .profile-card {
  transition: all 0.3s ease;
}
@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Responsive tweaks */
@media (max-width: 767px) {
  .modal-dialog {
    margin: 1rem;
  }
  .modal-body {
    padding: 1.5rem;
  }
}


</style>
