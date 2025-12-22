<?php
namespace App\Http\Controllers;

use App\Helpers\NotificationHelper; // Add this
use App\Models\AiUsage;
use App\Models\Complaint;
use App\Models\Media;
use App\Models\PendingSummaries;
use App\Models\PublicAlert;
use App\Models\RecentActivities;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        // Get user registration stats
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
        $alerts=PublicAlert::countPublicAlerts();
        $totalComplaints=Complaint::count();

        // ✅ **NEW: Dashboard access notification for admin**
        if (Auth::user()->role_id == 1) {
            $loginCount = RecentActivities::where('user_id', Auth::id())
                ->where('action', 'like', '%logged in%')
                ->whereDate('created_at', today())
                ->count();
                
            if ($loginCount == 1) { // First login today
                NotificationHelper::createForUser(
                    Auth::id(),
                    "Daily Admin Dashboard",
                    "Welcome to your admin dashboard. System stats: {$totalUsers} users, {$totalMedia} media files.",
                    'info',
                    route('dashboard')
                );
            }
        }

        // Recent activities
        $query = RecentActivities::with('user')->orderBy('created_at', 'desc');

        if ($request->get('show') === 'all') {
            $recentActivities = $query->get();
        } else {
            $recentActivities = $query->paginate(10);
        }
   

        return view('admin.dashboard', [
            'months' => $months,
            'userCounts' => $counts,
            'selectedYear' => $year,
            'years' => $years,
            'totalUsers' => $totalUsers,
            'totalMedia' => $totalMedia,
            'pendingSummaries' => $pendingSummaries,
            'aiUsage' => $aiUsage,
            'alerts' => $alerts,
            'totalComplaints' => $totalComplaints,
            'recentActivities' => $recentActivities,
            'showAll' => $request->get('show') === 'all',
        ]);
    }
    
    // ✅ **NEW: Method to send system alerts to all users**
    public function sendSystemAlert(Request $request)
    {
        if (Auth::user()->role_id != 1) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,danger,success'
        ]);
        
        // Broadcast to all active users
        $count = NotificationHelper::broadcastToAll(
            $request->title,
            $request->message,
            $request->type,
            route('public.alerts')
        );
        
        // Log activity
        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Admin ' . Auth::user()->name . ' sent system alert to ' . $count . ' users.',
        ]);
        
        // ✅ **NEW: Admin notification about sent alert**
        NotificationHelper::createForUser(
            Auth::id(),
            "System Alert Sent",
            "You successfully sent '{$request->title}' to {$count} users",
            'success',
            route('dashboard')
        );
        
        return back()->with('success', "System alert sent to {$count} users.");
    }
}