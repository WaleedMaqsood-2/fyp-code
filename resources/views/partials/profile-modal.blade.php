<!-- Profile Edit Modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label>Name</label>
              <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password (leave blank if not changing)</label>
              <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
              <label>Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
              <label>CNIC</label>
              <input type="text" name="cnic" value="{{ $user->cnic }}" class="form-control">
            </div>
            <div class="mb-3">
              <label>Contact Number</label>
              <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="form-control">
            </div>
            <div class="mb-3">
              <label>Profile Image</label>
              <input type="file" name="profile_image" class="form-control">
              @if($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="img-thumbnail mt-2" width="100">
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Update Profile</button>
          </div>
        </form>
      </div>
    </div>
  </div>