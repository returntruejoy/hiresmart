<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $metrics = $this->dashboardService->getMetrics();

        return response()->json(['data' => $metrics]);
    }

    public function employerStats(Request $request)
    {
        $employerId = $request->user()->id;
        $stats = $this->dashboardService->getEmployerStats($employerId);

        return response()->json($stats);
    }
}
