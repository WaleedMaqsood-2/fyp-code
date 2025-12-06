<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Media;
use App\Models\AIFeedback;
use App\Models\AnalyticsReport;
use App\Models\RecentActivities;
use App\Helpers\NotificationHelper; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function ajaxUserSearch(Request $request)
    {
        if ($request->has('id')) {
            $user = User::with('role')->find($request->id);
            $roles = Role::all();
            
            // ✅ **NEW: User detail view notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "User Details Viewed",
                "You viewed details for user: {$user->name}",
                'info',
                route('manage-users', $user->id)
            );
            
            return view('admin.partials.user-details', compact('user', 'roles'));
        }

        $query = $request->get('q');
        $users = User::with('role')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhereHas('role', function ($roleQuery) use ($query) {
                $roleQuery->where('role_name', 'like', "%{$query}%");
            })
            ->get();

        $roles = Role::all();

        if ($request->get('mode') === 'suggestion') {
            return response()->json(['users' => $users]);
        }

        if ($request->get('mode') === 'cards') {
            return view('admin.partials.user-cards', compact('users', 'roles'));
        }

        return view('admin.partials.user-search-results', compact('users', 'roles'));
    }

    public function ajaxUserList(Request $request)
    {
        $query = $request->get('q');

        $users = User::with('role')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->get();

        // ✅ **NEW: User search notification**
        if ($query) {
            NotificationHelper::createForUser(
                Auth::id(),
                "User Search",
                "You searched for users with keyword: '{$query}'",
                'info',
                route('manage.users')
            );
        }

        return view('admin.partials.user-cards', compact('users'))->render();
    }

    public function ajaxMediaSearch(Request $request)
    {
        if ($request->has('id')) {
            $media = Media::find($request->id);
            
            // ✅ **NEW: Media detail view notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "Media Details Viewed",
                "You viewed media file details",
                'info',
                route('manage.media', $media->id)
            );
            
            return view('admin.partials.media-details', compact('media'));
        }

        $query = $request->get('q');
        $mediaFiles = Media::where('file_type', 'like', "%{$query}%")
            ->orWhere('file_path', 'like', "%{$query}%")
            ->orWhere('user_id', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->orWhereHas('users', function ($userQuery) use ($query) {
                $userQuery->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'file_type','file_path','status','user_id','uploaded_at','description','created_at','updated_at']);

        if ($request->get('mode') === 'suggestion') {
            return response()->json(['media' => $mediaFiles]);
        }

        return view('admin.partials.media-search-results', compact('mediaFiles'));
    }

    public function ajaxAISearch(Request $request)
    {
        if ($request->has('id')) {
            $feedback = AIFeedback::find($request->id);
            
            // ✅ **NEW: AI feedback view notification**
            NotificationHelper::createForUser(
                Auth::id(),
                "AI Feedback Viewed",
                "You viewed AI feedback details",
                'info',
                route('ai.feedback')
            );
            
            return view('admin.partials.ai-feedback-search-results', compact('feedback'));
        }

        $query = $request->get('q');
        $feedbacks = AIFeedback::where('ai_type', 'like', "%{$query}%")
            ->orWhere('feedback_text', 'like', "%{$query}%")
            ->orWhere('rating', 'like', "%{$query}%")
            ->orWhere('user_id', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id','ai_type','feedback_text','rating','user_id','created_at','updated_at']);

        if ($request->get('mode') === 'suggestion') {
            return response()->json(['feedback' => $feedbacks]);
        }

        return view('admin.partials.ai-feedback-search-results', compact('feedbacks'))->render();
    }

    public function index()
    {
        $users = \App\Models\User::paginate(3);
        $roles = \App\Models\Role::all();
        
        // ✅ **NEW: Users dashboard access notification**
        NotificationHelper::createForUser(
            Auth::id(),
            "User Management",
            "You accessed the user management dashboard",
            'info',
            route('manage.users')
        );

        return view('admin.manage-users', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role_id' => 'required',
            'password' => 'required|min:6|confirmed',
            'cnic' => 'required|string|max:15|unique:users',
            'contact_number' => 'required|string|max:15|unique:users',
        ]);

        $otp = rand(100000, 999999);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'cnic' => $request->cnic,
            'contact_number' => $request->contact_number,
            'status' => 'inactive',
            'is_verified' => false,
            'reg_status' => 'pending',
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at' => null,
        ]);

        $verifyUrl = route('verify.email', ['email' => $user->email]);

        // ✅ **NEW: User creation notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "New User Created",
            "You added new user: {$user->name} ({$user->email}) as " . Role::find($request->role_id)->role_name,
            'success',
            route('manage.users', $user->id)
        );

        // ✅ **NEW: Registration notification to new user**
        NotificationHelper::publicFIRSubmitted(
            $user->id,
            'ADMIN-CREATED-' . $user->id,
            'Account-' . $user->id
        );

        // ✅ **NEW: Welcome notification to new user**
        NotificationHelper::createForUser(
            $user->id,
            "Account Created by Admin",
            "Your account has been created by administrator. Please verify your email.",
            'info',
            route('verify.email')
        );

        // ✅ **NEW: Notify other admins about new user creation
        $otherAdmins = User::where('role_id', 1)
            ->where('id', '!=', Auth::id())
            ->active()
            ->get();
            
        foreach ($otherAdmins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "New User Added",
                "Admin " . Auth::user()->name . " added new user: {$user->name}",
                'info',
                route('manage.users', $user->id)
            );
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action' => 'New user '.$request->name.' added',
        ]);

        \Illuminate\Support\Facades\Mail::send('auth.verify', ['user' => $user, 'otp' => $otp, 'verifyUrl' => $verifyUrl], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email - Forensic System');
        });

        session(['email' => $user->email]);

        return redirect()->back()->with('success', 'User added! Please verify the email.');
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $oldName = $user->name;
        $oldEmail = $user->email;
        $oldRoleId = $user->role_id;
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required',
            'cnic' => 'required|unique:users,cnic,' . $user->id,
            'contact_number' => 'required|unique:users,contact_number,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->cnic = $request->cnic;
        $user->contact_number = $request->contact_number;
        
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            
            // ✅ **NEW: Password change notification to user**
            NotificationHelper::createForUser(
                $user->id,
                "Password Changed by Admin",
                "Your account password has been changed by administrator",
                'warning',
                route('profile')
            );
        }
        
        $user->save();

        // ✅ **NEW: Profile update notification to user**
        if ($oldEmail !== $request->email || $oldName !== $request->name) {
            $changes = [];
            if ($oldName !== $request->name) $changes[] = "Name: {$oldName} → {$request->name}";
            if ($oldEmail !== $request->email) $changes[] = "Email: {$oldEmail} → {$request->email}";
            
            NotificationHelper::createForUser(
                $user->id,
                "Profile Updated by Admin",
                "Your profile has been updated: " . implode(', ', $changes),
                'info',
                route('profile')
            );
        }

        // ✅ **NEW: Role change notification**
        if ($oldRoleId != $request->role_id) {
            $oldRole = Role::find($oldRoleId)->role_name ?? 'Unknown';
            $newRole = Role::find($request->role_id)->role_name ?? 'Unknown';
            
            NotificationHelper::createForUser(
                $user->id,
                "Role Changed",
                "Your account role has been changed from {$oldRole} to {$newRole}",
                'warning',
                route('profile')
            );
            
            // Notify admin about role change
            NotificationHelper::createForUser(
                Auth::id(),
                "User Role Updated",
                "You changed role of {$oldName} from {$oldRole} to {$newRole}",
                'info',
                route('manage.users', $user->id)
            );
        }

        // ✅ **NEW: Update notification to admin**
        $updateMessage = "User {$oldName} updated";
        if ($oldEmail !== $request->email) $updateMessage .= ". Email changed";
        if ($oldRoleId != $request->role_id) $updateMessage .= ". Role changed";
        
        NotificationHelper::createForUser(
            Auth::id(),
            "User Updated",
            $updateMessage,
            'success',
            route('manage.users', $user->id)
        );

        return back()->with('success', 'User updated!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $userEmail = $user->email;
        
        // ✅ **NEW: Pre-deletion notification to user**
        NotificationHelper::createForUser(
            $user->id,
            "Account Deleted",
            "Your account has been deleted by administrator",
            'danger',
            route('login')
        );
        
        // ✅ **NEW: Deletion notification to admin**
        NotificationHelper::createForUser(
            Auth::id(),
            "User Deleted",
            "You deleted user: {$userName} ({$userEmail})",
            'warning',
            route('manage.users')
        );
        
        // ✅ **NEW: Notify other admins about user deletion
        $otherAdmins = User::where('role_id', 1)
            ->where('id', '!=', Auth::id())
            ->active()
            ->get();
            
        foreach ($otherAdmins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "User Account Deleted",
                "Admin " . Auth::user()->name . " deleted user: {$userName}",
                'warning',
                route('manage.users')
            );
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'User '.$userName.' deleted',
        ]);

        $user->delete();
        
        return back()->with('success', 'User deleted!');
    }
}