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
