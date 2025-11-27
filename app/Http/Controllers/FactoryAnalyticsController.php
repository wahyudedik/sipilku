<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use App\Models\FactoryView;
use App\Models\FactoryProduct;
use App\Models\FactoryReview;
use App\Models\Order;
use App\Services\FactoryAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FactoryAnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(FactoryAnalyticsService $analyticsService)
    {
        $this->middleware(['auth', 'role:factory_owner|admin']);
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the factory analytics dashboard
     */
    public function dashboard(Request $request)
    {
        $factoryId = $request->user()->hasRole('admin')
            ? $request->get('factory_id')
            : $request->user()->factory->id;

        if (!$factoryId) {
            return redirect()->back()->with('error', 'Factory not found');
        }

        $factory = Factory::with('factoryType')->findOrFail($factoryId);

        // Date range filter
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        // Get comprehensive analytics data
        $analytics = [
            'overview' => $this->analyticsService->getOverviewMetrics($factoryId, $startDate, $endDate),
            'viewStats' => $this->analyticsService->getViewStatistics($factoryId, $startDate, $endDate),
            'productPopularity' => $this->analyticsService->getProductPopularity($factoryId, $startDate, $endDate),
            'salesData' => $this->analyticsService->getSalesData($factoryId, $startDate, $endDate),
            'performanceMetrics' => $this->analyticsService->getPerformanceMetrics($factoryId, $startDate, $endDate),
            'qualityTrends' => $this->analyticsService->getQualityTrends($factoryId, $startDate, $endDate),
            'locationAnalytics' => $this->analyticsService->getLocationAnalytics($factoryId, $startDate, $endDate),
        ];

        return view('factory.analytics.dashboard', compact('factory', 'analytics', 'startDate', 'endDate'));
    }

    /**
     * Display factory view statistics
     */
    public function viewStatistics(Request $request, $factoryId)
    {
        $factory = Factory::findOrFail($factoryId);

        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $viewStats = $this->analyticsService->getDetailedViewStatistics($factoryId, $startDate, $endDate);

        return view('factory.analytics.view-statistics', compact('factory', 'viewStats', 'startDate', 'endDate'));
    }

    /**
     * Display product popularity analytics
     */
    public function productPopularity(Request $request, $factoryId)
    {
        $factory = Factory::with('factoryType')->findOrFail($factoryId);

        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $productAnalytics = $this->analyticsService->getDetailedProductPopularity($factoryId, $startDate, $endDate);

        return view('factory.analytics.product-popularity', compact('factory', 'productAnalytics', 'startDate', 'endDate'));
    }

    /**
     * Display sales reports
     */
    public function salesReports(Request $request, $factoryId)
    {
        $factory = Factory::findOrFail($factoryId);

        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        $salesReports = $this->analyticsService->getDetailedSalesReports($factoryId, $startDate, $endDate, $groupBy);

        return view('factory.analytics.sales-reports', compact('factory', 'salesReports', 'startDate', 'endDate', 'groupBy'));
    }

    /**
     * Display factory performance metrics
     */
    public function performanceMetrics(Request $request, $factoryId)
    {
        $factory = Factory::findOrFail($factoryId);

        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $performance = $this->analyticsService->getDetailedPerformanceMetrics($factoryId, $startDate, $endDate);

        return view('factory.analytics.performance-metrics', compact('factory', 'performance', 'startDate', 'endDate'));
    }

    /**
     * Display factory comparison reports
     */
    public function comparisonReports(Request $request)
    {
        $factoryTypeId = $request->get('factory_type_id');
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $comparison = $this->analyticsService->getFactoryComparisonReports($factoryTypeId, $startDate, $endDate);

        return view('factory.analytics.comparison-reports', compact('comparison', 'factoryTypeId', 'startDate', 'endDate'));
    }

    /**
     * Display quality ratings and review trends
     */
    public function qualityTrends(Request $request, $factoryId)
    {
        $factory = Factory::findOrFail($factoryId);

        $startDate = $request->get('start_date', Carbon::now()->subDays(90));
        $endDate = $request->get('end_date', Carbon::now());

        $qualityData = $this->analyticsService->getDetailedQualityTrends($factoryId, $startDate, $endDate);

        return view('factory.analytics.quality-trends', compact('factory', 'qualityData', 'startDate', 'endDate'));
    }

    /**
     * Display location-specific analytics
     */
    public function locationAnalytics(Request $request, $factoryId)
    {
        $factory = Factory::with('factoryLocations')->findOrFail($factoryId);

        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        $locationData = $this->analyticsService->getDetailedLocationAnalytics($factoryId, $startDate, $endDate);

        return view('factory.analytics.location-analytics', compact('factory', 'locationData', 'startDate', 'endDate'));
    }

    /**
     * Export analytics data to CSV/PDF
     */
    public function exportReport(Request $request, $factoryId)
    {
        $factory = Factory::findOrFail($factoryId);
        $format = $request->get('format', 'csv'); // csv, pdf, excel
        $reportType = $request->get('report_type', 'overview');

        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        return $this->analyticsService->exportReport($factoryId, $reportType, $format, $startDate, $endDate);
    }
}
