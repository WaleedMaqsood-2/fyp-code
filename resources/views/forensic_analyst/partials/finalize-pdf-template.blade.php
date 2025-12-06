{{-- resources/views/forensic_analyst/reports/pdf-template.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forensic Report - {{ $complaint->track_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .section-title { background-color: #f0f0f0; padding: 10px; font-weight: bold; border-left: 4px solid #007bff; }
        .evidence-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    margin: 40px 25px;
    position: relative;
}

/* HEADER */
.header {
    text-align: center;
    margin-bottom: 20px;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
}

/* CONFIDENTIAL WATERMARK */
.watermark {
    position: fixed;
    top: 35%;
    left: 20%;
    opacity: 0.08;
    font-size: 120px;
    transform: rotate(-30deg);
    pointer-events: none;
    z-index: -1;
}

/* OFFICIAL SEAL (STAMP) */
.seal {
    position: fixed;
    bottom: 120px;
    right: 40px;
    opacity: 0.18;
    width: 160px;
}

/* SECTION LABELS */
.section-title {
    background: #f0f0f0;
    padding: 6px;
    margin-top: 25px;
    border-left: 4px solid #000;
    font-weight: bold;
}

/* TABLES */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.table th, .table td {
    border: 1px solid #888;
    padding: 8px;
    font-size: 12px;
}

/* PAGE NUMBERS (DOMPDF footer) */
@page {
    margin: 60px 35px;
}
/* This will be handled by dompdf, but we can keep it here for reference */
/* @page {
    @bottom-right {
        content: "Page " counter(page) " of " counter(pages);
        font-size: 10px;
    }
} */

.footer {
    position: fixed;
    bottom: 10px;
    left: 0;
    right: 0;
    font-size: 10px;
    text-align: center;
    color: #555;
}

/* SIGNATURE BLOCK */
.signature-block {
    margin-top: 50px;
    width: 100%;
}
.signature {
    width: 45%;
    display: inline-block;
    text-align: center;
}
.signature-line {
    margin-top: 50px;
    border-top: 1px solid #000;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
}

/* EVIDENCE IMAGES */
.evidence-img {
    margin-top: 10px;
    max-height: 220px;
    border: 1px solid #888;
}

.contact-info {
    border: 1px solid #ccc;
    padding: 10px;
    margin-top: 20px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.contact-info h4 {
    margin: 0;
    font-weight: bold;
}

.contact-info div {
    margin-top: 5px;
}

.contact-info a {
    color: #007bff;
    text-decoration: none;
}

.contact-info span {
    font-weight: 600;
}

.footer-content {
    border-top: 1px solid #e6e6e6;
    padding-top: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10px;
    color: #666;
}

.footer-confidential {
    text-align: center;
    min-width: 220px;
}

.footer-confidential .confidential-text {
    font-weight: 700;
    color: #b30000;
    letter-spacing: 1px;
}

.footer-confidential .report-info {
    font-size: 11px;
    margin-top: 4px;
}



    </style>
</head>
<body>
    <div class="header">
               <h2>Centralized Digital Evidence Management System</h2>
        <h3>Forensic Analysis & AI Intelligence Division</h3>
        <p><strong>Official AI-Generated Forensic Report</strong></p>
    </div>
 <!-- CONFIDENTIAL WATERMARK -->
    <div class="watermark">CONFIDENTIAL</div>

    <!-- OFFICIAL SEAL (YOU CAN REPLACE seal.png) -->
    <img src="{{ public_path('seal.png') }}" class="seal" alt="Official Seal">

    <div class="section">
        <div class="section-title">Case Information</div>
        <table>
            <tr>
                <th width="30%">Case ID:</th>
                <td>{{ $complaint->track_id }}</td>
            </tr>
            <tr>
                <th>Case Title:</th>
                <td>{{ $complaint->subject }}</td>
            </tr>
            <tr>
                <th>Date Created:</th>
                <td>{{ $complaint->created_at->format('F j, Y') }}</td>
            </tr>
        </table>
    </div>

    @if($complaint->summaries->isNotEmpty())
    <div class="section">
        <div class="section-title">Case Summary</div>
        <p style="white-space: pre-wrap;">{{ $complaint->summaries->first()->summary_text }}</p>
    </div>
    @endif

    @php
        $transcript = null;
        foreach ($complaint->transcriptions as $transcription) {
            if ($transcription->verifications->isNotEmpty()) {
                $transcript = $transcription->verifications->first();
                break;
            }
        }
    @endphp
    
    @if($transcript)
    <div class="section">
        <div class="section-title">Verified Transcript</div>
        <p style="white-space: pre-wrap;">{{ $transcript->corrected_text ?? $transcript->transcript }}</p>
    </div>
    @endif

    @if($complaint->media->count() > 0)
    <div class="section">
        <div class="section-title">Evidence Attachments</div>
        <p>Total Evidence Files: {{ $complaint->media->count() }}</p>
        <table>
            <thead>
                <tr>
                    <th>File Type</th>
                    <th>File Name</th>
                    <th>Upload Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaint->media as $media)
                <tr>
                    <td>{{ ucfirst($media->file_type) }}</td>
                    <td>{{ basename($media->file_path) }}</td>
                    <td>{{ $media->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

      <!-- CONTACT INFORMATION -->
    <div class="contact-info">
        <h4>Contact Information</h4>
        <div>Email: <a href="mailto:support@cdems.example">support@cdems.example</a></div>
        <div>Tel: <span>+1 (555) 123‑4567</span></div>
        @if(!empty($case->track_id))
            <div>Track ID: {{ $case->track_id }}</div>
        @endif
    </div>

    <!-- SIGNATURE SECTION -->
    <div class="signature-block">
        <div class="signature">
            <div class="signature-line"></div>
            <p><strong>Forensic Analyst</strong></p>
            <p>{{ auth()->user()->name ?? 'Analyst' }}</p>
        </div>

        <div class="signature">
            <div class="signature-line"></div>
            <p><strong>Authorized Officer</strong></p>
            <p>Digital Evidence Division</p>
        </div>
    </div>
    <!-- FOOTER -->
    <footer class="footer" role="contentinfo">
        <div class="footer-content">
            <div class="footer-confidential">
                <div class="report-info">AI Forensic Report • Auto Generated • {{ date('d M Y') }}</div>
            </div>
        </div>
    </footer>
</body>
</html>