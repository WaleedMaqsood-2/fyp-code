@extends('layouts.master')
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
    

<style>
    :root {
        --primary-color: #1173d4;
    }

    body {
        font-family: 'Public Sans', 'Noto Sans', sans-serif;
        background-color: #1a1a1a;
        color: #fff;
    }

    a {
        text-decoration: none;
    }

    a:hover {
        color: #fff;
    }

    .navbar-custom {
        border-bottom: 1px solid #2c2c2c;
    }

    .nav-link.active {
        color: var(--primary-color) !important;
        border-bottom: 2px solid var(--primary-color);
    }

    .card-custom {
        background-color: #2c2c2c;
        border: none;
    }

    .btn-custom {
        background-color: #2c2c2c;
        color: #fff;
        border: 1px solid #2c2c2c;
    }

    .btn-custom:hover {
        background-color: #3a3a3a;
        color: #fff;
    }

    .material-symbols-outlined {
        font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 48
        ;
    }
</style>
</head>
<body>

<div class="d-flex flex-column min-vh-100">
   

    <!-- Main Content -->
    <main class="flex-grow-1 container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">AI Usage Monitoring</h1>
            <div class="d-flex gap-2">
                <div class="btn-group">
                    <button class="btn btn-secondary btn-sm active">Last 30 Days</button>
                    <button class="btn btn-secondary btn-sm">Last 3 Months</button>
                    <button class="btn btn-secondary btn-sm">Last Year</button>
                </div>
                <button class="btn btn-custom btn-sm d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined">download</span> Export
                </button>
            </div>
        </div>

        <!-- Speech-to-Text Section -->
        <section class="mb-5">
            <h2 class="fw-bold mb-3">Speech-to-Text Transcription</h2>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Total Transcription Duration</h6><p class="fs-3 fw-bold">12,450 mins</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Total Transcriptions</h6><p class="fs-3 fw-bold">876</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Avg. Transcription Time</h6><p class="fs-3 fw-bold">14m 12s</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Accuracy Rate</h6><p class="fs-3 fw-bold">95%</p></div></div>
            </div>
            <div class="card card-custom p-3 mt-3">
                <h6 class="fw-semibold mb-3">Transcription Duration Over Time</h6>
                <!-- Chart instead of image -->
                <canvas id="transcriptionChart" style="width:100%; height:300px;"></canvas>
            </div>
        </section>

        <!-- Face Detection Section -->
        <section class="mb-5">
            <h2 class="fw-bold mb-3">Face Detection</h2>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Images Processed</h6><p class="fs-3 fw-bold">1,523</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Faces Detected</h6><p class="fs-3 fw-bold">4,892</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Avg. Faces per Image</h6><p class="fs-3 fw-bold">3.2</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">False Positive Rate</h6><p class="fs-3 fw-bold">2%</p></div></div>
            </div>
            <div class="card card-custom p-3 mt-3">
                <h6 class="fw-semibold mb-3">Number of Faces Detected by Day</h6>
                <!-- Chart instead of image -->
                <canvas id="facesChart" style="width:100%; height:300px;"></canvas>
            </div>
        </section>

        <!-- Text Summarization Section -->
        <section class="mb-5">
            <h2 class="fw-bold mb-3">Text Summarization</h2>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Total Summaries</h6><p class="fs-3 fw-bold">452</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Avg. Original Length</h6><p class="fs-3 fw-bold">1,200 words</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Avg. Summary Length</h6><p class="fs-3 fw-bold">150 words</p></div></div>
                <div class="col-12 col-sm-6 col-lg-3"><div class="card card-custom p-3 text-center"><h6 class="text-secondary">Avg. Reduction Rate</h6><p class="fs-3 fw-bold">87.5%</p></div></div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <div class="card card-custom p-3">
                        <h6 class="fw-semibold mb-3">Summary Length Distribution</h6>
                        <!-- Chart instead of image -->
                        <canvas id="summaryDistributionChart" style="width:100%; height:250px;"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-custom p-3">
                        <h6 class="fw-semibold mb-3">Summaries Generated Over Time</h6>
                        <!-- Chart instead of image -->
                        <canvas id="summariesOverTimeChart" style="width:100%; height:250px;"></canvas>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>
@endsection

@php
  $searchAction = route('admin.ai.search');
  $searchPlaceholder = 'Search AI Usage...';
@endphp

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transcriptionCtx = document.getElementById('transcriptionChart').getContext('2d');
    new Chart(transcriptionCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Transcription Duration (mins)',
                data: [2000, 2500, 2200, 2700, 3000, 3100],
                backgroundColor: 'rgba(17, 115, 212,0.2)',
                borderColor: 'rgba(17, 115, 212,1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        }
    });

    const facesCtx = document.getElementById('facesChart').getContext('2d');
    new Chart(facesCtx, {
        type: 'bar',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [{
                label: 'Faces Detected',
                data: [500, 600, 700, 650, 800, 550, 700],
                backgroundColor: 'rgba(17, 115, 212, 0.7)'
            }]
        }
    });

    const summaryDistCtx = document.getElementById('summaryDistributionChart').getContext('2d');
    new Chart(summaryDistCtx, {
        type: 'pie',
        data: {
            labels: ['100-200 words', '201-400', '401-600', '601+'],
            datasets: [{
                data: [40, 30, 20, 10],
                backgroundColor: ['#1173d4','#0d6efd','#6c757d','#198754']
            }]
        }
    });

    const summariesOverTimeCtx = document.getElementById('summariesOverTimeChart').getContext('2d');
    new Chart(summariesOverTimeCtx, {
        type: 'line',
        data: {
            labels: ['Week 1','Week 2','Week 3','Week 4'],
            datasets: [{
                label: 'Summaries Generated',
                data: [50, 120, 80, 200],
                backgroundColor: 'rgba(17, 115, 212,0.2)',
                borderColor: 'rgba(17, 115, 212,1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        }
    });
});
</script>


