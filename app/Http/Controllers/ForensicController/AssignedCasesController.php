<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class AssignedCasesController extends Controller
{
    //
    public function assignedCases()
{
    $analystId = Auth::id();

    $assignedCases = Complaint::with(['officer'])
        ->whereHas('latestStatus', function ($q) use ($analystId) {
            $q->where('forwarded_to', $analystId)
              ->where('status', 'forwarded');
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return view('forensic_analyst.assigned-cases', compact('assignedCases'));
}

}
