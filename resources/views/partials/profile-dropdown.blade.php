@php
    $user = Auth::user();
@endphp

@if($user)
<a class="dropdown-toggle profile-pic d-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#" aria-expanded="false">
    <div class="avatar-sm">
        @if(!empty($user->profile_image))
            {{-- Profile Image --}}
            <img src="{{ asset('storage/' . $user->profile_image) }}" 
                 class="avatar-img rounded-circle" 
                 style="width:40px; height:40px; object-fit:cover;" />
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
    </div>
    <span class="profile-username d-none d-md-inline">{{ $user->name }}</span>
</a>


<ul class="dropdown-menu dropdown-user animated fadeIn">
    <div class="dropdown-user-scroll scrollbar-outer">
        <li class="p-2">
            <div class="user-box d-flex gap-3 flex-column flex-md-row">
                <div class="avatar-lg mb-2 mb-md-0">
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
                <div class="u-text text-center text-md-start">
                    <h5 class="mb-0">{{ $user->name }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                    <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </button>
                </div>
            </div>
        </li>
        <li>
            
           <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewProfileModal{{ $user->id }}"><i class="fas fa-user me-1"></i> My Profile</a></li>
        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Account Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    
        </li>
    </div>
</ul>
@else
<a class="btn btn-primary" href="{{ route('login') }}">Login</a>
<a class="btn btn-secondary ms-2" href="{{ route('register') }}">Register</a>
@endif
