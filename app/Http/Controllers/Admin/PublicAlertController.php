<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicAlert;
use App\Models\RecentActivities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicAlertController extends Controller
{
    // Show alerts
    // public function index()
    // {
    //     $alerts = PublicAlert::latest()->get();
    //     return view('admin.manage-public-alerts', compact('alerts'));
    // }
public function index(Request $request)
{
    $alerts = PublicAlert::query();

    // Filter by type
    if ($request->has('type') && $request->type != '') {
        $alerts->where('type', $request->type);
    }

    // Filter by status
    if ($request->has('status') && $request->status != '') {
        if ($request->status == 'active') {
            $alerts->where('visible_until', '>', now());
        } elseif ($request->status == 'expired') {
            $alerts->where('visible_until', '<=', now());
        }
    }

    $alerts = $alerts->latest()->get();

    return view('admin.manage-public-alerts', compact('alerts'));
}

    // Store alert
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
            $path = $file->store('alerts_media', 'public'); // store in storage/app/public/alerts_media
            $mediaFiles[] = $path;
        }
    }

  PublicAlert::create([
    'title' => $request->title,
    'message' => $request->message,
    'type' => $request->type,
    'visible_until' => $request->visible_until,
    'media' => !empty($mediaFiles) ? json_encode($mediaFiles) : null,
    'user_id' =>Auth::id(), 
    // logged-in user
]);
     
    RecentActivities::create([
        'user_id' => Auth::id(),
        'action'  => 'New alert created',
    ]);

    return redirect()->back()->with('success', 'Public Alert created successfully!');
}



public function edit($id)
{
    $alert = PublicAlert::findOrFail($id);
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

    // Media handle
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

    return redirect()->route('admin.public.alerts')->with('success', 'Alert updated successfully!');
}



    // Delete alert
    public function destroy($id)
    {
        $alert = PublicAlert::findOrFail($id);
            RecentActivities::create([
                'user_id' => Auth::id(),
                'action'  => 'Alert has id '.$id.' deleted',
            ]);
        $alert->delete();
        return redirect()->back()->with('success', 'Alert deleted successfully.');
    }
}
