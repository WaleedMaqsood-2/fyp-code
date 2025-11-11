<?php

namespace App\Http\Controllers\ForensicController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Media;


class CaseDetailsController extends Controller
{
    //
    public function CaseDetails($id)
{
    $case = Complaint::with('user')->findOrFail($id);
    $evidences = Media::where('complaint_id', $id)->get();

    return view('forensic_analyst.case-details', compact('case', 'evidences'));
}

}
