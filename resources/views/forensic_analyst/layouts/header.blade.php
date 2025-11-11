<header class="topbar">
     <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="fs-4 text-primary">
                <a href="{{ route('forensic.dashboard') }}" class="text-decoration-none">
                <i class="bi bi-shield-lock-fill"></i></div>
            <div>
                <h5 class="mb-0 fw-bold">Forensic Analysis <br>Dashboard</h5>
               
            </div>
            </a>
        </div>
        <div class="input-group d-none d-md-flex ms-4" style="width:350px;">
            <input id="searchInput" type="text" class="form-control form-control-sm bg-body-secondary border-0" placeholder="Search by Case ID, FIR...">
            <span class="input-group-text bg-body-secondary border-0"><i class="bi bi-search"></i></span>
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-2">

        <button id="sidebarToggle" class="btn btn-sm btn-light d-lg-none"><i class="bi bi-list"></i></button>

        <button class="btn btn-sm btn-outline-secondary" title="Notifications"><i class="bi bi-bell"></i></button>

        <!-- Theme toggle -->
        <button id="themeToggle" class="btn btn-sm btn-outline-secondary" title="Toggle theme"><i id="themeIcon" class="bi bi-moon"></i></button>

        <!-- user -->
        {{-- <div class="d-flex align-items-center gap-2 ms-2">
            <div class="avatar-sm" style="background-image:url('{{ auth()->user()->avatar ?? 'https://via.placeholder.com/44' }}');"></div>
            <div class="d-none d-md-block text-start">
                <div class="fw-semibold">{{ auth()->user()->name ?? 'Analyst' }}</div>
                <div class="small muted">{{ auth()->user()->role ?? 'Forensic Analyst' }}</div>
            </div>
        </div> --}}

         @if($user)
 {{-- @include('partials.profile-dropdown') --}}
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
               <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
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
@include('partials.edit-profile-modal')
@include('partials.view-profile-model')