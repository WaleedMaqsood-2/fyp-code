@extends('layouts.master')
@php
    if (!auth()->check()) {
        header('Location: ' . route('login'));
        exit;
    }
@endphp
@section('content')
<div class="container">
     <div class="ms-2 mt-4">
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
    <div class="card shadow-sm">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Manage Users</h3>
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        </div>
        <div class="card-body">
<div id="user-card">
  @include('admin.partials.user-cards', ['users' => $users])
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

@php
  $searchConfig = [
    'endpoint' => route('admin.user.search'),
    'suggestionKey' => 'users',
    'resultKey' => 'users',
  ];
@endphp
<script>window.searchConfig = @json($searchConfig);
</script>


@php
  $searchAction = route('admin.user.search'); 
  $searchPlaceholder = 'Search Users...';
@endphp

{{-- @section('scripts')
<script>
    const searchInput = document.getElementById('searchInput');
    const cardsContainer = document.getElementById('user-card');
    searchInput.addEventListener('keyup', function () {
  let query = this.value;


  // cards refresh
  fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}&mode=cards`)
    .then(res => res.text())
    .then(html => {
      cardsContainer.innerHTML = html; // replace card content
    });
});

</script>
@endsection --}}



