<?php
namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;



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
            if ($user->role_id == 1) {
                return redirect()->route('dashboard')->with('success', 'Welcome '.$user->name);
            } else {
                return redirect()->route('register')->with('success', 'Welcome '.$user->name);
            }
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }
    



// Signup Logic
    public function Register(Request $request)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'cnic' => 'required|string|max:15|unique:users',
            'contact_number' => 'required|string|max:15|unique:users',
            'password' => 'required|min:6|confirmed',
        ],
    [
        'name.required' => 'Name is required.',
        'email.required' => 'Email is required.',
        'email.unique' => 'The email has already been taken.',
        'cnic.required' => 'CNIC is required.',
        'cnic.unique' => 'The CNIC has already been taken.',
        'contact_number.required' => 'Contact number is required.',
        'contact_number.unique' => 'The contact number has already been taken.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 6 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
    ]);

        // $username = strtolower(str_replace(' ', '_', $request->name)) . rand(100, 999);

        // Generate OTP
        $otp = rand(100000, 999999);
$defaultRole = '4';
        $user = User::create([
            'name'        => $request->name,
            // 'user_name'    => $username,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'cnic'       => $request->cnic,
            'contact_number' => $request->contact_number,
            'role_id'        => $defaultRole,
            'status'      => 'active',
            'is_verified' => false, // not verified yet
            'reg_status'  => 'pending',
            'otp'   => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at'=> null,
        ]);


        Mail::raw("Your OTP for email verification is: {$otp}. It will expire in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email - TS Developers');
        });

        session([
            'email' => $user->email,
            // optional, if you also want to compare from session
        ]);

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

        $user->update([
            'is_verified' => true,
            'reg_status'  => 'Registered',
            'status'      => 'active',
            'otp'   => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Email verified! You can now login.');
    }




    

    // public function editProfile()
    // {
    //     $user = Auth::user();
    //     return view('profile.edit', compact('user'));
    // }


    //Profile Update Logic
   public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id()); // Use imported User model

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|min:6',
            'cnic' => "required|string|max:15|unique:users,cnic,{$user->id}",
            'contact_number' => "required|string|max:15|unique:users,contact_number,{$user->id}",
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],
    [
        'name.required' => 'Name is required.',
        'name.max' => 'Name may not be greater than 255 characters.',
        'name.string' => 'Name must be a string.',
        'email.required' => 'Email is required.',
        'email.email' => 'Please provide a valid email address.',
        'cnic.required' => 'CNIC is required.',
        'cnic.unique' => 'The CNIC has already been taken.',
        'contact_number.required' => 'Contact number is required.',
        'contact_number.unique' => 'The contact number has already been taken.',
        'profile_image.image' => 'Profile image must be an image.',
        'profile_image.mimes' => 'Profile image must be a file of type: jpeg, png, jpg, gif.',
        'profile_image.max' => 'Profile image may not be greater than 2MB.',

    ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->cnic = $request->cnic;
        $user->contact_number = $request->contact_number;

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $image = $request->file('profile_image');
            $imageName = time() . ".{$image->getClientOriginalExtension()}";
            $path = $image->storeAs('profiles', $imageName, 'public');
            $user->profile_image = $path;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }


    //change role only admin


public function updateRole(Request $request, User $user)
{
    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    if ($user->role_id == 1) { // Prevent changing role of main admin
        return redirect()->back()->with('error', 'Cannot change role of admin.');
    }

    $request->validate([
        'role_id' => 'required|exists:roles,id',
    ]);

    // Update role
    $user->role_id = $request->role_id;

    // Generate OTP for verification
    $otp = rand(100000, 999999);  
    $user->otp = $otp;
    $user->otp_expires_at = now()->addMinutes(15); // OTP valid for 15 mins
    $user->save();

   
    // ----------------- Send Email to User with OTP -----------------
       $verifyUrl = route('verify.email', ['email' => $user->email]);

    Mail::send('auth.verify', ['user' => $user, 'otp' => $otp, 'verifyUrl' => $verifyUrl], function ($message) use ($user) {
        $message->to($user->email)
            ->subject('Verify Your Email - TS Developers');
    });

    // Optional: Store in session (if you want to check OTP later)
    session([
        'email' => $user->email,
    ]);

    // ----------------- Send Email to Admin -----------------
    $user->load('role'); // Ensure role relationship is loaded
    $adminuser = Auth::user();
    Mail::raw("The role of user {$user->name} ({$user->email}) has been changed to {$user->role->name}.", function ($message) use ($adminuser) {
        $message->to($adminuser->email)
            ->subject('User Role Changed - TS Developers');
    });
    return redirect()->back()->with('success', 'User role updated successfully! Emails sent.');
}

}