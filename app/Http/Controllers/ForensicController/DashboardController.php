<?php

namespace App\Http\Controllers\ForensicController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintStatusLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Add this method in your existing DashboardController
private function sendDashboardNotifications($analystId)
{
    $totalCases = ComplaintStatusLog::where('forwarded_to', $analystId)->count();
    $pendingCases = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->where('status', 'forwarded')
        ->count();
    
    // ✅ **NEW: Daily dashboard summary notification**
    if (!session()->has('dashboard_summary_today')) {
        $message = "Good morning! You have {$totalCases} total cases, {$pendingCases} pending.";
        if ($pendingCases > 5) {
            $message .= " ⚠️ High workload alert!";
        }
        
        NotificationHelper::createForUser(
            $analystId,
            "Daily Case Summary",
            $message,
            'info',
            route('forensic.dashboard')
        );
        
        session(['dashboard_summary_today' => true]);
    }

    // ✅ **NEW: High priority case alert**
    $highPriority = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->whereHas('complaint', fn($q) => $q->where('severity', 'high'))
        ->where('status', 'forwarded')
        ->count();
    
    if ($highPriority > 0 && !session()->has('high_priority_alert')) {
        NotificationHelper::createForUser(
            $analystId,
            "⚠️ High Priority Cases",
            "You have {$highPriority} high priority cases pending review",
            'danger',
            route('forensic.assigned-cases')
        );
        session(['high_priority_alert' => true]);
    }
}


    
    // ... rest of your existing dashboard code ...
    public function dashboard(Request $request)
    {

    $analystId = Auth::id();
    $this->sendDashboardNotifications($analystId);
    /*====================================================
     | TOTAL CASES (All-time)
     ====================================================*/
    $totalCases = ComplaintStatusLog::where('forwarded_to', $analystId)->count();


    /*====================================================
     | CURRENT MONTH – ASSIGNED CASES
     ====================================================*/
    $currentMonthCases = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])
        ->count();

    /*====================================================
     | PREVIOUS MONTH – ASSIGNED CASES
     ====================================================*/
    $previousMonthCases = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])
        ->count();

    /*====================================================
     | PERCENTAGE CHANGE (Assigned Cases)
     ====================================================*/
    $percentageChange = $previousMonthCases == 0 
        ? 0 
        : (($currentMonthCases - $previousMonthCases) / $previousMonthCases) * 100;



    /*====================================================
     | PENDING CASES
     ====================================================*/
    $pendingCases = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->where('status', 'forwarded')
        ->count();

    $pendingMonth = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->where('status', 'forwarded')
        ->whereBetween('changed_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])
        ->count();

    $percentagePending = $pendingCases == 0 
        ? 0 
        : (($pendingMonth - $pendingCases) / $pendingCases) * 100;



    /*====================================================
     | COMPLETED CASES
     ====================================================*/
    $completedCases = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->where('status', 'completed')
        ->count();

    $currentMonthCompleted = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->where('status', 'completed')
        ->whereBetween('changed_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])
        ->count();

    $percentageChangeCompleted = $completedCases == 0
        ? 0
        : (($currentMonthCompleted - $completedCases) / $completedCases) * 100;



    /*====================================================
     | HIGH PRIORITY CASES
     ====================================================*/
    $highPriority = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->whereHas('complaint', fn($q) => $q->where('severity', 'high'))
        ->count();

    $currentMonthHighPriority = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])
        ->whereHas('complaint', fn($q) => $q->where('severity', 'high'))
        ->count();

    $percentageHighPriority = $highPriority == 0
        ? 0
        : (($currentMonthHighPriority - $highPriority) / $highPriority) * 100;



    /*====================================================
     | UNDER REVIEW (from Complaint table)
     ====================================================*/
    $underReview = Complaint::where('assigned_to', $analystId)
        ->where('status', 'under_review')
        ->count();




        /*====================================================
 | CHART DATA (Daily stats for current month)
 ====================================================*/
$daysInMonth = now()->daysInMonth;
$chartLabels = [];
$assignedData = [];
$completedData = [];
$pendingData = [];
$highPriorityData = [];

