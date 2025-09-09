<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

<div class="container mt-5">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header bg-primary text-white">Register</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                      <div class="mb-3">
                          <label for="cnic" class="form-label">CNIC</label>
                          <input type="text" name="cnic" class="form-control" required>
                      </div>
                      <div class="mb-3">
                          <label for="contact_number" class="form-label">Contact Number</label>
                          <input type="text" name="contact_number" class="form-control" required>
                      </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>