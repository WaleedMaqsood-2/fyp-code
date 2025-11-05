{{-- @extends('police.layouts.main')
@section('title','Forward Case to Forensic Analyst - Police Module')
<link rel="stylesheet" href="{{ asset('css/police/forward-case.css') }}"> --}}

{{-- @section('content')

<div class="container py-5">
  <h2 class="fw-bold text-primary mb-4">Forward Case to Forensic Analyst</h2>

  <!-- Case Selection -->
  <div class="card mb-5">
    <div class="card-body">
      <h5 class="mb-3 text-primary"><span class="material-icons align-middle">folder_shared</span> Select Case to Forward</h5>
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Case ID</th>
            <th>Title</th>
            <th>Officer</th>
            <th>Date Filed</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#P-103</td>
            <td>Burglary at Main Street</td>
            <td>Officer Ali</td>
            <td>08 Oct 2025</td>
            <td><span class="status-badge status-pending">Pending</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#forwardModal">
                <span class="material-icons fs-6">send</span> Forward
              </button>
            </td>
          </tr>
          <tr>
            <td>#P-104</td>
            <td>Cyber Fraud Case</td>
            <td>Officer Sara</td>
            <td>07 Oct 2025</td>
            <td><span class="status-badge status-progress">In Progress</span></td>
            <td>
              <button class="btn btn-sm btn-outline-secondary" disabled>
                <span class="material-icons fs-6">check_circle</span> Sent
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Forward Tracking -->
  <div class="card">
    <div class="card-body">
      <h5 class="mb-3 text-primary"><span class="material-icons align-middle">track_changes</span> Forwarded Cases Tracking</h5>
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Case ID</th>
            <th>Forensic Analyst</th>
            <th>Forwarded On</th>
            <th>Remarks</th>
            <th>Analysis Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#P-100</td>
            <td>Analyst Hamza</td>
            <td>02 Oct 2025</td>
            <td>Need deep face match verification</td>
            <td><span class="status-badge status-complete">Completed</span></td>
          </tr>
          <tr>
            <td>#P-102</td>
            <td>Analyst Ayesha</td>
            <td>05 Oct 2025</td>
            <td>Verify voice match and digital evidence</td>
            <td><span class="status-badge status-progress">In Progress</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Forward Modal -->
<div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="forwardModalLabel"><span class="material-icons align-middle">send</span> Forward Case</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Select Forensic Analyst</label>
            <select class="form-select">
              <option>Hamza Khan</option>
              <option>Ayesha Ahmed</option>
              <option>Bilal Qureshi</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Add Remarks / Instructions</label>
            <textarea class="form-control" rows="3" placeholder="Describe what to analyze..."></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success">
              <span class="material-icons">check_circle</span> Confirm Forward
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection --}}

@extends('police.layouts.main')
@section('title','Forward Case to Forensic Analyst - Police Module')
<link rel="stylesheet" href="{{ asset('css/police/forward-case.css') }}">

