<?php
// app/Http/Controllers/PublicAlertController.php
namespace App\Http\Controllers;

use App\Models\PublicAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserPublicAlerts extends Controller
{
    public function index()
    {
        // Sirf woh alerts jo abhi tak valid hain
        $alerts = PublicAlert::where('visible_until', '>=', Carbon::now())
    ->orderBy('id', 'desc')
    ->take(3) // sirf 3 latest alerts lo
    ->get()
    ->map(function ($alert) {
        $alert->media = json_decode($alert->media, true); // array bana do
        return $alert;
    });

                             
                             
                             return view('public_user.dashboard', compact('alerts'));
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

    if ($request->ajax()) {
        return view('public_user.partials.alerts-cards', compact('alerts'))->render();
    }

    return view('public_user.public-alerts', compact('alerts'));
}



}
