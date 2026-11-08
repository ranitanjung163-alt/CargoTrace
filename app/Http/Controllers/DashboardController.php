<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main CargoTrace dashboard.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Base query with role-based scoping
        $shipmentsQuery = Shipment::query();

        if ($user->isCustomer()) {
            $shipmentsQuery->where('customer_id', $user->id);
        }

        // 1. KPI STATISTIC CARDS (Calculated dynamically from database)
        $totalShipments = (clone $shipmentsQuery)->count();
        $inTransit = (clone $shipmentsQuery)->where('current_status', Shipment::STATUS_IN_TRANSIT)->count();
        $delivered = (clone $shipmentsQuery)->where('current_status', Shipment::STATUS_DELIVERED)->count();
        $delayed = (clone $shipmentsQuery)->where('is_delayed', true)->count();

        $exceptionsQuery = ShipmentException::query()->whereIn('status', ['OPEN', 'INVESTIGATING']);
        if ($user->isCustomer()) {
            $exceptionsQuery->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }
        $openExceptions = $exceptionsQuery->count();

        // 2. CHART: SHIPMENTS BY STATUS (Bar Chart)
        $statusCounts = [
            'In Transit' => $inTransit,
            'Delivered' => $delivered,
            'Delayed' => $delayed,
            'Open Exceptions' => $openExceptions,
        ];

        // 3. CHART: DELAY RATE BY CARRIER (Donut Chart)
        // Formula: (Carrier delayed shipments / Carrier total shipments) * 100
        $carrierStats = [];
        $carriers = Carrier::where('is_active', true)->get();

        $totalDelayedAcrossCarriers = 0;
        foreach ($carriers as $carrier) {
            $cQuery = Shipment::where('carrier_id', $carrier->id);
            if ($user->isCustomer()) {
                $cQuery->where('customer_id', $user->id);
            }
            $cTotal = (clone $cQuery)->count();
            $cDelayed = (clone $cQuery)->where('is_delayed', true)->count();

            if ($cTotal > 0) {
                $rate = round(($cDelayed / $cTotal) * 100, 1);
                $carrierStats[] = [
                    'name' => $carrier->name,
                    'total' => $cTotal,
                    'delayed' => $cDelayed,
                    'rate' => $rate,
                ];
                $totalDelayedAcrossCarriers += $cDelayed;
            }
        }

        $overallDelayRate = $totalShipments > 0 ? round(($delayed / $totalShipments) * 100, 1) : 0;

        // 4. MAP DATA: Worldwide Active Shipments with Origin, Destination, Waypoints
        $mapShipments = (clone $shipmentsQuery)
            ->with(['origin', 'destination', 'currentLocation', 'carrier', 'trackingEvents'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'number' => $s->shipment_number,
                    'status' => $s->current_status,
                    'is_delayed' => $s->is_delayed,
                    'carrier' => $s->carrier->name ?? 'Unknown',
                    'origin' => [
                        'name' => $s->origin->name,
                        'city' => $s->origin->city,
                        'country' => $s->origin->country,
                        'lat' => (float) $s->origin->latitude,
                        'lng' => (float) $s->origin->longitude,
                    ],
                    'destination' => [
                        'name' => $s->destination->name,
                        'city' => $s->destination->city,
                        'country' => $s->destination->country,
                        'lat' => (float) $s->destination->latitude,
                        'lng' => (float) $s->destination->longitude,
                    ],
                    'current' => $s->currentLocation ? [
                        'name' => $s->currentLocation->name,
                        'lat' => (float) $s->currentLocation->latitude,
                        'lng' => (float) $s->currentLocation->longitude,
                    ] : null,
                    'tracking_points' => $s->trackingEvents->map(function ($te) {
                        return [
                            'time' => $te->event_time->format('Y-m-d H:i'),
                            'type' => $te->event_type,
                            'name' => $te->location_name,
                            'lat' => (float) $te->latitude,
                            'lng' => (float) $te->longitude,
                            'is_exception' => $te->is_exception,
                            'desc' => $te->description,
                        ];
                    }),
                ];
            });

        // 5. RECENT SHIPMENTS TABLE (Matching lecturer reference structure)
        $recentShipments = (clone $shipmentsQuery)
            ->with(['customer', 'carrier', 'origin', 'destination'])
            ->latest()
            ->paginate(10);

        return view('dashboard.index', [
            'user' => $user,
            'kpis' => [
                'total' => $totalShipments,
                'in_transit' => $inTransit,
                'delivered' => $delivered,
                'delayed' => $delayed,
                'open_exceptions' => $openExceptions,
            ],
            'statusCounts' => $statusCounts,
            'carrierStats' => $carrierStats,
            'overallDelayRate' => $overallDelayRate,
            'mapShipments' => $mapShipments,
            'recentShipments' => $recentShipments,
        ]);
    }
}