@section('content')
<div class="container py-5">
  <h2 class="fw-bold text-primary mb-4">Forward Case to Forensic Analyst</h2>

  <!-- Case Selection -->
  <div class="card mb-5">
    <div class="card-body">
      <h5 class="mb-3 text-primary"><span class="material-icons align-middle">folder_shared</span> Select Case to Forward</h5>
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Case ID</th>
                 <th>Track ID</th>
            <th>Title</th>
            <th>Officer</th>
            <th>Date Filed</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        {{-- <tbody>
          @foreach ($cases as $case )
          <tr>
            <td>#{{ $case->id }}</td>
            <td>#{{ $case->track_id }}</td>
            <td>{{ $case->subject }}</td>
            <td>{{ $case->name}}</td>
            <td>{{ $case->created_at->format('d M Y') }}</td>
            <td><span class="status-badge status-{{ strtolower($case->status) }}">{{ ucfirst($case->status) }}</span></td>
            <td>
              @if($case->status === 'Pending')
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#forwardModal" data-id="{{ $case->id }}">
                <span class="material-icons fs-6">send</span> Forward
              </button>
              @else
              <button class="btn btn-sm btn-outline-secondary" disabled>
                <span class="material-icons fs-6">check_circle</span> Sent
              </button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody> --}}
        <tbody>
  @foreach ($cases as $case)
  <tr>
    <td>#{{ $case->id }}</td>
    <td>#{{ $case->track_id }}</td>
    <td>{{ $case->subject }}</td>

    {{-- Officer Name from users table --}}
    <td>{{ $case->officer?->name ?? 'Unknown Officer' }}</td>

    <td>{{ $case->created_at->format('d M Y') }}</td>

    {{-- Status from complaint_status_logs table --}}
    <td>
      @php
        $status = $case->latestStatus?->status ?? 'recieved';
      @endphp
      <span class="status-badge status-{{ strtolower($status) }}">{{ ucfirst($status) }}</span>
    </td>

    {{-- Action button --}}
    <td>
      @if($status === 'recieved')
      <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#forwardModal" data-id="{{ $case->id }}">
        <span class="material-icons fs-6">send</span> Forward
      </button>
      @else
      <button class="btn btn-sm btn-outline-secondary" disabled>
        <span class="material-icons fs-6">check_circle</span> Sent
      </button>
      @endif
    </td>
  </tr>
  @endforeach
</tbody>

      </table>
    </div>
  </div>

  <!-- Forward Tracking -->
  <div class="card">
    <div class="card-body">
      <h5 class="mb-3 text-primary"><span class="material-icons align-middle">track_changes</span> Forwarded Cases Tracking</h5>
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Case ID</th>
            <th>Forensic Analyst</th>
            <th>Forwarded On</th>
            <th>Remarks</th>
            <th>Analysis Status</th>
          </tr>
        </thead>
        <tbody>
{{--           
          @foreach ($forwardedCases as $item)
          <tr>
            <td>#{{ $item->case_id }}</td>
            <td>{{ $item->analyst_name }}</td>
            <td>{{ $item->created_at->format('d M Y') }}</td>
            <td>{{ $item->remarks }}</td>
            <td><span class="status-badge status-{{ strtolower($item->analysis_status) }}">{{ ucfirst($item->analysis_status) }}</span></td>
          </tr>
          @endforeach --}}
          @foreach ($forwardedCases as $item)
<tr>
  <td>#{{ $item['case_id'] }}</td>
  <td>{{ $item['analyst_name'] }}</td>
  <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y') }}</td>
  <td>{{ $item['remarks'] }}</td>
  <td><span class="status-badge status-{{ strtolower($item['analysis_status']) }}">{{ ucfirst($item['analysis_status']) }}</span></td>
</tr>
@endforeach

        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Forward Modal -->
<div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="forwardModalLabel"><span class="material-icons align-middle">send</span> Forward Case</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="forwardForm" method="POST" action="{{ route('police.forward.cases') }}">
          @csrf
          <input type="hidden" name="case_id" id="case_id">

          <div class="mb-3">
            <label class="form-label">Select Forensic Analyst</label>
            <select class="form-select" name="analyst_id" required>
              <option value="">Select Forensic Analyst</option>
              @foreach ($analysts as $analyst)
              <option value="{{ $analyst->id }}">{{ $analyst->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Add Remarks / Instructions</label>
            <textarea class="form-control" name="remarks" rows="3" placeholder="Describe what to analyze..." required></textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success">
              <span class="material-icons">check_circle</span> Confirm Forward
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script>
document.addEventListener("DOMContentLoaded", function() {
  const modal = document.getElementById('forwardModal');
  modal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const caseId = button.getAttribute('data-id');
    document.getElementById('case_id').value = caseId;
  });
});
</script>

@endpush


{{-- mery pass ya table ha ma chata hn k is ma kuch changing hn is ma forwarded_to foriegn key dalni ha jis ma forinsice analysts show ho k kis ko forwrd ki ha r dosri user_id k kis user ny forward ki ha us k bd status ki data type ko chnage kr k enum krna ha jis ma option save hn jsy forwarded not forwarded is trah k 2 ya 3 related option save hn ya phr agr is k ly ham alag tabe bna lyn kis karen yahn pa ? --}}