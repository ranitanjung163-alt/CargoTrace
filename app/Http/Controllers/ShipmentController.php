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

        $query = $this->buildShipmentsQuery($request, $user);

        $shipments = $query->paginate(12)->withQueryString();
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('country')->orderBy('city')->get();

        return view('shipments.index', [
            'shipments' => $shipments,
            'carriers' => $carriers,
            'locations' => $locations,
            'statuses' => Shipment::STATUSES,
        ]);
    }

    /**
     * Build unified query for index and export with customer isolation and comprehensive filtering.
     */
    public function buildShipmentsQuery(Request $request, User $user)
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation']);

        // Customer can only view their own shipments
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Search across shipment number, cargo type, customer name, carrier, and cities
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('carrier', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('origin', fn ($sq) => $sq->where('city', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"))
                  ->orWhereHas('destination', fn ($sq) => $sq->where('city', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $status = strtoupper(trim((string) $request->input('status')));
            if (in_array($status, Shipment::STATUSES, true)) {
                $query->where('current_status', $status);
            }
        }

        // Filter by Carrier
        if ($request->filled('carrier_id')) {
            $carrierId = (int) $request->input('carrier_id');
            if ($carrierId > 0) {
                $query->where('carrier_id', $carrierId);
            }
        }

        // Filter by Origin Location
        if ($request->filled('origin_id')) {
            $originId = (int) $request->input('origin_id');
            if ($originId > 0) {
                $query->where('origin_location_id', $originId);
            }
        }

        // Filter by Destination Location
        if ($request->filled('destination_id')) {
            $destinationId = (int) $request->input('destination_id');
            if ($destinationId > 0) {
                $query->where('destination_location_id', $destinationId);
            }
        }

        // Filter by Delay Status
        if ($request->filled('delayed')) {
            $delayedVal = (string) $request->input('delayed');
            if ($delayedVal === '1' || $delayedVal === 'true') {
                $query->where('is_delayed', true);
            } elseif ($delayedVal === '0' || $delayedVal === 'false') {
                $query->where('is_delayed', false);
            }
        }

        // Filter by Date Range (created_at) with format validation
        if ($request->filled('start_date')) {
            $startDate = trim((string) $request->input('start_date'));
            if (strtotime($startDate) !== false) {
                $query->whereDate('created_at', '>=', $startDate);
            }
        }
        if ($request->filled('end_date')) {
            $endDate = trim((string) $request->input('end_date'));
            if (strtotime($endDate) !== false) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        // Sorting: validate column and direction
        $sortField = $request->input('sort_by') ?? $request->input('sort') ?? 'created_at';
        $rawDirection = $request->input('sort_direction') ?? $request->input('direction') ?? 'desc';
        $sortDirection = strtolower((string) $rawDirection) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['shipment_number', 'created_at', 'expected_arrival', 'current_status', 'weight'];
        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('created_at');
        }

        return $query;
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
     * Export filtered shipments to CSV with anti-formula injection protection.
     */
    public function export(Request $request): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $query = $this->buildShipmentsQuery($request, $user);

        $fileName = 'worldcargo360_shipments_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Sanitize against CSV formula injection (OWASP guideline: prefix =, +, -, @ with ')
            $sanitizeCsv = function ($val) {
                if ($val === null) return '';
                $str = (string) $val;
                if (preg_match('/^[=\+\-\@\t\r]/', $str)) {
                    return "'" . $str;
                }
                return $str;
            };

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

            $query->chunk(100, function ($shipments) use ($handle, $sanitizeCsv) {
                foreach ($shipments as $s) {
                    fputcsv($handle, [
                        $sanitizeCsv($s->shipment_number),
                        $sanitizeCsv($s->customer->name ?? 'N/A'),
                        $sanitizeCsv($s->carrier->name ?? 'N/A'),
                        $sanitizeCsv($s->origin->display_name ?? 'N/A'),
                        $sanitizeCsv($s->destination->display_name ?? 'N/A'),
                        $sanitizeCsv($s->current_status),
                        $s->is_delayed ? 'YES' : 'NO',
                        $sanitizeCsv($s->delay_reason ?? ''),
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
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

