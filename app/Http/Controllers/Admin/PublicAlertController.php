<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicAlert;
use App\Models\RecentActivities;
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicAlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = PublicAlert::query();

        if ($request->has('type') && $request->type != '') {
            $alerts->where('type', $request->type);
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'active') {
                $alerts->where('visible_until', '>', now());
            } elseif ($request->status == 'expired') {
                $alerts->where('visible_until', '<=', now());
            }
        }

        $alerts = $alerts->latest()->get();

        // ✅ **NEW: Alerts dashboard access notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Public Alerts Management",
            "You accessed the public alerts management dashboard",
            'info',
            route('admin.public.alerts')
        );

        return view('admin.manage-public-alerts', compact('alerts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string',
            'visible_until' => 'required|date',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx|max:10240'
        ]);

        $mediaFiles = [];

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('alerts_media', 'public');
                $mediaFiles[] = $path;
            }
        }

        $alert = PublicAlert::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'visible_until' => $request->visible_until,
            'media' => !empty($mediaFiles) ? json_encode($mediaFiles) : null,
            'user_id' => Auth::id(),
        ]);

        // ✅ **NEW: Alert creation notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "Public Alert Created",
            "You created a new public alert: '{$request->title}'",
            'success',
            route('admin.public.alerts')
        );

        // ✅ **NEW: Broadcast alert to all users (important feature)**
        $userCount = NotificationHelper::broadcastToAll(
            $request->title,
            $request->message,
            'warning',
            route('public.alerts')
        );
        
        NotificationHelper::createForUser(
            Auth::id(),
            "Alert Broadcast Complete",
            "Public alert '{$request->title}' sent to {$userCount} users",
            'success',
            route('admin.public.alerts')
        );

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'New alert created',
        ]);

        return redirect()->back()->with('success', 'Public Alert created and broadcast successfully!');
    }

    public function edit($id)
    {
        $alert = PublicAlert::findOrFail($id);
        
        // ✅ **NEW: Alert edit view notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Editing Public Alert",
            "You are editing alert: '{$alert->title}'",
            'info',
            route('admin.public.alerts.edit', $alert->id)
        );

        return view('admin.partials.public-alerts-detail', compact('alert'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string',
            'visible_until' => 'required|date',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx'
        ]);

        $alert = PublicAlert::findOrFail($id);
        $oldTitle = $alert->title;

        $mediaFiles = json_decode($alert->media, true) ?? [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('alerts', 'public');
                $mediaFiles[] = $path;
            }
        }

        $alert->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'visible_until' => $request->visible_until,
            'media' => json_encode($mediaFiles)
        ]);

        // ✅ **NEW: Alert update notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Public Alert Updated",
            "Alert updated: '{$oldTitle}' → '{$request->title}'",
            'info',
            route('admin.public.alerts')
        );

        // ✅ **NEW: Re-broadcast updated alert to all users**
        if ($request->rebroadcast) {
            $userCount = NotificationHelper::broadcastToAll(
                "Updated: " . $request->title,
                $request->message,
                'info',
                route('public.alerts')
            );
            
            NotificationHelper::createForUser(
                Auth::id(),
                "Alert Re-broadcast",
                "Updated alert re-sent to {$userCount} users",
                'success',
                route('admin.public.alerts')
            );
        }

        return redirect()->route('admin.public.alerts')->with('success', 'Alert updated successfully!');
    }

    public function destroy($id)
    {
        $alert = PublicAlert::findOrFail($id);
        $alertTitle = $alert->title;
        
        // ✅ **NEW: Alert deletion notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "Public Alert Deleted",
            "You deleted alert: '{$alertTitle}'",
            'warning',
            route('admin.public.alerts')
        );

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'Alert has id '.$id.' deleted',
        ]);

        $alert->delete();
        
        return redirect()->back()->with('success', 'Alert deleted successfully.');
    }
}