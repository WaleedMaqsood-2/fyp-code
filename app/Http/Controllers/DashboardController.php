<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Media; // Replace with your actual media model
use App\Models\AiUsage;
use App\Models\PendingSummaries;


class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalMedia = Media::count(); 
        $pendingSummaries = PendingSummaries::countPendingSummaries();
        $aiUsage = AiUsage::countAiUsage();

        return view('admin.dashboard', compact('totalUsers', 'totalMedia', 'pendingSummaries', 'aiUsage'));
    }
}