 @if($users->count())
  <ul class="list-group">
    @foreach($users as $user)
      <li class="list-group-item">
        <strong>{{ $user->name }}</strong> ({{ $user->email }})
    <br>
    @php
      $roleColors = [
        'Admin' => 'bg-primary text-white',
        'Forensic Analyst' => 'bg-success text-white',
        'Police' => 'bg-info text-dark',
        'Public User' => 'bg-secondary text-white',
      ];
        $roleName = $user->role->role_name ?? 'N/A';
        $badgeClass = $roleColors[$roleName] ?? 'bg-light text-dark';
    @endphp
    <span class="badge {{ $badgeClass }}">{{ $roleName }}</span>

      </li>
    @endforeach
  </ul>
@else
  <div class="p-3">No users found.</div>
@endif

{{-- <div class="row">
    @forelse($users as $user)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-75 py-5 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold">{{ $user->name }}</h5>
                    <p class="mb-0">{{ $user->email }}</p>
                    <span class="badge bg-primary">{{ $user->role->role_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p>No users found.</p>
        </div>
    @endforelse
</div> --}}
