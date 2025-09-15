<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Media;
use App\Models\AIFeedback;
use App\Models\AnalyticsReport;

class UserController extends Controller
{
    // AJAX user search for admin




   
// public function ajaxUserSearch(Request $request)
// {
//     // If an id is provided -> return single user detail or single-card (depending on mode)
//     if ($request->has('id')) {
//         $user = User::with('role')->find($request->id);
//         if (! $user) {
//             return response('', 404);
//         }

//         // If caller asks for card HTML (replace cards)
//         if ($request->get('mode') === 'cards') {
//             return view('admin.partials.user-cards', ['users' => collect([$user])])->render();
//         }

//         // otherwise return details HTML (if you need it)
//         return view('admin.partials.user-details', compact('user'))->render();
//     }

//     $query = $request->get('q', '');

//     // Build search - name, email or role.role_name
//     $usersQuery = User::with('role')
//         ->where(function($q) use ($query) {
//             $q->where('name', 'like', "%{$query}%")
//               ->orWhere('email', 'like', "%{$query}%")
//               ->orWhereHas('role', function($rq) use ($query) {
//                   $rq->where('role_name', 'like', "%{$query}%");
//               });
//         });

//     // limit for suggestions / cards
//     $users = $usersQuery->limit(50)->get([
//         'id','name','email','role_id','profile_image','contact_number','cnic','status','reg_status'
//     ]);

//     // suggestions (JSON)
//     if ($request->get('mode') === 'suggestion') {
//         // send minimal data for suggestions
//         $payload = $users->map(function($u){
//             return [
//                 'id' => $u->id,
//                 'name' => $u->name,
//                 'email' => $u->email,
//                 'role' => $u->role->role_name ?? null,
//             ];
//         });
//         return response()->json(['users' => $payload]);
//     }

//     // cards (HTML partial)
//     if ($request->get('mode') === 'cards') {
//         return view('admin.partials.user-cards', ['users' => $users])->render();
//     }

//     // fallback: full search results HTML (if you use modal or separate view)
//     return view('admin.partials.user-search-results', compact('users'))->render();
// }


public function ajaxUserSearch(Request $request)
{
    if ($request->has('id')) {
        $user = User::with('role')->find($request->id);
        $roles = Role::all(); // 👈 add this
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

    $roles = Role::all(); // 👈 add this

    // Agar suggestions ke liye
    if ($request->get('mode') === 'suggestion') {
        return response()->json(['users' => $users]);
    }

    // Agar cards chahiye
    if ($request->get('mode') === 'cards') {
        return view('admin.partials.user-cards', compact('users', 'roles'));
    }

    // Agar search results (modal ya purana system)
    return view('admin.partials.user-search-results', compact('users', 'roles'));
}




public function ajaxUserList(Request $request)
{
    $query = $request->get('q');

    $users = User::with('role')
        ->when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ;
        })
        ->get();

    return view('admin.partials.user-cards', compact('users'))->render();
}



public function ajaxMediaSearch(Request $request)
{
    // Agar ek media ki detail mangi gayi hai (click kiya suggestion par)
    if ($request->has('id')) {
        $media = Media::find($request->id);
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

    // 👇 Suggestion ke liye JSON
    if ($request->get('mode') === 'suggestion') {
        return response()->json(['media' => $mediaFiles]);
    }

    // 👇 Enter button ya search button ke liye HTML
    return view('admin.partials.media-search-results', compact('mediaFiles'));
}




 
 

public function ajaxAISearch(Request $request)
{
    if ($request->has('id')) {
        $feedback = AIFeedback::find($request->id);
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



// public function ajaxAnalyticsSearch(Request $request)
// {
//     if ($request->has('id')) {
//         $report = AnalyticsReport::find($request->id);
//         return view('admin.partials.analytics-details', compact('report'));
//     }

//     $query = $request->get('q');
//     $reports = AnalyticsReport::where('name', 'like', "%{$query}%")
//         ->orWhere('summary', 'like', "%{$query}%")
//         ->limit(10)
//         ->get(['id','name','summary']);

//     if ($request->ajax() || $request->wantsJson()) {
//         return response()->json(['analytics' => $reports]);
//     }

//     return view('admin.partials.analytics-search-results', compact('reports'))->render();
// }

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
