<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Services\FactoryAnalyticsService;
use Illuminate\Http\Request;
use App\Models\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FactoryAnalyticsController extends Controller
{
    protected $analytics;

    public function __construct(FactoryAnalyticsService $analytics)
    {
        $this->analytics = $analytics;
        $this->middleware('auth');
    }

    public function index(Factory $factory)
    {
        return view('factories.analytics.index', compact('factory'));
    }

    public function dashboard(Factory $factory, Request $request): JsonResponse
    {
        $rangeDays = (int) $request->get('range', 30);
        $data = $this->analytics->getDashboardSummary($factory->uuid, $rangeDays);
        return response()->json($data);
    }

    public function productPopularityByType($factoryTypeId, Request $request): JsonResponse
    {
        $range = (int) $request->get('range', 30);
        $limit = (int) $request->get('limit', 10);
        $data = $this->analytics->getProductPopularityByFactoryType($factoryTypeId, $range, $limit);
        return response()->json($data);
    }

    public function salesReport(Factory $factory, Request $request): JsonResponse
    {
        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to = $request->get('to', now()->toDateString());
        $report = $this->analytics->getSalesReport($factory->uuid, $from, $to);
        return response()->json($report);
    }

    public function compareByType($factoryTypeId, Request $request): JsonResponse
    {
        $options = $request->only(['from', 'to', 'limit']);
        $data = $this->analytics->getFactoryComparisonByType($factoryTypeId, $options);
        return response()->json($data);
    }

    public function reviewTrends(Factory $factory, Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $data = $this->analytics->getQualityAndReviewTrends($factory->uuid, $days);
        return response()->json($data);
    }
}
