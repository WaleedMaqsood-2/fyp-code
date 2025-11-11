<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function dashboard()
{
    $analystId = Auth::id();

    $totalCases = Complaint::where('assigned_to', $analystId)->count();
    $pendingCases = Complaint::where('assigned_to', $analystId)->where('status', 'under_analysis')->count();
    $completedCases = Complaint::where('assigned_to', $analystId)->where('status', 'completed')->count();
    $highPriority = Complaint::where('assigned_to', $analystId)->where('severity', 'high')->count();
    $underReview = Complaint::where('assigned_to', $analystId)->where('status', 'under_review')->count();

    return view('forensic_analyst.dashboard', compact('totalCases', 'pendingCases', 'completedCases', 'highPriority', 'underReview'));
}

}
