<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): JsonResponse
    {
        $metrics = $this->dashboardService->getMetrics();

        return $this->successResponse($metrics, 'Admin dashboard metrics retrieved successfully.');
    }

    public function employerStats(): JsonResponse
    {
        $employerId = auth()->id();
        $stats = $this->dashboardService->getEmployerStats($employerId);

        return $this->successResponse($stats, 'Employer statistics retrieved successfully.');
    }
}
