<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Citizen Connect - Account Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <style>
    body {
      font-family: 'Public Sans', 'Noto Sans', sans-serif;
      background-color: #f9fafb;
    }
    :root {
      --primary-color: #1173d4;
    }
    .sidebar {
      width: 250px;
      background: #fff;
      border-right: 1px solid #e5e7eb;
      height: 100vh;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 15px;
      color: #555;
      text-decoration: none;
      border-radius: 8px;
      transition: background 0.2s;
    }
    .sidebar a.active,
    .sidebar a:hover {
      background: #f1f5f9;
      color: #000;
    }
    .form-label {
      font-weight: 500;
      color: #111827;
    }
    .form-control {
      border-radius: 6px;
      border: 1px solid #d1d5db;
      padding: 10px;
    }
    .btn-primary {
      background: var(--primary-color);
      border: none;
    }
    .btn-primary:hover {
      background: #0e5eb5;
    }
    .material-symbols-outlined {
      font-size: 20px;
    }
  </style>
</head>
<body>
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
<div class="d-flex">
  <!-- Sidebar -->
  <aside class="sidebar p-3">
    <h4 class="fw-bold text-dark">Citizen Connect</h4>
    <nav class="mt-4">
      <a href="#" class="active"><span class="material-symbols-outlined">home</span> Dashboard</a>
      <a href="#"><span class="material-symbols-outlined">description</span> Complaints</a>
      <a href="#"><span class="material-symbols-outlined">gavel</span> FIRs</a>
      <a href="#"><span class="material-symbols-outlined">inventory_2</span> Evidence</a>
      <a href="#"><span class="material-symbols-outlined">notifications</span> Alerts</a>
    </nav>
    <div class="mt-auto pt-4">
      <a href="#"><span class="material-symbols-outlined">account_circle</span> Profile</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-grow-1 p-4">
    <header class="mb-4">
      <h1 class="fw-bold text-dark">Account Settings</h1>
      <p class="text-muted">Manage your personal information, contact details, password, and notification preferences.</p>
    </header>

    <div class="container-fluid">
      <!-- Personal Info -->
      <div class="row border-bottom pb-4 mb-4">
        <div class="col-md-3">
          <h5 class="fw-semibold">Personal Information</h5>
          <p class="text-muted small">Use a permanent address where you can receive mail.</p>
        </div>
        <div class="col-md-9">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control">
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Details -->
      <div class="row border-bottom pb-4 mb-4">
        <div class="col-md-3">
          <h5 class="fw-semibold">Contact Details</h5>
          <p class="text-muted small">Update your current residential address.</p>
        </div>
        <div class="col-md-9">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Street Address</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">State / Province</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">ZIP / Postal Code</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
      </div>

      <!-- Password -->
      <div class="row border-bottom pb-4 mb-4">
        <div class="col-md-3">
          <h5 class="fw-semibold">Password</h5>
          <p class="text-muted small">Choose a strong and unique password for better security.</p>
        </div>
        <div class="col-md-9">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Current Password</label>
              <input type="password" class="form-control" placeholder="Enter current password">
            </div>
            <div class="col-md-6">
              <label class="form-label">New Password</label>
              <input type="password" class="form-control" placeholder="Enter new password">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" placeholder="Confirm new password">
            </div>
          </div>
        </div>
      </div>

      <!-- Notifications -->
      <div class="row border-bottom pb-4 mb-4">
        <div class="col-md-3">
          <h5 class="fw-semibold">Notification Preferences</h5>
          <p class="text-muted small">We'll always notify you about important changes.</p>
        </div>
        <div class="col-md-9">
          <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="emailNotify">
            <label class="form-check-label" for="emailNotify">Email Notifications</label>
          </div>
          <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="smsNotify">
            <label class="form-check-label" for="smsNotify">SMS Notifications</label>
          </div>
          <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="appNotify">
            <label class="form-check-label" for="appNotify">In-App Notifications</label>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="d-flex justify-content-end gap-3">
        <button type="button" class="btn btn-light">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Settings</button>
      </div>
    </div>
  </main>
</div>

<!-- Bootstrap + jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
