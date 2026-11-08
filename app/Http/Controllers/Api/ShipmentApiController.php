<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShipmentApiController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected TrackingService $trackingService,
        protected ExceptionService $exceptionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Shipment::with(['customer:id,name,email', 'carrier:id,name,code', 'origin:id,name,city,country', 'destination:id,name,city,country']);

        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->input('status'));
        }

        if ($request->filled('carrier_id')) {
            $query->where('carrier_id', $request->input('carrier_id'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(fn ($q) => $q->where('shipment_number', 'like', "%{$s}%")->orWhere('cargo_type', 'like', "%{$s}%"));
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $shipments = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Shipments retrieved successfully.',
            'data' => $shipments->items(),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Customers cannot create shipments.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'shipment_number' => ['nullable', 'string', 'max:50', 'unique:shipments,shipment_number'],
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_location_id' => ['required', 'exists:locations,id', 'different:destination_location_id'],
            'destination_location_id' => ['required', 'exists:locations,id'],
            'cargo_type' => ['required', 'string', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'volume' => ['required', 'numeric', 'min:0.01'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'estimated_arrival' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $shipment = $this->shipmentService->createShipment(
            $validator->validated(),
            $request->input('items', []),
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Shipment created successfully.',
            'data' => $shipment,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $shipment = Shipment::with([
            'customer:id,name,email',
            'carrier',
            'origin',
            'destination',
            'currentLocation',
            'items',
            'trackingEvents',
            'exceptions',
        ])->find($id);

        if (! $shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You do not have access to this shipment.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Customers cannot update shipments.'], 403);
        }

        $shipment = Shipment::find($id);
        if (! $shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'carrier_id' => ['sometimes', 'exists:carriers,id'],
            'cargo_type' => ['sometimes', 'string', 'max:100'],
            'weight' => ['sometimes', 'numeric', 'min:0.1'],
            'volume' => ['sometimes', 'numeric', 'min:0.01'],
            'expected_departure' => ['sometimes', 'date'],
            'expected_arrival' => ['sometimes', 'date'],
            'estimated_arrival' => ['sometimes', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error.', 'errors' => $validator->errors()], 422);
        }

        $updated = $this->shipmentService->updateShipment($shipment, $validator->validated(), $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Shipment updated successfully.',
            'data' => $updated,
        ], 200);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Only administrators can delete shipments.'], 403);
        }

        $shipment = Shipment::find($id);
        if (! $shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $this->shipmentService->deleteShipment($shipment, $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Shipment deleted successfully.',
        ], 200);
    }

    public function tracking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $shipment = Shipment::find($id);

        if (! $shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You do not have access to this tracking data.'], 403);
        }

        $events = $shipment->trackingEvents()->with('creator:id,name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shipment_number' => $shipment->shipment_number,
                'current_status' => $shipment->current_status,
                'events' => $events,
            ],
        ], 200);
    }

    public function addTracking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Customers cannot add tracking events.'], 403);
        }

        $shipment = Shipment::find($id);
        if (! $shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'event_type' => ['required', 'string', 'max:50'],
            'event_time' => ['required', 'date'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name' => ['required', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90.0,90.0'],
            'longitude' => ['nullable', 'numeric', 'between:-180.0,180.0'],
            'status_after_event' => ['nullable', 'string', 'in:' . implode(',', Shipment::STATUSES)],
            'description' => ['required', 'string', 'max:1000'],
            'is_exception' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error.', 'errors' => $validator->errors()], 422);
        }

        try {
            $event = $this->trackingService->addEvent($shipment, $validator->validated(), $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Tracking event logged successfully.',
                'data' => $event,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function exceptions(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $shipment = Shipment::find($id);

        if (! $shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $exceptions = $shipment->exceptions()->with(['creator:id,name', 'resolver:id,name'])->get();

        return response()->json([
            'success' => true,
            'data' => $exceptions,
        ], 200);
    }

    public function addException(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Customers cannot report exceptions.'], 403);
        }

        $shipment = Shipment::find($id);
        if (! $shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'exception_type' => ['required', 'string', 'in:' . implode(',', ShipmentException::TYPES)],
            'severity' => ['required', 'string', 'in:LOW,MEDIUM,HIGH'],
            'description' => ['required', 'string', 'max:1000'],
            'detected_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error.', 'errors' => $validator->errors()], 422);
        }

        try {
            $exception = $this->exceptionService->createException($shipment, $validator->validated(), $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Exception logged successfully.',
                'data' => $exception,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
