<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecentActivities;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = User::find(Auth::id()); // logged in user as Eloquent model

        // validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'cnic' => 'required|string|max:15|unique:users,cnic,' . $user->id,
            'contact_number' => 'required|string|max:15|unique:users,contact_number,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // update data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->cnic = $request->cnic;
        $user->contact_number = $request->contact_number;

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/profile'), $imageName);
            $user->profile_image = 'images/profile/' . $imageName;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

             
    RecentActivities::create([
        'user_id' => Auth::id(),
        'action'  => 'User '.$request->name.' update his profile',
    ]);
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
