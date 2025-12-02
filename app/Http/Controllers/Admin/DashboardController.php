<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Receipt;
use App\Services\DrawingService;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_receipts' => Receipt::count(),
            'approved_receipts' => Receipt::where('status', 'approved')->count(),
            'rejected_receipts' => Receipt::where('status', 'rejected')->count(),
        ];

        $drawingService = new DrawingService();
        $periods = $drawingService->getAllPeriods();

        return view('admin.dashboard', compact('stats', 'periods'));
    }
}