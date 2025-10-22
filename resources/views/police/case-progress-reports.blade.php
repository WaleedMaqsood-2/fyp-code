<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Case Progress & Reports - Police Module</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #f9fafc;
      color: #333;
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border: none;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      border-radius: 15px;
    }
    .progress-bar {
      background: linear-gradient(90deg, #007bff, #6610f2);
    }
    .export-btn {
      background: linear-gradient(90deg, #007bff, #6610f2);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 8px 15px;
      transition: 0.3s;
    }
    .export-btn:hover {
      opacity: 0.8;
    }
  </style>
</head>
<body>
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Case Progress & Reports</h3>
    <div>
      <button class="export-btn me-2">Export PDF</button>
      <button class="export-btn">Export Word</button>
    </div>
  </div>

  <!-- Case Progress Overview -->
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card p-3">
        <h6>Total Cases</h6>
        <h2 class="fw-bold">152</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h6>Solved Cases</h6>
        <h2 class="fw-bold text-success">98</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h6>Pending Cases</h6>
        <h2 class="fw-bold text-warning">54</h2>
      </div>
    </div>
  </div>

  <!-- Case Progress Bars -->
  <div class="card mt-4 p-4">
    <h5 class="fw-semibold mb-3">Case Progress Overview</h5>
    <div class="mb-3">
      <label class="fw-medium">Robbery Investigation</label>
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: 80%">80%</div>
      </div>
    </div>
    <div class="mb-3">
      <label class="fw-medium">Cyber Fraud Case</label>
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: 60%">60%</div>
      </div>
    </div>
    <div class="mb-3">
      <label class="fw-medium">Kidnapping Case</label>
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: 45%">45%</div>
      </div>
    </div>
  </div>

  <!-- Case Analytics Chart -->
  <div class="card mt-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold">Case Analytics</h5>
      <select class="form-select w-auto">
        <option>Monthly</option>
        <option>Quarterly</option>
        <option>Yearly</option>
      </select>
    </div>
    <canvas id="caseAnalyticsChart" height="100"></canvas>
  </div>

  <!-- Reports Table -->
  <div class="card mt-4 p-4">
    <h5 class="fw-semibold mb-3">Generated Reports</h5>
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Report ID</th>
          <th>Case Title</th>
          <th>Status</th>
          <th>Date Generated</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#RPT001</td>
          <td>Cyber Crime Analysis</td>
          <td><span class="badge bg-success">Approved</span></td>
          <td>2025-10-05</td>
          <td><button class="btn btn-sm btn-primary">View</button></td>
        </tr>
        <tr>
          <td>#RPT002</td>
          <td>Homicide Evidence Review</td>
          <td><span class="badge bg-warning">Pending</span></td>
          <td>2025-10-06</td>
          <td><button class="btn btn-sm btn-primary">View</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
const ctx = document.getElementById('caseAnalyticsChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'Cases Registered',
      data: [12, 19, 8, 15, 20, 25],
      borderWidth: 1,
      backgroundColor: 'rgba(54, 162, 235, 0.6)'
    }, {
      label: 'Cases Solved',
      data: [8, 14, 6, 12, 15, 18],
      borderWidth: 1,
      backgroundColor: 'rgba(75, 192, 192, 0.6)'
    }]
  },
  options: {
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>

</body>
</html>