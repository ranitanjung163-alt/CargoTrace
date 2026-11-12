<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShipmentService
{
    public function __construct(
        protected EtaService $etaService
    ) {}

    /**
     * Generate unique shipment number in format: SHP-YYYY-XXXXXX
     */
    public function generateShipmentNumber(): string
    {
        $year = date('Y');
        $random = mt_rand(100000, 999999);
        $number = "SHP-{$year}-{$random}";

        while (Shipment::where('shipment_number', $number)->exists()) {
            $random = mt_rand(100000, 999999);
            $number = "SHP-{$year}-{$random}";
        }

        return $number;
    }

    /**
     * Create a new shipment with items and initial tracking event inside a transaction.
     */
    public function createShipment(array $data, array $items, int $userId): Shipment
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            if (empty($data['shipment_number'])) {
                $data['shipment_number'] = $this->generateShipmentNumber();
            }

            $data['current_status'] = Shipment::STATUS_CREATED;
            $data['current_location_id'] = $data['origin_location_id'];

            // Initialize expected / estimated arrivals
            $expectedArrival = Carbon::parse($data['expected_arrival']);
            $estimatedArrival = isset($data['estimated_arrival']) ? Carbon::parse($data['estimated_arrival']) : $expectedArrival->copy();

            $data['expected_arrival'] = $expectedArrival;
            $data['estimated_arrival'] = $estimatedArrival;

            $shipment = Shipment::create($data);

            // Create items
            foreach ($items as $item) {
                if (! empty($item['item_name'])) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'item_name' => $item['item_name'],
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'unit' => $item['unit'] ?? 'units',
                        'weight' => isset($item['weight']) ? (float) $item['weight'] : null,
                        'hs_code' => $item['hs_code'] ?? null,
                        'description' => $item['description'] ?? null,
                    ]);
                }
            }

            // Create initial tracking event
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => 'CREATED',
                'event_time' => now(),
                'location_id' => $shipment->origin_location_id,
                'location_name' => $shipment->origin->name ?? 'Origin Hub',
                'latitude' => $shipment->origin->latitude ?? null,
                'longitude' => $shipment->origin->longitude ?? null,
                'status_after_event' => Shipment::STATUS_CREATED,
                'description' => 'Shipment order created and registered in WorldCargo 360 system.',
                'is_exception' => false,
                'created_by' => $userId,
            ]);

            // Evaluate ETA / delay
            $this->etaService->evaluateDelay($shipment);

            // Send In-App Notification to Customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "Shipment {$shipment->shipment_number} Created",
                'message' => "Your shipment from {$shipment->origin->city} to {$shipment->destination->city} has been created.",
                'type' => 'SHIPMENT_CREATED',
                'is_read' => false,
            ]);

            // Record Audit Log
            AuditLog::record(
                action: 'SHIPMENT_CREATED',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: null,
                newValues: ['shipment_number' => $shipment->shipment_number, 'status' => 'CREATED'],
                userId: $userId
            );

            return $shipment->fresh(['items', 'origin', 'destination', 'carrier', 'customer']);
        });
    }

    /**
     * Update shipment details and re-evaluate delay.
     */
    public function updateShipment(Shipment $shipment, array $data, int $userId): Shipment
    {
        return DB::transaction(function () use ($shipment, $data, $userId) {
            $oldValues = $shipment->only([
                'expected_arrival',
                'estimated_arrival',
                'cargo_type',
                'weight',
                'volume',
            ]);

            $shipment->update($data);

            // Re-evaluate delay
            $this->etaService->evaluateDelay($shipment);

            // Record Audit Log
            AuditLog::record(
                action: 'SHIPMENT_UPDATED',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: $oldValues,
                newValues: $shipment->only(array_keys($oldValues)),
                userId: $userId
            );

            return $shipment->fresh();
        });
    }

    /**
     * Transition shipment status strictly following the business lifecycle rules.
     */
    public function transitionStatus(Shipment $shipment, string $newStatus, int $userId, ?string $notes = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $newStatus, $userId, $notes) {
            $oldStatus = $shipment->current_status;

            if ($shipment->isTerminal()) {
                throw new InvalidArgumentException(
                    "Shipment {$shipment->shipment_number} has reached terminal status '{$oldStatus}' and cannot be transitioned."
                );
            }

            if (! $shipment->canTransitionTo($newStatus)) {
                $allowed = implode(', ', Shipment::ALLOWED_TRANSITIONS[$oldStatus] ?? []);
                throw new InvalidArgumentException(
                    "Invalid status transition from '{$oldStatus}' to '{$newStatus}'. Allowed transition(s): [{$allowed}]."
                );
            }

            $updateData = ['current_status' => $newStatus];

            if ($newStatus === Shipment::STATUS_PICKED_UP && empty($shipment->actual_departure)) {
                $updateData['actual_departure'] = now();
            }

            if ($newStatus === Shipment::STATUS_DELIVERED) {
                $updateData['actual_delivered_at'] = now();
                $updateData['current_location_id'] = $shipment->destination_location_id;
            }

            $shipment->update($updateData);

            // Log corresponding tracking event
            $loc = $newStatus === Shipment::STATUS_DELIVERED ? $shipment->destination : ($shipment->currentLocation ?? $shipment->origin);
            $eventDesc = $notes ?? "Shipment status changed from {$oldStatus} to {$newStatus}.";

            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => $newStatus,
                'event_time' => now(),
                'location_id' => $loc->id ?? null,
                'location_name' => $loc->name ?? 'Hub',
                'latitude' => $loc->latitude ?? null,
                'longitude' => $loc->longitude ?? null,
                'status_after_event' => $newStatus,
                'description' => $eventDesc,
                'is_exception' => false,
                'created_by' => $userId,
            ]);

            // Notify customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "Shipment {$shipment->shipment_number} is now {$newStatus}",
                'message' => $eventDesc,
                'type' => 'STATUS_UPDATED',
                'is_read' => false,
            ]);

            // Record Audit Log
            AuditLog::record(
                action: 'STATUS_TRANSITION',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $newStatus, 'notes' => $notes],
                userId: $userId
            );

            return $shipment->fresh();
        });
    }

    /**
     * Delete shipment with safety checks.
     */
    public function deleteShipment(Shipment $shipment, int $userId): bool
    {
        return DB::transaction(function () use ($shipment, $userId) {
            $number = $shipment->shipment_number;

            AuditLog::record(
                action: 'SHIPMENT_DELETED',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: ['shipment_number' => $number, 'status' => $shipment->current_status],
                newValues: null,
                userId: $userId
            );

            return $shipment->delete();
        });
    }
}

