<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackingEventStoreRequest;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\TrackingService;
use App\Services\WeatherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService,
        protected WeatherService $weatherService
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $search = $request->input('shipment_number');
        $shipment = null;
        $weather = null;

        $baseQuery = Shipment::with([
            'customer',
            'carrier',
            'origin',
            'destination',
            'currentLocation',
            'trackingEvents.creator',
            'exceptions',
        ]);

        if ($user->isCustomer()) {
            $baseQuery->where('customer_id', $user->id);
        }

        if ($search) {
            $shipment = (clone $baseQuery)->where('shipment_number', trim($search))->first();
        } else {
            // Default to an active or latest shipment when accessed so the Google Maps directions view immediately renders
            $shipment = (clone $baseQuery)->whereIn('current_status', [
                Shipment::STATUS_IN_TRANSIT,
                Shipment::STATUS_PICKED_UP,
                Shipment::STATUS_BOOKED,
            ])->latest()->first() ?? (clone $baseQuery)->latest()->first();

            if ($shipment) {
                $search = $shipment->shipment_number;
            }
        }

        if ($shipment) {
            $targetLoc = $shipment->currentLocation ?? $shipment->destination;
            if ($targetLoc) {
                $weather = $this->weatherService->getWeather($targetLoc->latitude, $targetLoc->longitude);
            }
        }

        // Consignments list for quick lookup across worldwide fleet corridors
        $activeShipmentsQuery = Shipment::with(['origin', 'destination', 'carrier']);

        if ($user->isCustomer()) {
            $activeShipmentsQuery->where('customer_id', $user->id);
        }

        $activeShipments = $activeShipmentsQuery->latest()->take(20)->get();
        $locations = Location::orderBy('country')->orderBy('city')->get();

        return view('tracking.index', [
            'search' => $search,
            'shipment' => $shipment,
            'weather' => $weather,
            'activeShipments' => $activeShipments,
            'locations' => $locations,
            'statuses' => Shipment::STATUSES,
        ]);
    }

    public function store(TrackingEventStoreRequest $request, Shipment $shipment): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isCustomer()) {
            abort(403, 'Customers cannot add tracking events.');
        }

        try {
            $this->trackingService->addEvent($shipment, $request->validated(), $user->id);

            return redirect()->route('tracking.index', ['shipment_number' => $shipment->shipment_number])
                ->with('success', 'Tracking event logged successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}

