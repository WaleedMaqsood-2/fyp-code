<!-- Profile Edit Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"><!-- Large + Centered -->
    <div class="modal-content shadow-lg rounded-3">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editProfileModalLabel">
          <i class="fas fa-user-edit me-2"></i>Edit Profile
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-3"><!-- Spacing between inputs -->

            <!-- Name -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Name</label>
              <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
            </div>

            <!-- Password -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Password <small class="text-muted">(leave blank if not changing)</small></label>
              <input type="password" name="password" class="form-control">
            </div>

            <!-- Confirm Password -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control">
            </div>

            <!-- CNIC -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">CNIC</label>
              <input type="text" name="cnic" value="{{ $user->cnic }}" class="form-control">
            </div>

            <!-- Contact Number -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Number</label>
              <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="form-control">
            </div>

            <!-- Profile Image -->
            <div class="col-md-12">
              <label class="form-label fw-semibold">Profile Image</label>
              <input type="file" name="profile_image" class="form-control">
              @if($user->profile_image)
                <div class="mt-3">
                  <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" 
                       class="img-thumbnail rounded shadow-sm" style="width:100px; height:100px; object-fit:cover;">
                </div>
              @endif
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check-circle me-1"></i> Update Profile
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
