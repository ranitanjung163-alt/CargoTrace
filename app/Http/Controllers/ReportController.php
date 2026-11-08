<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Shipment::query();
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Date Range Filter
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now();

        $query->whereBetween('created_at', [$startDate, $endDate]);

        $totalShipments = (clone $query)->count();
        $deliveredShipments = (clone $query)->where('current_status', Shipment::STATUS_DELIVERED)->count();
        $delayedShipments = (clone $query)->where('is_delayed', true)->count();
        $onTimeShipments = (clone $query)->where('is_delayed', false)->where('current_status', Shipment::STATUS_DELIVERED)->count();

        // 1. On-Time Delivery Rate (%)
        $onTimeRate = $deliveredShipments > 0
            ? round(($onTimeShipments / $deliveredShipments) * 100, 1)
            : ($totalShipments > 0 ? round((($totalShipments - $delayedShipments) / $totalShipments) * 100, 1) : 100);

        // 2. Average Delay Duration (hours)
        $avgDelayMinutes = (clone $query)->where('is_delayed', true)->avg('delay_duration_minutes') ?? 0;
        $avgDelayHours = round($avgDelayMinutes / 60, 1);

        // 3. Exception Statistics & Resolution Time
        $exceptionsQuery = ShipmentException::whereBetween('detected_at', [$startDate, $endDate]);
        if ($user->isCustomer()) {
            $exceptionsQuery->whereHas('shipment', fn ($q) => $q->where('customer_id', $user->id));
        }

        $totalExceptions = (clone $exceptionsQuery)->count();
        $resolvedExceptions = (clone $exceptionsQuery)->where('status', ShipmentException::STATUS_RESOLVED)->get();

        $totalResolutionHours = 0;
        foreach ($resolvedExceptions as $rex) {
            if ($rex->resolved_at && $rex->detected_at) {
                $totalResolutionHours += $rex->detected_at->diffInHours($rex->resolved_at);
            }
        }
        $avgResolutionHours = count($resolvedExceptions) > 0 ? round($totalResolutionHours / count($resolvedExceptions), 1) : 0;

        // Carrier Performance Table
        $carrierPerformance = [];
        $carriers = Carrier::where('is_active', true)->get();
        foreach ($carriers as $carrier) {
            $cQuery = (clone $query)->where('carrier_id', $carrier->id);
            $cTotal = (clone $cQuery)->count();
            $cDelayed = (clone $cQuery)->where('is_delayed', true)->count();
            $cDelivered = (clone $cQuery)->where('current_status', Shipment::STATUS_DELIVERED)->count();

            if ($cTotal > 0) {
                $carrierPerformance[] = [
                    'name' => $carrier->name,
                    'code' => $carrier->code,
                    'service_type' => $carrier->service_type,
                    'total' => $cTotal,
                    'delayed' => $cDelayed,
                    'delivered' => $cDelivered,
                    'on_time_rate' => $cTotal > 0 ? round((($cTotal - $cDelayed) / $cTotal) * 100, 1) : 100,
                ];
            }
        }

        // Exceptions Breakdown by Type
        $exceptionsByType = (clone $exceptionsQuery)
            ->selectRaw('exception_type, count(*) as count')
            ->groupBy('exception_type')
            ->pluck('count', 'exception_type')
            ->toArray();

        return view('reports.index', [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'totalShipments' => $totalShipments,
            'deliveredShipments' => $deliveredShipments,
            'delayedShipments' => $delayedShipments,
            'onTimeRate' => $onTimeRate,
            'avgDelayHours' => $avgDelayHours,
            'totalExceptions' => $totalExceptions,
            'resolvedExceptionsCount' => count($resolvedExceptions),
            'avgResolutionHours' => $avgResolutionHours,
            'carrierPerformance' => $carrierPerformance,
            'exceptionsByType' => $exceptionsByType,
        ]);
    }
}

