<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\RecentActivities;
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldEmail = $user->email;
        $oldName = $user->name;

        $user->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
            
            // ✅ **NEW: Email change notification**
            NotificationHelper::createForUser(
                $user->id,
                "Email Change Requires Verification",
                "Your email has been changed to {$request->email}. Please verify your new email.",
                'warning',
                route('verify.email')
            );
        }

        $request->user()->save();

        // ✅ **NEW: Profile update notification**
        NotificationHelper::createForUser(
            $user->id,
            "Profile Information Updated",
            "Your profile information has been updated successfully",
            'success',
            route('profile.edit')
        );

        // ✅ **NEW: Notify admin about significant profile changes**
        if ($oldEmail !== $request->email || $oldName !== $request->name) {
            $admins = \App\Models\User::where('role_id', 1)->active()->get();
            foreach ($admins as $admin) {
                NotificationHelper::createForUser(
                    $admin->id,
                    "User Profile Updated",
                    "User changed profile: Name '{$oldName}' → '{$request->name}', Email '{$oldEmail}' → '{$request->email}'",
                    'info',
                    route('profile.edit', $user->id)
                );
            }
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'User '.$user->name.' updated profile information',
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // ✅ **NEW: Account deletion notification to user (before logout)**
        NotificationHelper::createForUser(
            $user->id,
            "Account Deletion Request",
            "Your account deletion request has been received. Account will be permanently deleted.",
            'danger',
            route('login')
        );

        // ✅ **NEW: Notify all admins about account deletion**
        $admins = \App\Models\User::where('role_id', 1)
            ->where('id', '!=', $user->id)
            ->active()
            ->get();
            
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "User Account Deleted",
                "User {$user->name} ({$user->email}) has deleted their account",
                'warning',
                route('manage.users')
            );
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        RecentActivities::create([
            'user_id' => $user->id,
            'action'  => 'User '.$user->name.' deleted his account',
        ]);

        return Redirect::to('/');
    }
}