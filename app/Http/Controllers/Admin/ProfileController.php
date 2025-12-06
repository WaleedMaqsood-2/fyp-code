<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecentActivities;
use App\Models\User;
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'cnic' => 'required|string|max:15|unique:users,cnic,' . $user->id,
            'contact_number' => 'required|string|max:15|unique:users,contact_number,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldEmail = $user->email;
        $oldName = $user->name;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->cnic = $request->cnic;
        $user->contact_number = $request->contact_number;

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/profile'), $imageName);
            $user->profile_image = 'images/profile/' . $imageName;
            
            // ✅ **NEW: Profile image update notification**
            NotificationHelper::createForUser(
                $user->id,
                "Profile Picture Updated",
                "Your profile picture has been updated successfully",
                'success',
                route('admin.profile')
            );
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            
            // ✅ **NEW: Password change notification (security alert)**
            NotificationHelper::createForUser(
                $user->id,
                "Password Changed",
                "Your account password has been changed",
                'warning',
                route('admin.profile')
            );
            
            // ✅ **NEW: Security notification to all admins**
            $otherAdmins = User::where('role_id', 1)
                ->where('id', '!=', $user->id)
                ->active()
                ->get();
                
            foreach ($otherAdmins as $admin) {
                NotificationHelper::createForUser(
                    $admin->id,
                    "Admin Password Changed",
                    "Admin {$user->name} changed their password",
                    'info',
                    route('admin.users.show', $user->id)
                );
            }
        }

        $user->save();

        // ✅ **NEW: Profile update notification**
        NotificationHelper::createForUser(
            $user->id,
            "Profile Updated",
            "Your profile information has been updated successfully",
            'success',
            route('admin.profile')
        );

        // ✅ **NEW: Notify other admins about significant changes**
        if ($oldEmail !== $request->email) {
            $otherAdmins = User::where('role_id', 1)
                ->where('id', '!=', $user->id)
                ->active()
                ->get();
                
            foreach ($otherAdmins as $admin) {
                NotificationHelper::createForUser(
                    $admin->id,
                    "Admin Email Changed",
                    "Admin {$oldName} changed email from {$oldEmail} to {$request->email}",
                    'info',
                    route('admin.users.show', $user->id)
                );
            }
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'User '.$request->name.' updated his profile',
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}