<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Media;
use App\Models\ReportExport;
use App\Models\SummaryVerification;
use App\Models\TranscriptionVerification;
use App\Models\RecentActivities; // Add this
use App\Helpers\NotificationHelper; // Add this
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FinalizeReportController extends Controller
{
    /**
     * Show Finalize Report page
     */
    public function index(Request $request)
    {
        // Get current analyst ID
        $analystId = Auth::id();
        
        // Query for cases that have approved summaries AND verified transcripts
        $query = Complaint::whereHas('PendingSummary', function($q) {
                $q->where('status', 'approved');
            })
            ->whereHas('transcriptions.verifications', function($q) use ($analystId) {
                $q->where('analyst_id', $analystId)
                  ->where('approved', 1);
            })
            ->with([
                'summaries' => function($q) {
                    $q->where('status', 'approved')
                      ->latest();
                },
                'transcriptions.verifications' => function($q) use ($analystId) {
                    $q->where('analyst_id', $analystId)
                      ->where('approved', 1);
                },
                'media' => function($q) {
                    $q->whereIn('file_type', ['image', 'video', 'audio']);
                },
                'reportExports' => function($q) {
                    $q->latest();
                }
            ])
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('track_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
            
            // ✅ **NEW: Report search notification**
            NotificationHelper::createForUser(
                $analystId,
                "Report Search",
                "You searched for reports with keyword: '{$search}'",
                'info',
                route('forensic.finalize')
            );
        }

        // Status filter (exported vs not exported)
        if ($request->has('status')) {
            if ($request->status === 'exported') {
                $query->whereHas('reportExports');
            } elseif ($request->status === 'not_exported') {
                $query->whereDoesntHave('reportExports');
            }
        }

        $cases = $query->paginate(10)->withQueryString();

        // Get statistics
        $stats = [
            'total' => $cases->total(),
            'exported' => Complaint::whereHas('reportExports')->count(),
            'pending_export' => Complaint::whereDoesntHave('reportExports')
                ->whereHas('PendingSummary', function($q) {
                    $q->where('status', 'approved');
                })
                ->count(),
        ];

        // ✅ **NEW: Finalize report dashboard access notification**
        if (!session()->has('finalize_report_accessed')) {
            NotificationHelper::createForUser(
                $analystId,
                "Final Report Dashboard",
                "You accessed the final report generation dashboard",
                'info',
                route('forensic.finalize')
            );
            session(['finalize_report_accessed' => true]);
        }

        return view('forensic_analyst.finalize-report', compact('cases', 'stats'));
    }

    /**
     * Generate PDF report
     */
    public function exportPDF($id)
    {
        $complaint = Complaint::with([
            'summaries' => function($q) {
                $q->where('status', 'approved')
                  ->latest();
            },
            'transcriptions.verifications' => function($q) {
                $q->where('approved', 1)
                  ->latest();
            },
            'media' => function($q) {
                $q->whereIn('file_type', ['image', 'video', 'audio']);
            },
            'reportExports'
        ])->findOrFail($id);

        // Check if already exported
        $alreadyExported = $complaint->reportExports->isNotEmpty();
        
        // Generate PDF
        $pdf = Pdf::loadView('forensic_analyst.partials.finalize-pdf-template', compact('complaint'));
        
        // Generate filename
        $filename = 'report_' . $complaint->track_id . '_' . date('Ymd_His') . '.pdf';
        $filePath = 'reports/' . $filename;
        
        // Save PDF to storage
        Storage::disk('public')->put($filePath, $pdf->output());
        
        // Create report export record
        $export = ReportExport::create([
            'review_id' => $id,
            'file_path' => $filePath,
            'exported_at' => now(),
        ]);

        // ✅ **NEW: Report generation notification**
        $exportType = $alreadyExported ? 're-exported' : 'generated';
        NotificationHelper::createForUser(
            Auth::id(),
            "Forensic Report {$exportType}",
            "Forensic report {$exportType} for case #{$complaint->track_id}",
            'success',
            route('forensic.finalize')
        );

        // ✅ **NEW: Notify admin about report generation
        $admins = \App\Models\User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "Forensic Report Generated",
                "Forensic analyst {$exportType} final report for case #{$complaint->track_id}",
                'info',
                route('admin.complaints.show', $id)
            );
        }

        // ✅ **NEW: Notify police officer about report generation
        if ($complaint->assigned_to) {
            NotificationHelper::createForUser(
                $complaint->assigned_to,
                "Forensic Report Ready",
                "Final forensic report ready for case #{$complaint->track_id}",
                'info',
                route('police.cases.show', $id)
            );
        }

        // ✅ **NEW: Notify complaint owner about report completion
        if ($complaint->user_id) {
            NotificationHelper::createForUser(
                $complaint->user_id,
                "Forensic Analysis Complete",
                "Forensic analysis completed for your case #{$complaint->track_id}. Report is ready.",
                'success',
                route('public.complaints.track', ['track_id' => $complaint->track_id])
            );
        }

        // Log activity
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Final report exported for case #' . $complaint->track_id,
        ]);

        return back()->with('success', 'PDF Report generated successfully: ' . $filename . '. You can download it from the reports list.');
    }

    /**
     * View generated reports
     */
    public function generatedReports(Request $request)
    {
        $query = ReportExport::with('complaint')
            ->orderBy('exported_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('complaint', function($cq) use ($search) {
                    $cq->where('track_id', 'like', "%{$search}%")
                       ->orWhere('subject', 'like', "%{$search}%");
                });
            });
        }

        $reports = $query->paginate(15)->withQueryString();

        return view('forensic_analyst.partials.finalize-generated-reports', compact('reports'));
    }

    /**
     * View single report details
     */
    public function viewReport($exportId)
    {
        $report = ReportExport::with(['complaint', 'complaint.summaries', 'complaint.media'])
            ->findOrFail($exportId);

        // ✅ **NEW: Report view notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Report Details Viewed",
            "You viewed details for report #{$exportId}",
            'info',
            route('forensic.finalize.view', $exportId)
        );

        return view('forensic_analyst.partials.finalize-report-details', compact('report'));
    }

    public function checkFile($exportId)
    {
        $report = ReportExport::findOrFail($exportId);
        
        return response()->json([
            'exists' => Storage::disk('public')->exists($report->file_path),
            'size' => $report->fileExists() ? $report->fileSize : 'N/A',
            'path' => $report->file_path
        ]);
    }

    /**
     * Download report
     */
    public function downloadReport($exportId)
    {
        $report = ReportExport::findOrFail($exportId);
        
        if (!Storage::disk('public')->exists($report->file_path)) {
            return back()->with('error', 'Report file not found.');
        }

        // ✅ **NEW: Report download notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Report Downloaded",
            "You downloaded forensic report #{$exportId}",
            'success',
            route('forensic.finalize')
        );

        // Build the full local path to the file stored in storage/app/public
        $fullPath = storage_path('app/public/' . $report->file_path);

        // Use the response helper to download the file
        return response()->download($fullPath, basename($report->file_path));
    }

    public function deleteReport($exportId)
    {
        try {
            $report = ReportExport::findOrFail($exportId);
            $reportPath = $report->file_path;
            
            // Delete file from storage
            if (Storage::disk('public')->exists($report->file_path)) {
                Storage::disk('public')->delete($report->file_path);
            }
            
            // Delete record
            $report->delete();

            // ✅ **NEW: Report deletion notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Report Deleted",
                "You deleted forensic report #{$exportId}",
                'warning',
                route('forensic.finalize')
            );

            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Report deleted successfully.'
                ]);
            }
            
            return back()->with('success', 'Report deleted successfully.');
            
        } catch (\Exception $e) {
            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error deleting report: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error deleting report: ' . $e->getMessage());
        }
    }
}