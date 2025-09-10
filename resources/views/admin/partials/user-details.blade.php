@if($user)
<div class="card">
  <div class="card-body">
    @if (isset($user->name))
    <h5 class="card-title">{{ $user->name }}</h5>
    @endif
    @if (isset($user->email))
    <p class="card-text mb-1"><strong>Email:</strong> {{ $user->email }}</p>
    @endif
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
    @if (isset($user->role))
    <p class="card-text mb-1"><strong>Role:</strong> <span class="badge {{ $badgeClass }}">{{ $roleName }}</span></p>
    @endif
    @if(isset($user->cnic))
      <p class="card-text mb-1"><strong>CNIC:</strong> {{ $user->cnic }}</p>
    @endif
    @if(isset($user->contact_number))
     <p class="card-text mb-1"><strong>Contact:</strong> {{ $user->contact_number }}</p>
    @endif
     @php
  $reg_status_color=[
 'pending' => 'bg-danger',
'Registered' => 'bg-success',
'Rejected' => 'bg-danger',
];
  $reg_status_name = $user->reg_status ?? 'N/A';
   $badgeClass = $reg_status_color[$reg_status_name] ?? 'text-secondary ';
  @endphp
      @if(isset($user->reg_status))
  <p class="card-text mb-1"><strong>Reg-Status:</strong> <span class="badge {{ $badgeClass }}">{{ $reg_status_name }}</span></p>
@endif
@php
 $status_color=[
 'inactive' => 'bg-danger',
 'active' => 'bg-success',
 ];
 $status_name = $user->status ?? 'N/A';
 $badgeClass = $status_color[$status_name] ?? 'text-secondary ';
  @endphp
      @if(isset($user->status))
  <p class="card-text mb-1"><strong>Status:</strong> <span class="badge {{ $badgeClass }}">{{ $user->status }}</span></p>
  @endif
  @php
   $is_verified_color=[
  '0' => 'bg-danger',
 '1' => 'bg-success',      
  ];
  $is_verified_name = $user->is_verified ?? 'N/A';
  $badgeClass = $is_verified_color[$is_verified_name] ?? 'text-secondary ';
  @endphp
      @if(isset($user->is_verified))
 <p class="card-text mb-1"><strong>Is verified:</strong> <span class="badge {{ $badgeClass }}">{{ $user->is_verified ? 'Yes' : 'No' }}</span></p>
@endif
    @if(isset($user->email_verified_at))
 <p class="card-text mb-1"><strong>Email-Verified-At:</strong> {{ $user->email_verified_at }}</p>
@endif
     @if(isset($user->created_at))
 <p class="card-text mb-1"><strong>Created At:</strong> {{ $user->created_at }}</p>
@endif
     @if(isset($user->updated_at))
 <p class="card-text mb-1"><strong>Updated At:</strong> {{ $user->updated_at}}</p>
        @endif                                
  </div>
</div>
@else
<div class="p-3">User not found.</div>
@endif
