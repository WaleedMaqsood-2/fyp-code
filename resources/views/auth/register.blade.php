<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
        }
         .card {
        border-radius: 1rem; /* main card rounded corners */
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.2);
        overflow: hidden; /* important: clip child elements so header merges with card */
    }
    .card-header {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        color: white;
        border: none; /* remove default border */
        border-radius: 0; /* remove rounded corners from header itself */
        padding: 1rem;
    }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <div class="card ">
                    <div class="card-header text-white">Register</div>
                    <div class="card-body p-4">
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
                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label ">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label ">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cnic" class="form-label ">CNIC</label>
                                <input type="text" name="cnic" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label ">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label ">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label ">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-4">Register</button>
                    </form>
                    <div class="mt-3 text-center ">
                        <a href="{{ route('login') }}" class="text-decoration-none ">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
