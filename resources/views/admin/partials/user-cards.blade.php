
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
@include('partials.view-profile-model')
      

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