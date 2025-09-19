<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CrimeWatch - Your Centralized Security Platform</title>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Material Symbols -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

  <style>
    :root {
      --primary-color: #1173d4;
      --secondary-color: #f0f4f8;
      --text-primary: #1a202c;
      --text-secondary: #4a5568;
    }
    body {
      font-family: "Public Sans", "Noto Sans", sans-serif;
      background-color: #f8fafc;
    }
    .navbar-brand svg {
      color: var(--primary-color);
    }
    .hero-section {
      position: relative;
      background: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBgLLSwYccy51UN2lxvruCQ3CCovnXwi6MATXrwL9ZOdewI_udGIbZTuUouQVYsiSvunSSHloGgMz50hKEa1F72tjMgglKn8NAM9DmYZNg7BHJYKfYWjALKszIauk7QXLp_u9WG-7EkkLEj3jS9IFbbiIvW6Mjgp9_VHUmScswm4Qd2NCJWLlnIjuSj8G-wekQv-sbNZidwtC_GwSNzfaKtHBjEM8ZJNUt3AF6ovu-iOm596nWy8-Oo2S3rkNCFB96vNB0Dmv5Zwyap') no-repeat center center/cover;
      min-height: 100vh;
      color: white;
    }
    .hero-overlay {
      background: rgba(0, 0, 0, 0.5);
      position: absolute;
      inset: 0;
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    .card img {
      height: 220px;
      object-fit: cover;
    }
    footer {
      background: var(--secondary-color);
    }
    footer .brand {
      color: var(--primary-color);
    }
  </style>
</head>
<body >
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
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container my-2">
      <a class="navbar-brand d-flex align-items-center fw-bold text-dark" href="#">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="me-2" viewBox="0 0 48 48">
          <path d="M44 11.2727C44 14.0109 39.8386 16.3957 33.69 17.6364C39.8386 18.877 44 21.2618 44 24C44 26.7382 39.8386 29.123 33.69 30.3636C39.8386 31.6043 44 33.9891 44 36.7273C44 40.7439 35.0457 44 24 44C12.9543 44 4 40.7439 4 36.7273C4 33.9891 8.16144 31.6043 14.31 30.3636C8.16144 29.123 4 26.7382 4 24C4 21.2618 8.16144 18.877 14.31 17.6364C8.16144 16.3957 4 14.0109 4 11.2727C4 7.25611 12.9543 4 24 4C35.0457 4 44 7.25611 44 11.2727Z"/>
        </svg>
        CrimeWatch
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="#">About</a></li>
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="#">Services</a></li>
          <li class="nav-item"><a class="nav-link fw-medium mx-lg-4" href="#">Contact</a></li>
        </ul>
        <div class="d-flex gap-2">
          <a href="#" class="btn btn-primary">Login</a>
          <a href="#" class="btn btn-outline-dark">Sign Up</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-sm-start">
      <h1 class="display-4 fw-bold">Welcome to 
        <span class="text-primary">CrimeWatch</span>
      </h1>
      <p class="lead mt-3">Your centralized platform for managing complaints, FIRs, evidence, and public alerts. Stay informed and secure with our AI-powered analysis.</p>
      <div class="mt-4 d-flex gap-3 flex-wrap">
        <a href="#" class="btn btn-primary btn-lg px-lg-5 ">Login</a>
        <a href="#" class="btn btn-light btn-lg px-lg-5">Sign Up</a>
      </div>
    </div>
  </section>

  <!-- Public Alerts -->
  <section class="py-5 bg-white">
    <div class="container text-center">
      <h2 class="fw-bold mb-3">Public Alerts</h2>
      <p class="text-dark">Stay updated with the latest security information in your area.</p>
      <div class="row mt-4 g-4">
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm border-0">
            <img src="https://picsum.photos/600/400?random=1" class="card-img-top" alt="">
            <div class="card-body">
              <h5 class="card-title fw-bold">Traffic Alert: Road Closure</h5>
              <p class="card-text text-secondary">Due to investigation, Main Street is closed. Please use alternative routes.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm border-0">
            <img src="https://picsum.photos/600/400?random=2" class="card-img-top" alt="">
            <div class="card-body">
              <h5 class="card-title fw-bold">Safety Advisory</h5>
              <p class="card-text text-secondary">Increased police patrols in downtown. Stay vigilant and report suspicious activity.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm border-0">
            <img src="https://picsum.photos/600/400?random=3" class="card-img-top" alt="">
            <div class="card-body">
              <h5 class="card-title fw-bold">Community Event</h5>
              <p class="card-text text-secondary">Join our free safety awareness workshop this Saturday at 10 AM.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-4">
    <div class="container d-flex flex-column flex-lg-row justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
       <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="me-2" viewBox="0 0 48 48">
          <path d="M44 11.2727C44 14.0109 39.8386 16.3957 33.69 17.6364C39.8386 18.877 44 21.2618 44 24C44 26.7382 39.8386 29.123 33.69 30.3636C39.8386 31.6043 44 33.9891 44 36.7273C44 40.7439 35.0457 44 24 44C12.9543 44 4 40.7439 4 36.7273C4 33.9891 8.16144 31.6043 14.31 30.3636C8.16144 29.123 4 26.7382 4 24C4 21.2618 8.16144 18.877 14.31 17.6364C8.16144 16.3957 4 14.0109 4 11.2727C4 7.25611 12.9543 4 24 4C35.0457 4 44 7.25611 44 11.2727Z"/>
        </svg>
        <span class="fw-bold">CrimeWatch</span>
      </div>
      <div class="mt-4 mt-lg-0 text-center text-secondary ">
          <p class="text-secondary small mb-0">&copy; 2024 CrimeWatch. All rights reserved.</p>
          <div class="mt-4 text-center text-secondary small">
              <a   class="text-decoration-none text-dark ms-4" href="#">Privacy Policy</a>
              <a class="text-decoration-none text-dark ms-4" href="#">Terms of Service</a>
              <a class="text-decoration-none text-dark ms-4" href="#">Contact Us</a>
            </div>
        </div>
   
  </footer>

 
 
</body>
</html>
