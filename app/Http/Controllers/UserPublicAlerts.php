<?php
namespace App\Http\Controllers;

use App\Helpers\NotificationHelper; // Add this
use App\Models\PublicAlert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserPublicAlerts extends Controller
{
    public function index()
    {
        $alerts = PublicAlert::where('visible_until', '>=', Carbon::now())
            ->orderBy('id', 'desc')
            ->take(3)
            ->get()
            ->map(function ($alert) {
                $alert->media = json_decode($alert->media, true);
                return $alert;
            });

        // ✅ **NEW: Alert view notification for first-time visitors**
        if (Auth::check() && !session()->has('alerts_viewed')) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Public Alerts Available",
                "Check important public alerts and announcements",
                'info',
                route('public.alerts')
            );
            session(['alerts_viewed' => true]);
        }

        return view('public_user.dashboard', compact('alerts'));
    }
    
    public function welcomeIndex()
    {
        $alerts = PublicAlert::where('visible_until', '>=', Carbon::now())
            ->orderBy('id', 'desc')
            ->take(3)
            ->get()
            ->map(function ($alert) {
                $alert->media = json_decode($alert->media, true);
                return $alert;
            });

        return view('public_user.welcome', compact('alerts'));
    }

    public function allAlerts(Request $request)
    {
        $query = PublicAlert::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $alerts = $query->where('visible_until', '>=', now())
                        ->latest()
                        ->paginate(6);

        $alerts->getCollection()->transform(function($alert){
            $alert->media = json_decode($alert->media, true);
            return $alert;
        });

        // Get unique alert types
        $alertTypes = PublicAlert::select('type')
                        ->distinct()
                        ->pluck('type');

        // ✅ **NEW: Alert search notification**
        if (Auth::check() && $request->filled('search')) {
            NotificationHelper::createForUser(
                Auth::id(),
                "Alert Search Performed",
                "You searched for alerts with keyword: '{$request->search}'",
                'info',
                route('public.alerts', $request->query())
            );
        }

        if ($request->ajax()) {
            return view('public_user.partials.alerts-cards', compact('alerts'))->render();
        }

        return view('public_user.public-alerts', compact('alerts', 'alertTypes'));
    }
    
    public function subscribeToAlerts(Request $request)
    {
        $request->validate([
            'alert_types' => 'array',
            'alert_types.*' => 'in:crime,emergency,weather,traffic,general'
        ]);
        
        if (Auth::check()) {
            $user = User::find(Auth::id());
            $user->alert_preferences = json_encode($request->alert_types ?? []);
            $user->save();
            $user->save();
            
            NotificationHelper::createForUser(
                $user->id,
                "Alert Preferences Updated",
                "You will now receive notifications for selected alert types",
                'success',
                route('public.alerts')
            );
            
            return back()->with('success', 'Alert preferences updated successfully!');
        }
        
        return back()->with('error', 'Please login to update alert preferences.');
    }
}