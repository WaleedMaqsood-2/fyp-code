<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RecentActivities;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // user ko login kr
            Auth::login($user);
            
            // Login success notification to user
            NotificationHelper::createForUser(
                $user->id,
                "Login Successful",
                "You have successfully logged into your account",
                'success',
                route('dashboard')
            );
            
            // Log activity
            RecentActivities::create([
                'user_id' => Auth::id(),
                'action'  => $user->name . ' logged in.',
            ]);
            
            // Redirect based on role with notification
            if ($user->role_id == 1) {
                // Admin login - notify other admins
                $otherAdmins = User::where('role_id', 1)
                    ->where('id', '!=', $user->id)
                    ->active()
                    ->get();
                
                foreach ($otherAdmins as $admin) {
                    NotificationHelper::createForUser(
                        $admin->id,
                        "Admin Login Alert",
                        "Admin {$user->name} has logged into the system",
                        'info',
                        route('login')
                    );
                }
                
                return redirect()->route('dashboard')->with('success', 'Welcome '.$user->name);
            }  
            else if ($user->role_id == 2) {
                // Police login - notify supervisor/admin
                NotificationHelper::createForUser(
                    $user->id,
                    "Welcome Back",
                    "You are now logged into Police Dashboard",
                    'success',
                    route('police.dashboard')
                );
                
                return redirect()->route('police.dashboard')->with('success', 'Welcome '.$user->name);
            } 
            else if ($user->role_id == 3) {
                // Forensic analyst login
                NotificationHelper::createForUser(
                    $user->id,
                    "Forensic Dashboard",
                    "Welcome to Forensic Analysis System",
                    'success',
                    route('forensic.dashboard')
                );
                
                return redirect()->route('forensic.dashboard')->with('success', 'Welcome '.$user->name);
            } 
            else if ($user->role_id == 4) {
                // Public user login
                NotificationHelper::createForUser(
                    $user->id,
                    "Login Successful",
                    "Welcome back to Public Complaint System",
                    'success',
                    route('public.dashboard')
                );
                
                return redirect()->route('public.dashboard')->with('success', 'Welcome '.$user->name);
            }
            else {
                return redirect()->route('register')->with('success', 'Welcome '.$user->name);
            }
        }
        
        // Failed login attempt - notify admin if multiple attempts
        $failedUser = User::where('email', $request->email)->first();
        if ($failedUser) {
            // Count recent failed attempts
            $failedAttempts = DB::table('failed_login_attempts')
                ->where('email', $request->email)
                ->where('created_at', '>', now()->subMinutes(15))
                ->count();
                
            if ($failedAttempts >= 3) {
                // Notify admins about suspicious activity
                $admins = User::where('role_id', 1)->active()->get();
                foreach ($admins as $admin) {
                    NotificationHelper::createForUser(
                        $admin->id,
                        "Suspicious Login Attempt",
                        "Multiple failed login attempts for user: {$request->email}",
                        'warning',
                        route('login')
                    );
                }
            }
        }
        
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        $user = Auth::user();
        
        // Logout notification
        NotificationHelper::createForUser(
            $user->id,
            "Logout Successful",
            "You have been logged out from your account",
            'info',
            route('login')
        );
        
        // Log activity
        RecentActivities::create([
            'user_id' => $user->id,
            'action'  => $user->name . ' logged out.',
        ]);
        
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function Register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'cnic' => 'required|string|max:15|unique:users',
            'contact_number' => 'required|string|max:15|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);
        $defaultRole = '4';

        // Create user
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'cnic'       => $request->cnic,
            'contact_number' => $request->contact_number,
            'role_id'        => $defaultRole,
            'status'      => 'active',
            'is_verified' => false,
            'reg_status'  => 'pending',
            'otp'   => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at'=> null,
        ]);

        // Send OTP email
        Mail::raw("Your OTP for email verification is: {$otp}. It will expire in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email - Forensic System');
        });

        session(['email' => $user->email]);

        // Log activity
        RecentActivities::create([
            'user_id' => $user->id,
            'action'  => 'New user '.$user->name.' registered.',
        ]);
        
        // ✅ **NEW: Send registration notification to the new user**
        NotificationHelper::publicFIRSubmitted(
            $user->id,
            'REG-' . $user->id,
            'Registration-' . $user->id
        );
        
        // ✅ **NEW: Send welcome email notification**
        NotificationHelper::createForUser(
            $user->id,
            "Welcome to Forensic System",
            "Thank you for registering. Please verify your email to continue.",
            'info',
            route('verify.email')
        );

        // ✅ **NEW: Send notification to all admins about new registration**
        $admins = User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::adminNewUserRegistration(
                $admin->id,
                $user->id,
                $user->name
            );
        }

        // Redirect to verify page
        return redirect()->route('verify.email')->with('success', 'Signup successful! Please verify your email.');
    }

    public function showVerifyForm()
    {
        return view('auth.verify-email');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required',
        ]);

        $otp = implode('', $request->otp);
        $user = User::where('email', $request->email)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$user) {
            return back()->with('error', 'Invalid or expired OTP.');
        }

        // Update user verification status
        $user->update([
            'is_verified' => true,
            'reg_status'  => 'Registered',
            'status'      => 'active',
            'otp'   => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        // ✅ **NEW: Send verification success notification to user**
        NotificationHelper::createForUser(
            $user->id,
            "Email Verified Successfully",
            "Your email has been verified. You can now login to your account.",
            'success',
            route('login')
        );
        
        // ✅ **NEW: Notify admins about successful verification**
        $admins = User::where('role_id', 1)->active()->get();
        foreach ($admins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "User Email Verified",
                "User {$user->name} ({$user->email}) has verified their email",
                'info',
                route('verify.email', $user->id)
            );
        }

        return redirect()->route('login')->with('success', 'Email verified! You can now login.');
    }

    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.']);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Send OTP email
        Mail::raw("Your OTP for email verification is: {$otp}. It will expire in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email - Forensic System');
        });

        session(['email' => $user->email]);

        // ✅ **NEW: Send OTP resend notification**
        NotificationHelper::createForUser(
            $user->id,
            "OTP Resent",
            "A new OTP has been sent to your email address",
            'info',
            route('verify.email')
        );

        return response()->json(['success' => true, 'message' => 'OTP sent successfully.']);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|min:6',
            'cnic' => "required|string|max:15|unique:users,cnic,{$user->id}",
            'contact_number' => "required|string|max:15|unique:users,contact_number,{$user->id}",
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldEmail = $user->email;
        $oldName = $user->name;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->cnic = $request->cnic;
        $user->contact_number = $request->contact_number;

        // Handle profile image
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $image = $request->file('profile_image');
            $imageName = time() . ".{$image->getClientOriginalExtension()}";
            $path = $image->storeAs('profiles', $imageName, 'public');
            $user->profile_image = $path;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            
            // ✅ **NEW: Password change notification**
            NotificationHelper::createForUser(
                $user->id,
                "Password Changed",
                "Your account password has been updated successfully",
                'warning',
                route('profile.edit')
            );
        }

        $user->save();

        // ✅ **NEW: Profile update notification**
        NotificationHelper::createForUser(
            $user->id,
            "Profile Updated",
            "Your profile information has been updated successfully",
            'info',
            route('profile.edit')
        );

        // ✅ **NEW: Notify admin if email changed**
        if ($oldEmail !== $request->email) {
            $admins = User::where('role_id', 1)->active()->get();
            foreach ($admins as $admin) {
                NotificationHelper::createForUser(
                    $admin->id,
                    "User Profile Email Changed",
                    "User {$oldName} changed email from {$oldEmail} to {$request->email}",
                    'warning',
                    route('verify.email', $user->id)
                );
            }
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'User '.$request->name.' updated his profile',
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updateRole(Request $request, User $user)
    {
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if ($user->role_id == 1) {
            return redirect()->back()->with('error', 'Cannot change role of admin.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $oldRoleId = $user->role_id;
        $user->role_id = $request->role_id;

        // Generate OTP for verification
        $otp = rand(100000, 999999);  
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(15);
        $user->save();

        // Send verification email
        $verifyUrl = route('verify.email', ['email' => $user->email]);
        Mail::send('auth.verify', ['user' => $user, 'otp' => $otp, 'verifyUrl' => $verifyUrl], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email - Forensic System');
        });

        session(['email' => $user->email]);

        // ✅ **NEW: Role change notification to user**
        $roleName = \App\Models\Role::find($request->role_id)->role_name;
        NotificationHelper::createForUser(
            $user->id,
            "Account Role Updated",
            "Your account role has been changed to {$roleName}. Please verify your email.",
            'warning',
            route('verify.email')
        );

        // ✅ **NEW: Role change notification to admin**
        $adminUser = Auth::user();
        $user->load('role');
        NotificationHelper::createForUser(
            $adminUser->id,
            "User Role Changed",
            "You changed the role of {$user->name} to {$user->role->role_name}",
            'info',
            route('verify.email', $user->id)
        );

        // ✅ **NEW: Notify all admins about role change**
        $allAdmins = User::where('role_id', 1)
            ->where('id', '!=', $adminUser->id)
            ->active()
            ->get();
            
        foreach ($allAdmins as $admin) {
            NotificationHelper::createForUser(
                $admin->id,
                "User Role Modified",
                "Admin {$adminUser->name} changed role of {$user->name} from " . 
                \App\Models\Role::find($oldRoleId)->role_name . " to {$user->role->role_name}",
                'info',
                route('verify.email', $user->id)
            );
        }

        RecentActivities::create([
            'user_id' => Auth::id(),
            'action'  => 'User '.$user->name.' role changed to '.$user->role->role_name,
        ]);

        return redirect()->back()->with('success', 'User role updated successfully!');
    }
}