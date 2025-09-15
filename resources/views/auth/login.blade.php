<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

<div class="card shadow p-4" style="width: 400px;">
    <h4 class="mb-3">Login</h4>

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


    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <div class="mt-3 text-center">
        <a href="{{ route('register') }}">Don't have an account? Register</a>
    </div>
</div>

</body>
</html>
