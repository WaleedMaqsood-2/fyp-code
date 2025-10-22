@extends('police.layouts.main')

@section('title', 'Dashboard')

@php
    if (!auth()->check()) {
        header('Location: ' . route('login'));
        exit;
    }
@endphp

@section('content')
<div class="container-fluid py-4">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h1 class="fw-bold fs-3 text-dark">Assigned Cases</h1>
      <p class="text-secondary mb-0">Overview of your active cases.</p>
    </div>
    <button class="btn btn-primary fw-bold px-4 shadow-sm">View All</button>
  </div>

  <!-- ASSIGNED CASES TABLE -->
  <div class="card shadow-sm border-0 mb-5 rounded-4">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Case ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#12345</td>
            <td>Robbery at Main Street</td>
            <td><span class="badge bg-warning text-dark">In Progress</span></td>
            <td><span class="badge bg-danger">High</span></td>
            <td>2024-05-20</td>
            <td class="text-primary fw-bold">View Details</td>
          </tr>
          <tr>
            <td>#12346</td>
            <td>Vandalism at City Park</td>
            <td><span class="badge bg-success">Open</span></td>
            <td><span class="badge bg-warning text-dark">Medium</span></td>
            <td>2024-05-19</td>
            <td class="text-primary fw-bold">View Details</td>
          </tr>
          <tr>
            <td>#12347</td>
            <td>Missing Person</td>
            <td><span class="badge bg-success">Open</span></td>
            <td><span class="badge bg-danger">High</span></td>
            <td>2024-05-18</td>
            <td class="text-primary fw-bold">View Details</td>
          </tr>
          <tr>
            <td>#12348</td>
            <td>Fraud Investigation</td>
            <td><span class="badge bg-secondary">Closed</span></td>
            <td><span class="badge bg-success">Low</span></td>
            <td>2024-05-17</td>
            <td class="text-primary fw-bold">View Details</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- NEW COMPLAINTS -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold fs-4 mb-1">New Complaints</h2>
      <p class="text-secondary small mb-0">Review and take action on complaints from the public.</p>
    </div>
  </div>

  <div class="row g-4 mb-5 pb-5">
    <div class="col-md-6 col-lg-4">
      <div class="card p-3 shadow-sm border-0 rounded-4">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="fw-bold mb-0">Complaint #C-5678</h6>
            <small class="text-secondary">Noise Complaint at Elm Street</small>
          </div>
          <small class="text-secondary">2024-05-21</small>
        </div>
        <p class="mt-3 small text-muted">Loud music reported from apartment 3B. Multiple reports received.</p>
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-success btn-sm flex-fill fw-bold shadow-sm">Accept</button>
          <button class="btn btn-danger btn-sm flex-fill fw-bold shadow-sm">Reject</button>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card p-3 shadow-sm border-0 rounded-4">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="fw-bold mb-0">Complaint #C-5679</h6>
            <small class="text-secondary">Illegal Parking</small>
          </div>
          <small class="text-secondary">2024-05-21</small>
        </div>
        <p class="mt-3 small text-muted">A blue sedan is blocking a fire hydrant on Oak Avenue.</p>
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-success btn-sm flex-fill fw-bold shadow-sm">Accept</button>
          <button class="btn btn-danger btn-sm flex-fill fw-bold shadow-sm">Reject</button>
        </div>
      </div>
    </div>
  </div>

  <!-- CHARTS SECTION -->
  <div class="row g-4">
    <!-- BAR CHART -->
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-bold mb-3">Cases by Type</h5>
        <canvas id="casesByTypeChart" height="120"></canvas>
      </div>
    </div>

    <!-- DOUGHNUT CHART -->
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-bold mb-3">Case Status</h5>
        <canvas id="caseStatusChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

  // ==== BAR CHART ====
  const ctx1 = document.getElementById('casesByTypeChart');
  new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: ['Robbery','Assault','Theft','Fraud','Other'],
      datasets: [{
        label: 'Cases',
        data: [50, 9, 70, 4, 6],
        backgroundColor: ['#007bff','#6610f2','#6f42c1','#e83e8c','#fd7e14'],
        borderRadius: 10
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true },
        x: { grid: { display: false } },
      }
    }
  });

  // ==== DOUGHNUT CHART ====
  const ctx2 = document.getElementById('caseStatusChart');
  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: ['Pending', 'Under Analysis', 'Solved', 'Closed'],
      datasets: [{
        data: [18, 36, 41, 29],
        backgroundColor: ['#ffc107','#0dcaf0','#198754','#6c757d'],
        hoverOffset: 4,
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      cutout: '70%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 15, color: '#333' } }
      }
    }
  });
});
</script>

