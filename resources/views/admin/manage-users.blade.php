@extends('layouts.master')

@section('content')
<div class="container">
     <div class="ms-2 mt-4">
         @if ($errors->any())
    <div class="alert alert-danger mt-2">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="card shadow-sm">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Manage Users</h3>
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif


            <div class="row">
                @foreach($users as $user)
                <div class="col-12 col-md-6 col-lg-4 mb-4 ">
                    <div class="card h-75 py-5 shadow-sm position-relative border-0" style="background: linear-gradient(135deg, #f8fafc 60%, #e3e6ed 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                        <!-- Action Dropdown Button -->
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
                                 <!-- View Profile Modal -->
                    <div class="modal fade" id="viewProfileModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">User Profile</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card border-0 shadow-sm mx-auto" style="max-width: 400px;">
                                        <div class="card-body text-center">
                                            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/default-avatar.png') }}" alt="Profile Image" class="img-fluid rounded-circle border mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                                            <h5 class="fw-bold text-primary mb-2">{{ $user->name }}</h5>
                                            <p class="mb-1"><span class="fw-semibold">Email:</span> {{ $user->email }}</p>
                                            <p class="mb-1">
                                                <span class="fw-semibold ">Role:</span>
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
                                                <span class="badge {{ $badgeClass }}">{{ $roleName }}</span>
                                            </p>
                                            <p class="mb-1"><span class="fw-semibold">Contact:</span> {{ $user->contact_number }}</p>
                                            <p class="mb-1"><span class="fw-semibold">CNIC:</span> {{ $user->cnic }}</p>
                                            @php
                                                $reg_status_color=[
                                                    'pending' => 'bg-danger',
                                                    'Registered' => 'bg-success',
                                                    'Rejected' => 'bg-danger',
                                                ];
                                                $reg_status_name = $user->reg_status ?? 'N/A';
                                                $badgeClass = $reg_status_color[$reg_status_name] ?? 'text-secondary ';
                                            @endphp
                                            <p class="mb-1" ><span class="fw-semibold">Reg-Status:</span> <span class="badge {{ $badgeClass }}">{{ $reg_status_name }}</span></p>
                                                @php
                                                    $status_color=[
                                                        'inactive' => 'bg-danger',
                                                        'active' => 'bg-success',
                                                        
                                                    ];
                                                    $status_name = $user->status ?? 'N/A';
                                                    $badgeClass = $status_color[$status_name] ?? 'text-secondary ';
                                                @endphp
                                            <p class="mb-1"><span class="fw-semibold">Status:</span> <span class="badge {{ $badgeClass }}">{{ $user->status }}</span></p>
                                            @php
                                                $is_verified_color=[
                                                    '0' => 'bg-danger',
                                                    '1' => 'bg-success',
                                                    
                                                ];
                                                $is_verified_name = $user->is_verified ?? 'N/A';
                                                $badgeClass = $is_verified_color[$is_verified_name] ?? 'text-secondary ';
                                            @endphp
                                            <p class="mb-1"><span class="fw-semibold">Is verified:</span> <span class="badge {{ $badgeClass }}">{{ $user->is_verified ? 'Yes' : 'No' }}</span></p>

                                            <p class="mb-1"><span class="fw-semibold">Email-Verified-At:</span> {{ $user->email_verified_at }}</p>
                                            <p class="mb-1"><span class="fw-semibold">Created At:</span> {{ $user->created_at }}</p>
                                            <p class="mb-0"><span class="fw-semibold">Updated At:</span> {{ $user->updated_at}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                                    <p class="mb-0"><span class="fw-semibold text-dark fs-6">Email:</span> <span class="text-muted fs-6">{{ Str::limit($user->email, 15, '...') }}</span></p>
                                    <p class="mb-0">
                                        <span class="fw-semibold text-dark fs-6">Role:</span>
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
                                        <span class="badge {{ $badgeClass }}">{{ $roleName }}</span>
                                    </p>
                                    <p class="mb-0"><span class="fw-semibold text-dark fs-6">Contact:</span> <span class="text-muted fs-6">{{ Str::limit($user->contact_number, 15, '...') }}</span></p>
                                    <p class="mb-0"><span class="fw-semibold text-dark fs-6">CNIC:</span> <span class="text-muted fs-6">{{ Str::limit($user->cnic, 10, '...') }}</span></p>
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
            </div>

            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Add New User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="text" name="name" placeholder="Name" class="form-control mb-2" required value="{{ old('name') }}">
                                <input type="email" name="email" placeholder="Email" class="form-control mb-2" required value="{{ old('email') }}">
                                <select name="role_id" class="form-control mb-2" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                                <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control mb-2" required>
                                <input type="text" name="cnic" placeholder="CNIC" class="form-control mb-2" required value="{{ old('cnic') }}">
                                <input type="text" name="contact_number" placeholder="Phone" class="form-control mb-2" required value="{{ old('contact_number') }}">
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
