<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forensic Report - {{ $case->track_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .section { margin-bottom: 15px; page-break-inside: avoid; }
        .section-title { background-color: #f0f0f0; padding: 8px; font-weight: bold; border-left: 4px solid #007bff; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
        .page-break { page-break-before: always; }
        .signature { margin-top: 30px; border-top: 1px solid #333; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>FORENSIC ANALYSIS REPORT</h2>
        <h4>Case ID: {{ $case->track_id }}</h4>
        <p>Generated on: {{ date('F j, Y H:i:s') }}</p>
    </div>

    <div class="section">
        <div class="section-title">1. Case Information</div>
        <table>
            <tr>
                <th width="30%">Case ID:</th>
                <td>{{ $case->id }}</td>
                <th width="30%">Track ID:</th>
                <td>{{ $case->track_id }}</td>
            </tr>
            <tr>
                <th>Case Title:</th>
                <td>{{ $case->subject }}</td>
                <th>Incident Type:</th>
                <td>{{ $case->incident_type ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Filed By:</th>
                <td>{{ $case->user->name ?? 'Unknown' }}</td>
                <th>Case Status:</th>
                <td>{{ ucfirst($case->status) }}</td>
            </tr>
            <tr>
                <th>Location:</th>
                <td>{{ $case->location ?? 'N/A' }}</td>
                <th>Date Created:</th>
                <td>{{ $case->created_at->format('F j, Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. Evidence Summary</div>
        <p>Total Evidence Files: {{ $case->media->count() }}</p>
        @if($case->media->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>File Type</th>
                    <th>File Name</th>
                    <th>Upload Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($case->media as $index => $media)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ ucfirst($media->file_type) }}</td>
                    <td>{{ basename($media->file_path) }}</td>
                    <td>{{ $media->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>No evidence files uploaded.</p>
        @endif
    </div>

    @if($verifiedTranscription)
    <div class="section">
        <div class="section-title">3. Verified Transcription</div>
        <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
            <p style="white-space: pre-wrap; font-size: 11px;">{{ $verifiedTranscription }}</p>
        </div>
    </div>
    @endif

    @if($case->latestForensicReview)
    <div class="section">
        <div class="section-title">4. Forensic Analyst Review</div>
        <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
            <p style="white-space: pre-wrap; font-size: 11px;">{{ $case->latestForensicReview->findings }}</p>
            <p><strong>Analyst:</strong> {{ $case->latestForensicReview->analyst->name ?? 'Forensic Analyst' }}</p>
            <p><strong>Date:</strong> {{ $case->latestForensicReview->created_at->format('F j, Y H:i') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($case->latestForensicReview->status) }}</p>
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">5. Case Summary</div>
        <table>
            <tr>
                <th width="30%">Evidence Status</th>
                <td>{{ $case->media->count() > 0 ? 'Available' : 'Not Available' }}</td>
            </tr>
            <tr>
                <th>Transcription Status</th>
                <td>{{ $verifiedTranscription ? 'Verified' : ($case->transcriptions->count() > 0 ? 'Available' : 'Pending') }}</td>
            </tr>
            <tr>
                <th>Analysis Status</th>
                <td>{{ $case->latestForensicReview ? 'Completed' : 'Pending' }}</td>
            </tr>
            <tr>
                <th>Overall Assessment</th>
                <td>{{ ucfirst($case->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="signature">
        <table>
            <tr>
                <td width="50%">
                    <p><strong>Generated By:</strong></p>
                    <p>______________________________</p>
                    <p>Forensic Analysis System</p>
                    <p>Date: {{ date('F j, Y') }}</p>
                </td>
                <td width="50%">
                    <p><strong>Approved By:</strong></p>
                    <p>______________________________</p>
                    <p>Forensic Department Head</p>
                    <p>Date: _________________________</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>--- End of Report ---</p>
        <p>Confidential Document - For Official Use Only</p>
        <p>Generated by Forensic Analysis System | Page 1 of 1</p>
    </div>
</body>
</html>