for ($day = 1; $day <= $daysInMonth; $day++) {
    $date = now()->startOfMonth()->addDays($day - 1);
    $chartLabels[] = $date->format('d M');

    // Assigned (forwarded cases)
    $assignedData[] = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->whereDate('created_at', $date)
        ->count();

    // High priority
    $highPriorityData[] = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->whereDate('created_at', $date)
        ->whereHas('complaint', fn($q) => $q->where('severity', 'high'))
        ->count();

    // Pending
    $pendingData[] = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->where('status', 'forwarded')
        ->whereDate('created_at', $date)
        ->count();

    // Completed (changed_at)
    $completedData[] = ComplaintStatusLog::where('forwarded_to', $analystId)
        ->where('status', 'completed')
        ->whereDate('changed_at', $date)
        ->count();
}



$filterSeverity = $request->input('severity'); // 'high', 'medium', 'low'
$sortBy = $request->input('sort', 'created_at'); // default sorting
$sortOrder = $request->input('order', 'desc');

$query = Complaint::with(['officer', 'media', 'latestStatus'])
    ->whereHas('latestStatus', function($q) use ($analystId) {
        $q->where('forwarded_to', $analystId)
          ->where('status', 'forwarded');
    });

// Apply severity filter if selected
if ($filterSeverity) {
    $query->where('severity', $filterSeverity);
}

// Apply sorting
$query->orderBy($sortBy, $sortOrder);

