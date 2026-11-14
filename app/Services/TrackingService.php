<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TrackingService
{
    /**
     * Add a tracking event with chronological validation, integrated payload validation, and state synchronization.
     */
    public function addEvent(Shipment $shipment, array $data, int $userId): TrackingEvent
    {
        return DB::transaction(function () use ($shipment, $data, $userId) {
            // 1. Validate shipment existence
            if (empty($shipment->id) || ! $shipment->exists) {
                throw new InvalidArgumentException("Tracking event must reference a valid persisted shipment.");
            }

            // 2. Validate event type
            $eventType = strtoupper(trim($data['event_type'] ?? ''));
            if (empty($eventType) || strlen($eventType) > 50) {
                throw new InvalidArgumentException("Event type is required and must not exceed 50 characters.");
            }

            // 3. Event Time & Chronological Order validation
            $rawEventTime = $data['event_time'] ?? now();
            $eventTime = Carbon::parse($rawEventTime);

            $latestEvent = $shipment->trackingEvents()->latest('event_time')->first();
            if ($latestEvent && $eventTime->lt($latestEvent->event_time)) {
                throw new InvalidArgumentException(
                    "New event timestamp ({$eventTime}) cannot be earlier than the most recent event timestamp ({$latestEvent->event_time})."
                );
            }

            $locationId = isset($data['location_id']) && ! empty($data['location_id']) ? (int) $data['location_id'] : null;
            $locationName = trim($data['location_name'] ?? ($shipment->currentLocation->name ?? 'In Transit'));
            $latitude = isset($data['latitude']) && $data['latitude'] !== '' && $data['latitude'] !== null
                ? (float) $data['latitude']
                : ($shipment->currentLocation->latitude ?? null);
            $longitude = isset($data['longitude']) && $data['longitude'] !== '' && $data['longitude'] !== null
                ? (float) $data['longitude']
                : ($shipment->currentLocation->longitude ?? null);
            $isException = (bool) ($data['is_exception'] ?? false);
            $statusAfter = $data['status_after_event'] ?? $shipment->current_status;
            $description = $data['description'] ?? "Tracking update: {$eventType}";

            // 4. Integrated validation via TrackingEvent::validateEventPayload
            TrackingEvent::validateEventPayload([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_id' => $locationId,
                'location_name' => $locationName,
                'status_after_event' => $statusAfter,
                'is_exception' => $isException,
                'created_by' => $userId,
            ], $shipment);

            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => $eventType,
                'event_time' => $eventTime,
                'location_id' => $locationId,
                'location_name' => $locationName,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'status_after_event' => $statusAfter,
                'description' => $description,
                'is_exception' => $isException,
                'created_by' => $userId,
            ]);

            // Synchronize shipment current location & status if requested
            $updateData = [];
            if ($locationId) {
                $updateData['current_location_id'] = $locationId;
            }

            // If event represents a valid lifecycle state change, update current_status
            if (in_array($statusAfter, Shipment::STATUSES, true) && $statusAfter !== $shipment->current_status) {
                if ($shipment->canTransitionTo($statusAfter)) {
                    $updateData['current_status'] = $statusAfter;
                    if ($statusAfter === Shipment::STATUS_DELIVERED && empty($shipment->actual_delivered_at)) {
                        $updateData['actual_delivered_at'] = $eventTime;
                    }
                }
            }

            if (! empty($updateData)) {
                $shipment->update($updateData);
            }

            // Create In-App Notification for the Customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "Shipment {$shipment->shipment_number} Updated",
                'message' => "New tracking event: {$description} at {$locationName}.",
                'type' => $isException ? 'EXCEPTION_DETECTED' : 'TRACKING_UPDATE',
                'is_read' => false,
            ]);

            // Record Audit Log
            AuditLog::record(
                action: 'TRACKING_EVENT_ADDED',
                entityType: 'Shipment',
                entityId: $shipment->id,
                oldValues: ['status' => $shipment->getOriginal('current_status')],
                newValues: ['event' => $eventType, 'status' => $shipment->current_status, 'location' => $locationName],
                userId: $userId
            );

            return $event;
        });
    }
}
