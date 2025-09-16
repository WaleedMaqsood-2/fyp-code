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
<div class="row">
  
  @if ($users->count())
  @foreach($users as $user)
  <style>
    .user-card{
      font-size: 13px;
    }
  </style>
  <div class="col-12 col-md-6 col-lg-4 mb-4 ">
                    
                
                    <div class="card h-75 py-5 shadow-sm position-relative border-0" style="background: linear-gradient(135deg, #f8fafc 60%, #e3e6ed 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                        <!-- Action Dropdown Button -->
                         <!-- Role Badge (Top-Left) -->
        @php
            $roleColors = [
                'Admin' => 'bg-primary',
                'Public User' => 'bg-secondary',
                'Police' => 'bg-success',
                'Forensic Analyst' => 'bg-warning text-dark',
                'Viewer' => 'bg-secondary',
            ];
            $roleName = $user->role->role_name ?? 'N/A';
            $badgeClass = $roleColors[$roleName] ?? 'bg-info text-dark';
        @endphp
        <span class="badge {{ $badgeClass }} position-absolute top-0 start-0 m-2 px-3 py-1">
            {{ $roleName }}
        </span>

        <!-- Action Dropdown Button (Top-Right) -->
        <div class="position-absolute top-0 end-0 m-2">
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionDropdown{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown{{ $user->id }}">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewProfileModal{{ $user->id }}"><i class="fas fa-user me-1"></i> View Profile</a></li>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i class="fas fa-edit me-1"></i> Edit</a></li>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeRoleModal{{ $user->id }}"><i class="fas fa-exchange-alt me-1"></i> Change Role</a></li>
                  
                   
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}"><i class="fas fa-trash-alt me-1"></i> Delete</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-ban me-1"></i> Block</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-envelope me-1"></i> Send Email</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-user-slash me-1"></i> Remove Access</a></li>
                                </ul>
{{-- viewProfileModal --}}
  
                                       <div class="modal fade" id="viewProfileModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Wider for better layout -->
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-user-circle me-2"></i> User Profile
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      
      <div class="modal-body">
        <div class="row g-4 align-items-center">
          
          <!-- Profile Image -->
          <div class="col-md-4 text-center">
            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/default-avatar.png') }}"
                 alt="Profile Image"
                 class="img-fluid rounded-circle border shadow-sm"
                 style="width: 130px; height: 130px; object-fit: cover;">
                  @php
