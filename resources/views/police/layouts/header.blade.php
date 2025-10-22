<header class="d-flex align-items-center justify-content-between px-4">
  <div class="d-flex align-items-center gap-3">
     <a href="{{ route('police.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
    <span class="material-symbols-outlined text-primary fs-2">local_police</span>
    <h4 class="mb-0 fw-bold fs-5">Police Dashboard</h4>
</a>
    <div class="input-group ms-4 d-none d-md-flex" style="width:200px;">
      <input type="text" class="form-control border-0 bg-body-secondary" placeholder="Search" />
      <span class="input-group-text bg-body-secondary border-0"><span class="material-symbols-outlined">search</span></span>
    </div>
  </div>

  <div class="d-flex align-items-center gap-3">
    <span class="material-symbols-outlined fs-4">notifications</span>
    <span id="themeToggle" class="material-symbols-outlined dark-toggle">dark_mode</span>

@if($user)
    <!-- Profile Dropdown Trigger -->
    <a class="d-flex align-items-center gap-2 text-decoration-none" 
       data-bs-toggle="dropdown" href="#" aria-expanded="false">
      
             @if(!empty($user->profile_image))
            {{-- Profile Image --}}
  <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/profile.jpg') }}" 
             class="rounded-circle border shadow-sm" 
             style="width:42px; height:42px; object-fit:cover;" />
        @else
            {{-- Fallback: Initials --}}
            @php
                $nameParts = explode(' ', trim($user->name ?? ''));
                $initials = strtoupper(substr($nameParts[0] ?? 'N', 0, 1));
                if (count($nameParts) > 1) {
                    $initials .= strtoupper(substr($nameParts[1], 0, 1));
                }
            @endphp
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" 
                 style="width:40px; height:40px; font-weight:600; font-size:16px;">
                {{ $initials }}
            </div>
        @endif
        <span class="fw-semibold d-none d-md-inline">{{ $user->name }}</span>
    </a>


    <!-- Dropdown -->
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2">

  <li class=" py-2">
    <div class="d-flex align-items-center text-start" style="max-width: 220px; margin:auto;">
        <!-- Profile Image -->
        <div class="d-flex justify-content-center align-items-center" style="width:80px;">

                 @if(!empty($user->profile_image))
            {{-- Profile Image --}}
            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/profile.jpg') }}" 
                 class="rounded-circle border shadow-sm" 
                 style="width:70px; height:70px; object-fit:cover;" />
        @else
            {{-- Fallback: Initials --}}
            @php
                $nameParts = explode(' ', trim($user->name ?? ''));
                $initials = strtoupper(substr($nameParts[0] ?? 'N', 0, 1));
                if (count($nameParts) > 1) {
                    $initials .= strtoupper(substr($nameParts[1], 0, 1));
                }
            @endphp
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" 
                 style="width:70px; height:70px; font-weight:600; font-size:24px;">
                {{ $initials }}
            </div>
        @endif
        </div>

        <!-- Name, Email & Button -->
        <div class="flex-grow-1">
            <h6 class="mb-0 fw-semibold" style="font-size: 14px; max-width:120px; word-break: break-word;">
                {{ $user->name }}
            </h6>
            <small class="text-muted d-block" style="font-size: 12px; max-width:125px; word-break: break-word;">
                {{ $user->email }}
            </small>
            <div class="mt-2">
                <button type="button" style="font-size: 12px" class="btn btn-sm btn-primary px-2 " data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>
</li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewProfileModal{{ $user->id }}"><i class="fas fa-user me-1"></i> My Profile</a></li>
        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Account Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
    @endif
</div>
</header>