// Limit 5 cases
$forwardedCases = $query->take(5)->get();




        // Selected year (default = current year)
    $selectedYear = $request->input('year', Carbon::now()->year);

    // Monthly forwarded cases for this analyst
    $forwardedData = ComplaintStatusLog::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->where('forwarded_to', $analystId)
        ->whereYear('created_at', $selectedYear)
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

    // Prepare 12 months data
    $months = [];
    $forwardedCounts = [];

    foreach (range(1, 12) as $m) {
        $months[] = Carbon::create()->month($m)->format('M');
        $forwardedCounts[] = $forwardedData[$m] ?? 0;
    }

    // Year dropdown (last 5 years)
    $years = range(Carbon::now()->year, Carbon::now()->year - 4);
    /*====================================================
     | RETURN VIEW
     ====================================================*/
  

        $analystId = Auth::id();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // ==================== DASHBOARD METRICS ====================
        $metrics = $this->getDashboardMetrics($analystId, $currentMonth, $currentYear);

        // ==================== CHART DATA ====================
        $chartData = $this->getChartData($analystId, $currentMonth, $currentYear);

        // ==================== CASE STATISTICS ====================
        $caseStats = $this->getCaseStatistics($analystId, $currentMonth);

        // ==================== FORWARDED CASES LIST ====================
        $forwardedCases = $this->getForwardedCases($analystId, $request);

        // ==================== YEARLY ANALYTICS ====================
        $yearlyAnalytics = $this->getYearlyAnalytics($analystId, $request);

        // ==================== SEVERITY DISTRIBUTION ====================
        $severityDistribution = $this->getSeverityDistribution($analystId);

        // ==================== PERFORMANCE METRICS ====================
        $performanceMetrics = $this->getPerformanceMetrics($analystId, $currentMonth);

        // ==================== RETURN VIEW ====================
         

        return view('forensic_analyst.dashboard', [
                   'totalCases'                => $totalCases,
        'currentMonthCases'         => $currentMonthCases,
        'previousMonthCases'        => $previousMonthCases,
        'percentageChange'          => round($percentageChange, 1),

        'pendingCases'              => $pendingCases,
        'pendingMonth'              => $pendingMonth,
        'percentagePending'         => round($percentagePending, 1),

        'completedCases'            => $completedCases,
        'currentMonthCompleted'     => $currentMonthCompleted,
        'percentageChangeCompleted' => round($percentageChangeCompleted, 1),

        'highPriority'              => $highPriority,
        'currentMonthHighPriority'  => $currentMonthHighPriority,
        'percentageHighPriority'    => round($percentageHighPriority, 1),

        'underReview'               => $underReview,


         'chartLabels'        => $chartLabels,
    'assignedData'       => $assignedData,
    'completedData'      => $completedData,
    'pendingData'        => $pendingData,
    'highPriorityData'   => $highPriorityData,

    'forwardedCases'=>$forwardedCases,

    'months' => $months,
    'forwardedCounts' => $forwardedCounts,
    'years' => $years,
    'selectedYear' => $selectedYear,
            // Dashboard Metrics
            'metrics' => $metrics,
            
            // Chart Data
            'chartData' => $chartData,
            
            // Case Statistics
            'caseStats' => $caseStats,
            
            // Cases List
            'forwardedCases' => $forwardedCases,
            
            // Analytics Data
            'yearlyAnalytics' => $yearlyAnalytics,
            'severityDistribution' => $severityDistribution,
            'performanceMetrics' => $performanceMetrics,
            
            // Filter Options
            'filters' => [
                'severities' => ['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'],
                'sortOptions' => [
                    'created_at' => 'Date Created',
                    'severity' => 'Severity',
                    'updated_at' => 'Last Updated'
                ],
                'years' => range($currentYear, $currentYear - 4)
            ],
            
            // Current Filters
            'currentFilters' => [
                'severity' => $request->input('severity'),
                'sort' => $request->input('sort', 'created_at'),
                'order' => $request->input('order', 'desc'),
                'year' => $request->input('year', $currentYear)
            ]
        ]);
    }

    // ==================== PRIVATE METHODS ====================

    private function getDashboardMetrics($analystId, $currentMonth, $currentYear)
    {
        // Total Cases (All Time)
        $totalCases = ComplaintStatusLog::where('forwarded_to', $analystId)->count();

        // Current Month Cases
        $currentMonthCases = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Previous Month Cases
        $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        
        $previousMonthCases = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count();

        // Percentage Change Calculation
        $percentageChange = $previousMonthCases > 0 
            ? (($currentMonthCases - $previousMonthCases) / $previousMonthCases) * 100
            : ($currentMonthCases > 0 ? 100 : 0);

        return [
            'totalCases' => [
                'value' => $totalCases,
                'icon' => '📊',
                'trend' => null,
                'description' => 'Total Cases Assigned'
            ],
            'monthlyCases' => [
                'value' => $currentMonthCases,
                'previous' => $previousMonthCases,
                'trend' => $percentageChange,
                'icon' => '📈',
                'description' => 'Monthly Cases'
            ]
        ];
    }

    private function getCaseStatistics($analystId, $currentMonth)
    {
        $currentYear = now()->year;

        // Pending Cases
        $pendingCases = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->where('status', 'forwarded')
            ->count();

        $pendingMonth = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->where('status', 'forwarded')
            ->whereMonth('changed_at', $currentMonth)
            ->whereYear('changed_at', $currentYear)
            ->count();

        $pendingTrend = $pendingCases > 0 
            ? (($pendingMonth - $pendingCases) / $pendingCases) * 100
            : 0;

        // Completed Cases
        $completedCases = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->where('status', 'completed')
            ->count();

        $completedMonth = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->where('status', 'completed')
            ->whereMonth('changed_at', $currentMonth)
            ->whereYear('changed_at', $currentYear)
            ->count();

        $completedTrend = $completedCases > 0
            ? (($completedMonth - $completedCases) / $completedCases) * 100
            : 0;

        // High Priority Cases
        $highPriority = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->whereHas('complaint', fn($q) => $q->where('severity', 'high'))
            ->count();

        $highPriorityMonth = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereHas('complaint', fn($q) => $q->where('severity', 'high'))
            ->count();

        $highPriorityTrend = $highPriority > 0
            ? (($highPriorityMonth - $highPriority) / $highPriority) * 100
            : 0;

        // Under Review Cases
        $underReview = Complaint::where('assigned_to', $analystId)
            ->where('status', 'under_review')
            ->count();

        return [
            'pending' => [
                'value' => $pendingCases,
                'monthly' => $pendingMonth,
                'trend' => round($pendingTrend, 1),
                'icon' => '⏳',
                'color' => 'warning',
                'description' => 'Pending Review'
            ],
            'completed' => [
                'value' => $completedCases,
                'monthly' => $completedMonth,
                'trend' => round($completedTrend, 1),
                'icon' => '✅',
                'color' => 'success',
                'description' => 'Completed Cases'
            ],
            'highPriority' => [
                'value' => $highPriority,
                'monthly' => $highPriorityMonth,
                'trend' => round($highPriorityTrend, 1),
                'icon' => '🚨',
                'color' => 'danger',
                'description' => 'High Priority'
            ],
            'underReview' => [
                'value' => $underReview,
                'icon' => '🔍',
                'color' => 'info',
                'description' => 'Under Review'
            ]
        ];
    }

    private function getChartData($analystId, $currentMonth, $currentYear)
    {
        $daysInMonth = now()->daysInMonth;
        $labels = [];
        $assigned = [];
        $completed = [];
        $pending = [];
        $highPriority = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($currentYear, $currentMonth, $day);
            $labels[] = $date->format('d M');

            // Daily counts
            $assigned[] = ComplaintStatusLog::where('forwarded_to', $analystId)
                ->whereDate('created_at', $date)
                ->count();

            $completed[] = ComplaintStatusLog::where('forwarded_to', $analystId)
                ->where('status', 'completed')
                ->whereDate('changed_at', $date)
                ->count();

            $pending[] = ComplaintStatusLog::where('forwarded_to', $analystId)
                ->where('status', 'forwarded')
                ->whereDate('created_at', $date)
                ->count();

            $highPriority[] = ComplaintStatusLog::where('forwarded_to', $analystId)
                ->whereDate('created_at', $date)
                ->whereHas('complaint', fn($q) => $q->where('severity', 'high'))
                ->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                'assigned' => [
                    'label' => 'Assigned',
                    'data' => $assigned,
                    'color' => '#3B82F6'
                ],
                'completed' => [
                    'label' => 'Completed',
                    'data' => $completed,
                    'color' => '#10B981'
                ],
                'pending' => [
                    'label' => 'Pending',
                    'data' => $pending,
                    'color' => '#F59E0B'
                ],
                'highPriority' => [
                    'label' => 'High Priority',
                    'data' => $highPriority,
                    'color' => '#EF4444'
                ]
            ]
        ];
    }

    private function getForwardedCases($analystId, Request $request)
    {
        $query = Complaint::with(['officer', 'media', 'latestStatus'])
            ->whereHas('latestStatus', function($q) use ($analystId) {
                $q->where('forwarded_to', $analystId)
                  ->where('status', 'forwarded');
            });

        // Apply filters
        if ($severity = $request->input('severity')) {
            $query->where('severity', $severity);
        }

        // Apply sorting
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->take(5)->get();
    }

    private function getYearlyAnalytics($analystId, Request $request)
    {
        $selectedYear = $request->input('year', now()->year);

        $monthlyData = ComplaintStatusLog::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('forwarded_to', $analystId)
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $months = [];
        $counts = [];

        foreach (range(1, 12) as $month) {
            $months[] = Carbon::create()->month($month)->format('M');
            $counts[] = $monthlyData[$month] ?? 0;
        }

        return [
            'labels' => $months,
            'data' => $counts,
            'selectedYear' => $selectedYear
        ];
    }

    private function getSeverityDistribution($analystId)
    {
        $distribution = Complaint::select('severity', DB::raw('COUNT(*) as count'))
            ->whereHas('latestStatus', function($q) use ($analystId) {
                $q->where('forwarded_to', $analystId);
            })
            ->groupBy('severity')
            ->pluck('count', 'severity');

        $total = $distribution->sum();
        
        if ($total === 0) {
            return [
                'high' => 0,
                'medium' => 0,
                'low' => 0,
                'total' => 0
            ];
        }

        return [
            'high' => round(($distribution['high'] ?? 0) / $total * 100, 1),
            'medium' => round(($distribution['medium'] ?? 0) / $total * 100, 1),
            'low' => round(($distribution['low'] ?? 0) / $total * 100, 1),
            'total' => $total
        ];
    }

    private function getPerformanceMetrics($analystId, $currentMonth)
    {
        $currentYear = now()->year;
        
        // Average Resolution Time
        $avgResolution = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->where('status', 'completed')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, changed_at)) as avg_hours'))
            ->first();

        // Monthly Completion Rate
        $monthlyAssigned = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $monthlyCompleted = ComplaintStatusLog::where('forwarded_to', $analystId)
            ->where('status', 'completed')
            ->whereMonth('changed_at', $currentMonth)
            ->whereYear('changed_at', $currentYear)
            ->count();

        $completionRate = $monthlyAssigned > 0 
            ? round(($monthlyCompleted / $monthlyAssigned) * 100, 1)
            : 0;

        return [
            'avgResolutionTime' => round($avgResolution->avg_hours ?? 0, 1),
            'completionRate' => $completionRate,
            'monthlyAssigned' => $monthlyAssigned,
            'monthlyCompleted' => $monthlyCompleted
        ];
    }

    public function showCaseDetail($trackId)
    {
        $analystId = Auth::id();
        $cases = Complaint::where('assigned_to', $analystId)->get();

        return view('forensic_analyst.partials.case_detail_panel', compact('cases'));
    }

    
}





