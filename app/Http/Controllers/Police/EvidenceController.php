<?php

namespace App\Http\Controllers\Police;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\RecentActivities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    // // Show all uploaded evidence
    // public function index()
    // {
    //     $user = Auth::user();
    //     $evidences = Media::where('user_id', $user->id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('police.upload-evidence', compact('evidences'));
    // }

    
    // // Delete evidence
    // public function destroy($id)
    // {
        //     $evidence = Media::findOrFail($id);
        
        //     if (Storage::disk('public')->exists($evidence->file_path)) {
            //         Storage::disk('public')->delete($evidence->file_path);
            //     }
            
            //     $evidence->delete();
            
            //     return redirect()->back()->with('success', 'Evidence deleted successfully.');
            // }
            
            
            public function index()
            {
        // Sare media files database se lao

        $media = \App\Models\Media::where('user_id', Auth::id())->paginate(5);
        foreach ($media as $file) {
            $absolutePath = Storage::disk('public')->path($file->file_path);
            
            $file->size = file_exists($absolutePath) 
            ? filesize($absolutePath) 
            : 0;
        }
        
        
        return view('police.upload-evidence', compact('media'));
    }
    // Store uploaded files
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // max 10MB
            'complaint_id' => 'required|numeric',
            'file_type' => 'required|string',
        ]);

        $officerId = Auth::check() ? Auth::id() : null;
   foreach ($request->file('files') as $file) {
        if ($file->isValid()) {
            $path = $file->store('media_uploads', 'public');

            // Detect file type from extension
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $fileType = 'image';
            } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'mkv'])) {
                $fileType = 'video';
            } elseif (in_array($extension, ['mp3', 'wav', 'aac'])) {
                $fileType = 'audio';
            } elseif (in_array($extension, ['zip', 'rar'])) {
                $fileType = 'archive';
            } else {
                $fileType = 'document'; // pdf, doc, docx, txt, xlsx etc.
            }
                Media::create([
                    'user_id' => $officerId,
                    'file_type' => $request->file_type,
                    'file_path' => $path,
                    'complaint_id' => $request->complaint_id,
                    'status' => 'pending',
                ]);
                 // Log upload in activity
                    RecentActivities::create([
                        'user_id' => $officerId,
                        'action'  => 'Uploaded ' . $fileType . ' evidence for FIR.',
                    ]);
            }
        }



                   
              
    


        return redirect()->back()->with('success', 'Evidence uploaded successfully!');
    }


// 🔎 Search API
public function search(Request $request)
{
    $query = $request->q;

    $media = Media::with('complaint')
        ->where('file_path', 'LIKE', "%$query%")
        ->orWhere('description','LIKE',"%$query%")
        ->orWhereHas('complaint', function ($q) use ($query) {
            $q->where('track_id', 'LIKE', "%$query%");
        })
        ->get();

    $html = view('admin.partials.media-search', compact('media'))->render();

    return response()->json(['html' => $html]);
}


// 🔄 Update Status
public function updateStatus(Request $request, $id)
{
    $request->validate(['status' => 'required|in:pending,approved,rejected']);

    $media = Media::findOrFail($id);
    $media->status = $request->status;
    $media->save();

    return back()->with('success', 'Status updated successfully!');
}

// ❌ Delete File
public function destroy($id)
{
    $media = Media::findOrFail($id);


    // Delete actual file from storage
    if (Storage::exists($media->file_path)) {
        Storage::delete($media->file_path);
    }
  RecentActivities::create([
        'user_id' => Auth::id(),
        'action'  => 'Media for ' . optional($media->complaint)->track_id . ' has been deleted.',
    ]);
    $media->delete();

    return back()->with('success', 'Media deleted successfully!');
}

}

