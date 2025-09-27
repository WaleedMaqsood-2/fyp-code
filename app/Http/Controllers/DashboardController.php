<?php

namespace App\Http\Controllers;

use App\Models\AiUsage;
use App\Models\Media; // Replace with your actual media model
use App\Models\PendingSummaries;
use App\Models\RecentActivites;
use App\Models\RecentActivities;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
  public function index(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);

    $userRegistrations = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

    $months = [];
    $counts = [];

    foreach (range(1, 12) as $m) {
        $months[] = Carbon::create()->month($m)->format('M');
        $counts[] = $userRegistrations[$m] ?? 0;
    }

    // Available years for dropdown
    $years = range(Carbon::now()->year, Carbon::now()->year - 4);

    // Stats
    $totalUsers = User::count();
    $totalMedia = Media::count(); 
    $pendingSummaries = PendingSummaries::countPendingSummaries();
    $aiUsage = AiUsage::countAiUsage();



    // Recent activities fetch
 $query = RecentActivities::with('user')->orderBy('created_at', 'desc');

    if ($request->get('show') === 'all') {
        $recentActivities = $query->get(); // sari records bina pagination
    } else {
        $recentActivities = $query->paginate(10); // default 10
    }

    // $recentActivities = RecentActivities::with('user')
    //     ->orderBy('created_at', 'desc')
    //     ->take(10) // latest 10 activities
    //     ->get();
    return view('admin.dashboard', [
        'months' => $months,
        'userCounts' => $counts,
        'selectedYear' => $year,
        'years' => $years,
        'totalUsers' => $totalUsers,
        'totalMedia' => $totalMedia,
        'pendingSummaries' => $pendingSummaries,
        'aiUsage' => $aiUsage,
        'recentActivities' => $recentActivities,
        'showAll' => $request->get('show') === 'all', // flag bhej do
    ]);
}

}