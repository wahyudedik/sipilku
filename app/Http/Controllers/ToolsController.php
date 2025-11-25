<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCalculationRequest;
use App\Models\Calculation;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ToolsController extends Controller
{
    /**
     * Display tools navigation page.
     */
    public function index(): View
    {
        $recentCalculations = Auth::check() 
            ? Calculation::where('user_id', Auth::id())
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return view('tools.index', compact('recentCalculations'));
    }

    /**
     * Display RAB Calculator.
     */
    public function rab(): View
    {
        return view('tools.rab');
    }

    /**
     * Calculate RAB.
     */
    public function calculateRab(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $items = $request->items;
        $total = 0;
        $calculatedItems = [];

        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $total += $subtotal;
            $calculatedItems[] = [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $subtotal,
            ];
        }

        return response()->json([
            'success' => true,
            'items' => $calculatedItems,
            'total' => $total,
        ]);
    }

    /**
     * Display Volume Material Calculator.
     */
    public function volumeMaterial(): View
    {
        return view('tools.volume-material');
    }

    /**
     * Calculate Volume Material.
     */
    public function calculateVolumeMaterial(Request $request): JsonResponse
    {
        $request->validate([
            'shape' => 'required|string|in:rectangle,circle,triangle,trapezoid',
            'dimensions' => 'required|array',
        ]);

        $shape = $request->shape;
        $dimensions = $request->dimensions;
        $volume = 0;

        switch ($shape) {
            case 'rectangle':
                $volume = ($dimensions['length'] ?? 0) * ($dimensions['width'] ?? 0) * ($dimensions['height'] ?? 0);
                break;
            case 'circle':
                $radius = $dimensions['radius'] ?? 0;
                $height = $dimensions['height'] ?? 0;
                $volume = M_PI * pow($radius, 2) * $height;
                break;
            case 'triangle':
                $base = $dimensions['base'] ?? 0;
                $height = $dimensions['height'] ?? 0;
                $length = $dimensions['length'] ?? 0;
                $volume = (0.5 * $base * $height) * $length;
                break;
            case 'trapezoid':
                $top = $dimensions['top'] ?? 0;
                $bottom = $dimensions['bottom'] ?? 0;
                $height = $dimensions['height'] ?? 0;
                $length = $dimensions['length'] ?? 0;
                $volume = (0.5 * ($top + $bottom) * $height) * $length;
                break;
        }

        return response()->json([
            'success' => true,
            'volume' => round($volume, 2),
            'unit' => 'm³',
        ]);
    }

    /**
     * Display Struktur Calculator.
     */
    public function struktur(): View
    {
        return view('tools.struktur');
    }

    /**
     * Calculate Struktur.
     */
    public function calculateStruktur(Request $request): JsonResponse
    {
        $request->validate([
            'beam_length' => 'required|numeric|min:0',
            'beam_width' => 'required|numeric|min:0',
            'beam_height' => 'required|numeric|min:0',
            'column_count' => 'required|integer|min:0',
            'column_side' => 'required|numeric|min:0',
            'column_height' => 'required|numeric|min:0',
            'concrete_price' => 'required|numeric|min:0',
            'steel_price' => 'required|numeric|min:0',
        ]);

        $beamLength = $request->beam_length;
        $beamWidth = $request->beam_width;
        $beamHeight = $request->beam_height;
        $columnCount = $request->column_count;
        $columnSide = $request->column_side;
        $columnHeight = $request->column_height;
        $concretePrice = $request->concrete_price;
        $steelPrice = $request->steel_price;

        // Calculate volumes
        $beamVolume = $beamLength * $beamWidth * $beamHeight;
        $columnVolume = $columnCount * pow($columnSide, 2) * $columnHeight;
        $totalVolume = $beamVolume + $columnVolume;

        // Estimate steel (assume 100 kg per m³ concrete)
        $steelWeight = $totalVolume * 100; // kg
        $steelCost = $steelWeight * $steelPrice;

        // Concrete cost
        $concreteCost = $totalVolume * $concretePrice;

        // Total cost
        $totalCost = $concreteCost + $steelCost;

        return response()->json([
            'success' => true,
            'beam_volume' => round($beamVolume, 2),
            'column_volume' => round($columnVolume, 2),
            'total_volume' => round($totalVolume, 2),
            'steel_weight' => round($steelWeight, 2),
            'concrete_cost' => round($concreteCost, 2),
            'steel_cost' => round($steelCost, 2),
            'total_cost' => round($totalCost, 2),
        ]);
    }

    /**
     * Display Pondasi Calculator.
     */
    public function pondasi(): View
    {
        return view('tools.pondasi');
    }

    /**
     * Calculate Pondasi.
     */
    public function calculatePondasi(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:footing,strip,raft',
            'dimensions' => 'required|array',
            'concrete_price' => 'required|numeric|min:0',
            'rebar_price' => 'required|numeric|min:0',
        ]);

        $type = $request->type;
        $dimensions = $request->dimensions;
        $concretePrice = $request->concrete_price;
        $rebarPrice = $request->rebar_price;

        $volume = 0;
        $rebarWeight = 0;

        switch ($type) {
            case 'footing':
                $length = $dimensions['length'] ?? 0;
                $width = $dimensions['width'] ?? 0;
                $height = $dimensions['height'] ?? 0;
                $count = $dimensions['count'] ?? 1;
                $volume = $length * $width * $height * $count;
                $rebarWeight = $volume * 80; // kg per m³
                break;
            case 'strip':
                $length = $dimensions['length'] ?? 0;
                $width = $dimensions['width'] ?? 0;
                $height = $dimensions['height'] ?? 0;
                $volume = $length * $width * $height;
                $rebarWeight = $volume * 100; // kg per m³
                break;
            case 'raft':
                $length = $dimensions['length'] ?? 0;
                $width = $dimensions['width'] ?? 0;
                $thickness = $dimensions['thickness'] ?? 0;
                $volume = $length * $width * $thickness;
                $rebarWeight = $volume * 120; // kg per m³
                break;
        }

        $concreteCost = $volume * $concretePrice;
        $rebarCost = $rebarWeight * $rebarPrice;
        $totalCost = $concreteCost + $rebarCost;

        return response()->json([
            'success' => true,
            'volume' => round($volume, 2),
            'rebar_weight' => round($rebarWeight, 2),
            'concrete_cost' => round($concreteCost, 2),
            'rebar_cost' => round($rebarCost, 2),
            'total_cost' => round($totalCost, 2),
        ]);
    }

    /**
     * Display Estimasi Waktu Proyek Calculator.
     */
    public function estimasiWaktu(): View
    {
        return view('tools.estimasi-waktu');
    }

    /**
     * Calculate Estimasi Waktu Proyek.
     */
    public function calculateEstimasiWaktu(Request $request): JsonResponse
    {
        $request->validate([
            'activities' => 'required|array|min:1',
            'activities.*.name' => 'required|string',
            'activities.*.duration' => 'required|numeric|min:0',
            'activities.*.workers' => 'required|integer|min:1',
            'activities.*.dependencies' => 'nullable|array',
        ]);

        $activities = $request->activities;
        $totalDuration = 0;
        $totalWorkDays = 0;

        // Simple calculation: sum of all activities
        foreach ($activities as $activity) {
            $activityDuration = $activity['duration'];
            $workers = $activity['workers'];
            $workDays = $activityDuration / $workers;
            $totalDuration += $activityDuration;
            $totalWorkDays += $workDays;
        }

        // Estimate project duration (considering dependencies and parallel work)
        $estimatedDays = max($totalWorkDays, $totalDuration / max(array_column($activities, 'workers')));

        return response()->json([
            'success' => true,
            'total_duration' => round($totalDuration, 2),
            'total_work_days' => round($totalWorkDays, 2),
            'estimated_days' => round($estimatedDays, 2),
            'estimated_weeks' => round($estimatedDays / 7, 2),
            'estimated_months' => round($estimatedDays / 30, 2),
        ]);
    }

    /**
     * Display Overhead & Profit Calculator.
     */
    public function overheadProfit(): View
    {
        return view('tools.overhead-profit');
    }

    /**
     * Calculate Overhead & Profit.
     */
    public function calculateOverheadProfit(Request $request): JsonResponse
    {
        $request->validate([
            'direct_cost' => 'required|numeric|min:0',
            'overhead_percentage' => 'required|numeric|min:0|max:100',
            'profit_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $directCost = $request->direct_cost;
        $overheadPercentage = $request->overhead_percentage;
        $profitPercentage = $request->profit_percentage;

        $overhead = $directCost * ($overheadPercentage / 100);
        $subtotal = $directCost + $overhead;
        $profit = $subtotal * ($profitPercentage / 100);
        $total = $subtotal + $profit;

        return response()->json([
            'success' => true,
            'direct_cost' => round($directCost, 2),
            'overhead' => round($overhead, 2),
            'subtotal' => round($subtotal, 2),
            'profit' => round($profit, 2),
            'total' => round($total, 2),
        ]);
    }

    /**
     * Save calculation to history.
     */
    public function save(SaveCalculationRequest $request): JsonResponse
    {
        $calculation = Calculation::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'title' => $request->title ?? $request->type,
            'inputs' => $request->inputs,
            'results' => $request->results,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perhitungan berhasil disimpan',
            'calculation' => $calculation,
        ]);
    }

    /**
     * Display calculation history.
     */
    public function history(Request $request): View
    {
        $query = Calculation::where('user_id', Auth::id())
            ->latest();

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $calculations = $query->paginate(20)->withQueryString();

        return view('tools.history', compact('calculations'));
    }

    /**
     * Show calculation detail.
     */
    public function show(Calculation $calculation): View
    {
        if ($calculation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tools.show', compact('calculation'));
    }

    /**
     * Delete calculation.
     */
    public function destroy(Calculation $calculation): RedirectResponse
    {
        if ($calculation->user_id !== Auth::id()) {
            abort(403);
        }

        $calculation->delete();

        return redirect()->route('tools.history')
            ->with('success', 'Perhitungan berhasil dihapus');
    }
}
