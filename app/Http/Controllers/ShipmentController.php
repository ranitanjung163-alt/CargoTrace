<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentStoreRequest;
use App\Http\Requests\ShipmentUpdateRequest;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use App\Services\WeatherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected WeatherService $weatherService
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation']);

        // Customer can only view their own shipments
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('carrier', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('origin', fn ($sq) => $sq->where('city', 'like', "%{$search}%"))
                  ->orWhereHas('destination', fn ($sq) => $sq->where('city', 'like', "%{$search}%"));
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('current_status', $request->input('status'));
        }

        // Filter by Carrier
        if ($request->filled('carrier_id')) {
            $query->where('carrier_id', $request->input('carrier_id'));
        }

        // Filter by Delay
        if ($request->filled('delayed')) {
            $query->where('is_delayed', $request->boolean('delayed'));
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSorts = ['shipment_number', 'created_at', 'expected_arrival', 'current_status', 'weight'];
        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $shipments = $query->paginate(12)->withQueryString();
        $carriers = Carrier::where('is_active', true)->get();

        return view('shipments.index', [
            'shipments' => $shipments,
            'carriers' => $carriers,
            'statuses' => Shipment::STATUSES,
        ]);
    }

    public function create(): View
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isCustomer()) {
            abort(403, 'Customers cannot create shipments directly.');
        }

        $customers = User::where('role', User::ROLE_CUSTOMER)->get();
        $carriers = Carrier::where('is_active', true)->get();
        $locations = Location::orderBy('country')->orderBy('city')->get();

        return view('shipments.create', [
            'customers' => $customers,
            'carriers' => $carriers,
            'locations' => $locations,
            'suggestedNumber' => $this->shipmentService->generateShipmentNumber(),
        ]);
    }

    public function store(ShipmentStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $request->input('items', []);

        $shipment = $this->shipmentService->createShipment($validated, $items, Auth::id());

        return redirect()->route('shipments.show', $shipment)
            ->with('success', "Shipment {$shipment->shipment_number} created successfully.");
    }

    public function show(Shipment $shipment): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Data isolation check: Customer can only view their own
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            abort(403, 'Unauthorized access to another customer shipment.');
        }

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'currentLocation',
            'items',
            'trackingEvents.creator',
            'exceptions.creator',
            'exceptions.resolver',
        ]);

        // Get weather at current / destination location
        $targetLoc = $shipment->currentLocation ?? $shipment->destination;
        $weather = $targetLoc
            ? $this->weatherService->getWeather($targetLoc->latitude, $targetLoc->longitude)
            : null;

        return view('shipments.show', [
            'shipment' => $shipment,
            'weather' => $weather,
            'allowedNextStatuses' => Shipment::ALLOWED_TRANSITIONS[$shipment->current_status] ?? [],
        ]);
    }

    public function edit(Shipment $shipment): View
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Customers cannot edit shipments.');
        }

        $carriers = Carrier::where('is_active', true)->get();
        $locations = Location::orderBy('country')->orderBy('city')->get();

        return view('shipments.edit', [
            'shipment' => $shipment,
            'carriers' => $carriers,
            'locations' => $locations,
        ]);
    }

    public function update(ShipmentUpdateRequest $request, Shipment $shipment): RedirectResponse
    {
        $this->shipmentService->updateShipment($shipment, $request->validated(), Auth::id());

        return redirect()->route('shipments.show', $shipment)
            ->with('success', "Shipment {$shipment->shipment_number} updated successfully.");
    }

    public function destroy(Shipment $shipment): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Only administrators can delete shipments.');
        }

        $this->shipmentService->deleteShipment($shipment, $user->id);

        return redirect()->route('shipments.index')
            ->with('success', "Shipment {$shipment->shipment_number} deleted successfully.");
    }

    public function transitionStatus(Request $request, Shipment $shipment): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Customers cannot change shipment status.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', Shipment::STATUSES)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->shipmentService->transitionStatus(
                $shipment,
                $request->input('status'),
                $user->id,
                $request->input('notes')
            );

            return redirect()->route('shipments.show', $shipment)
                ->with('success', "Status transitioned to {$request->input('status')}.");
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('shipments.show', $shipment)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Export shipments to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination']);
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        $fileName = 'cargotrace_shipments_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Shipment Number',
                'Customer',
                'Carrier',
                'Origin',
                'Destination',
                'Status',
                'Is Delayed',
                'Delay Reason',
                'Expected Arrival',
                'Estimated Arrival',
                'Weight (kg)',
                'Volume (cbm)',
            ]);

            $query->chunk(100, function ($shipments) use ($handle) {
                foreach ($shipments as $s) {
                    fputcsv($handle, [
                        $s->shipment_number,
                        $s->customer->name ?? 'N/A',
                        $s->carrier->name ?? 'N/A',
                        $s->origin->display_name ?? 'N/A',
                        $s->destination->display_name ?? 'N/A',
                        $s->current_status,
                        $s->is_delayed ? 'YES' : 'NO',
                        $s->delay_reason ?? '',
                        $s->expected_arrival?->format('Y-m-d H:i') ?? '',
                        $s->estimated_arrival?->format('Y-m-d H:i') ?? '',
                        $s->weight,
                        $s->volume,
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}

