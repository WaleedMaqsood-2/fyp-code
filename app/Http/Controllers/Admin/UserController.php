<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::paginate(3);
        $roles = \App\Models\Role::all();
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
            ],
            [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'The email has already been taken.',
            'role_id.required' => 'Role is required.',
            'cnic.required' => 'CNIC is required.',
            'cnic.unique' => 'The CNIC has already been taken.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.unique' => 'The contact number has already been taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
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

            \Illuminate\Support\Facades\Mail::send('auth.verify', ['user' => $user, 'otp' => $otp, 'verifyUrl' => $verifyUrl], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email - TS Developers');
            });

            session([
            'email' => $user->email,
            ]);

            return redirect()->back()->with('success', 'User added! Please verify the email.');
        }

        

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required',
            'cnic' => 'required|unique:users,cnic,' . $user->id,
            'contact_number' => 'required|unique:users,contact_number,' . $user->id,
        ],
    [
        'name.required'=>'Name is required.',
        'email.required' => 'Email is required.',
        'email.unique' => 'The email has already been taken.',
        'cnic.required' => 'CNIC is required.',
        'cnic.unique' => 'The CNIC has already been taken.',
        'contact_number.required' => 'Contact number is required.',
        'contact_number.unique' => 'The contact number has already been taken.',
    ]
    );
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->cnic = $request->cnic;
        $user->contact_number = $request->contact_number;
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();
        return back()->with('success', 'User updated!');
    }

    public function destroy($id)
    {
        \App\Models\User::findOrFail($id)->delete();
        return back()->with('success', 'User deleted!');
    }
}
