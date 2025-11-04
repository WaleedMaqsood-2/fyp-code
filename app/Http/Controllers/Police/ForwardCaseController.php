<?php
namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseFile;
use App\Models\ForwardedCase;
use App\Models\ForensicAnalyst;

class ForwardCaseController extends Controller
{
    public function index()
    {
        $cases = CaseFile::where('status', '!=', 'Forwarded')->get();
        $forwardedCases = ForwardedCase::with('analyst')->get();
        $analysts = ForensicAnalyst::all();

        return view('police.forward-case', compact('cases', 'forwardedCases', 'analysts'));
    }

    public function forward(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:case_files,id',
            'analyst_id' => 'required|exists:forensic_analysts,id',
            'remarks' => 'required|string|max:1000',
        ]);

        ForwardedCase::create([
            'case_id' => $validated['case_id'],
            'analyst_id' => $validated['analyst_id'],
            'remarks' => $validated['remarks'],
            'analysis_status' => 'Pending',
        ]);

        CaseFile::where('id', $validated['case_id'])->update(['status' => 'Forwarded']);

        return back()->with('success', 'Case forwarded successfully!');
    }
}