$roleColors = [
'Admin' => 'bg-primary ',
'Public User' => 'bg-secondary',
'Forensic Analyst' => 'bg-warning text-dark',
'Police' => 'bg-success',
'Viewer' => 'bg-secondary',
];
$roleName = $user->role->role_name ?? 'N/A';
$badgeClass = $roleColors[$roleName] ?? 'bg-info text-dark';
@endphp
            <h5 class="fw-bold text-primary mt-3">{{ $user->name }}</h5>
            <span class="badge {{ $badgeClass }} px-3 py-1">{{ $roleName }}</span>
          </div>
          
          <!-- User Info -->
          <div class="col-md-8">
            <div class="row g-3">
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Email</p>
                <p class="mb-0">{{ $user->email }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Contact</p>
                <p class="mb-0">{{ $user->contact_number ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">CNIC</p>
                <p class="mb-0">{{ $user->cnic ?? 'N/A' }}</p>
              </div>
@php
$reg_status_color=[
'pending' => 'bg-danger',
'Registered' => 'bg-success',
'Rejected' => 'bg-danger',
];
$reg_status_name = $user->reg_status ?? 'N/A';
$badgeClass = $reg_status_color[$reg_status_name] ?? 'text-secondary ';
@endphp
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Reg-Status</p>
                <p class="mb-0"><span class="badge {{ $badgeClass }}">{{ $reg_status_name }}</span></p>
              </div>
              @php
$status_color=[
'inactive' => 'bg-danger',
'active' => 'bg-success',

];
$status_name = $user->status ?? 'N/A';
$badgeClass = $status_color[$status_name] ?? 'text-secondary ';
@endphp
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Status</p>
                <p class="mb-0"><span class="badge {{ $badgeClass }}">{{ $status_name }}</span></p>
              </div>
              @php
$is_verified_color=[
'0' => 'bg-danger',
'1' => 'bg-success',
];
$is_verified_name = $user->is_verified ?? 'N/A';
$badgeClass = $is_verified_color[$is_verified_name] ?? 'text-secondary ';
@endphp
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Verified</p>
                <p class="mb-0"><span class="badge {{ $badgeClass }}">{{ $user->is_verified ? 'Yes' : 'No' }}</span></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Email Verified At</p>
                <p class="mb-0">{{ $user->email_verified_at ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Created At</p>
                <p class="mb-0">{{ $user->created_at }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Updated At</p>
                <p class="mb-0">{{ $user->updated_at }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
                                 <!-- View Profile Modal -->
                    <div class="modal fade" id="viewProfileModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Wider for better layout -->
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-user-circle me-2"></i> User Profile
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      
      <div class="modal-body">
        <div class="row g-4 align-items-center">
          
          <!-- Profile Image -->
          <div class="col-md-4 text-center">
            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/default-avatar.png') }}"
                 alt="Profile Image"
                 class="img-fluid rounded-circle border shadow-sm"
                 style="width: 130px; height: 130px; object-fit: cover;">
            <h5 class="fw-bold text-primary mt-3">{{ $user->name }}</h5>
            <span class="badge {{ $badgeClass }} px-3 py-1">{{ $roleName }}</span>
          </div>
          
          <!-- User Info -->
          <div class="col-md-8">
            <div class="row g-3">
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Email</p>
                <p class="mb-0">{{ $user->email }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Contact</p>
                <p class="mb-0">{{ $user->contact_number ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">CNIC</p>
                <p class="mb-0">{{ $user->cnic ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Reg-Status</p>
                <p class="mb-0"><span class="badge {{ $badgeClass }}">{{ $reg_status_name }}</span></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Status</p>
                <p class="mb-0"><span class="badge {{ $badgeClass }}">{{ $status_name }}</span></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Verified</p>
                <p class="mb-0"><span class="badge {{ $badgeClass }}">{{ $user->is_verified ? 'Yes' : 'No' }}</span></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Email Verified At</p>
                <p class="mb-0">{{ $user->email_verified_at ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Created At</p>
                <p class="mb-0">{{ $user->created_at }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1 fw-semibold text-muted">Updated At</p>
                <p class="mb-0">{{ $user->updated_at }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

                            </div>
                        </div>


                          <!-- Change Role Modal -->
                    <div class="modal fade" id="changeRoleModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('users.updateRole', $user->id) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Change User Role</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <select name="role_id" class="form-control mb-2" required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Change Role</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



    <div class="card-body py-4 px-3">
        <div class="row align-items-center">
            <div class="col-4 d-flex align-items-center justify-content-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/default-avatar.png') }}" alt="Profile Image" class="img-fluid rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="col-8">
                                    <h5 class="card-title mb-2 fw-bold text-primary ">{{ Str::limit($user->name, 10, '...') }}</h5>
                                    <p class="mb-0"><span class="fw-semibold text-dark fs-6">Email:</span> <span class="text-muted user-card">{{ Str::limit($user->email, 15, '...') }}</span></p>

                                    <p class="mb-0"><span class="fw-semibold text-dark fs-6">Contact:</span> <span class="text-muted user-card">{{ Str::limit($user->contact_number, 15, '...') }}</span></p>
                                    <p class="mb-0"><span class="fw-semibold text-dark fs-6">CNIC:</span> <span class="text-muted user-card">{{ Str::limit($user->cnic, 10, '...') }}</span></p>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                  
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control mb-2" required>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control mb-2" required>
                                        <input type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" class="form-control mb-2" required>
                                        <input type="text" name="cnic" value="{{ old('cnic', $user->cnic) }}" class="form-control mb-2" required>
                                        <select name="role_id" class="form-control mb-2" required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->role_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="password" name="password" placeholder="New Password (optional)" class="form-control mb-2">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete {{ $user->name }}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                  @else
                    <div class="text-center"><h3>No user found</h3></div>
                    @endif
            </div>