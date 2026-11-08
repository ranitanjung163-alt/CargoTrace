<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ExceptionService
{
    public function __construct(
        protected EtaService $etaService
    ) {}

    /**
     * Report / detect an exception on a shipment.
     */
    public function createException(Shipment $shipment, array $data, int $userId): ShipmentException
    {
        return DB::transaction(function () use ($shipment, $data, $userId) {
            $type = strtoupper(trim($data['exception_type']));
            if (! in_array($type, ShipmentException::TYPES, true)) {
                throw new InvalidArgumentException("Invalid exception type: {$type}");
            }

            $severity = strtoupper(trim($data['severity'] ?? 'MEDIUM'));
            $description = $data['description'] ?? "Exception reported: {$type}";
            $detectedAt = isset($data['detected_at']) ? $data['detected_at'] : now();

            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'exception_type' => $type,
                'severity' => $severity,
                'status' => ShipmentException::STATUS_OPEN,
                'description' => $description,
                'detected_at' => $detectedAt,
                'created_by' => $userId,
            ]);

            // Add corresponding tracking event indicating exception
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_type' => $type,
                'event_time' => $detectedAt,
                'location_id' => $shipment->current_location_id ?? $shipment->origin_location_id,
                'location_name' => $shipment->currentLocation->name ?? 'Current Location',
                'latitude' => $shipment->currentLocation->latitude ?? null,
                'longitude' => $shipment->currentLocation->longitude ?? null,
                'status_after_event' => $shipment->current_status,
                'description' => "EXCEPTION [{$type} - {$severity}]: {$description}",
                'is_exception' => true,
                'created_by' => $userId,
            ]);

            // If severity is MEDIUM or HIGH, mark shipment as delayed if not already
            if (in_array($severity, ['MEDIUM', 'HIGH'], true)) {
                $shipment->update([
                    'is_delayed' => true,
                    'delay_severity' => $severity,
                    'delay_reason' => "Operational exception: {$type} ({$severity}) - {$description}",
                ]);
            }

            // In-App Notification
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "Exception Alert on {$shipment->shipment_number}",
                'message' => "An exception ({$type}) of severity {$severity} was reported: {$description}.",
                'type' => 'EXCEPTION_OPENED',
                'is_read' => false,
            ]);

            // Audit Log
            AuditLog::record(
                action: 'EXCEPTION_DETECTED',
                entityType: 'ShipmentException',
                entityId: $exception->id,
                oldValues: null,
                newValues: ['type' => $type, 'severity' => $severity, 'shipment_no' => $shipment->shipment_number],
                userId: $userId
            );

            return $exception;
        });
    }

    /**
     * Update exception status (e.g. to INVESTIGATING or RESOLVED).
     */
    public function updateStatus(ShipmentException $exception, string $status, ?string $resolutionNotes, int $userId): ShipmentException
    {
        return DB::transaction(function () use ($exception, $status, $resolutionNotes, $userId) {
            $oldStatus = $exception->status;
            $status = strtoupper(trim($status));

            if (! in_array($status, ShipmentException::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: {$status}");
            }

            $updateData = ['status' => $status];

            if ($status === ShipmentException::STATUS_RESOLVED) {
                $updateData['resolved_at'] = now();
                $updateData['resolved_by'] = $userId;
                $updateData['resolution_notes'] = $resolutionNotes ?? 'Resolved by operator.';
            } elseif (! empty($resolutionNotes)) {
                $updateData['resolution_notes'] = $resolutionNotes;
            }

            $exception->update($updateData);

            $shipment = $exception->shipment;

            // If resolved, re-evaluate delay metrics on shipment
            if ($status === ShipmentException::STATUS_RESOLVED) {
                $remainingOpen = $shipment->exceptions()->whereIn('status', ['OPEN', 'INVESTIGATING'])->count();
                if ($remainingOpen === 0) {
                    $this->etaService->evaluateDelay($shipment);
                }

                // Add tracking event for resolution
                TrackingEvent::create([
                    'shipment_id' => $shipment->id,
                    'event_type' => 'EXCEPTION_RESOLVED',
                    'event_time' => now(),
                    'location_id' => $shipment->current_location_id,
                    'location_name' => $shipment->currentLocation->name ?? 'Current Location',
                    'latitude' => $shipment->currentLocation->latitude ?? null,
                    'longitude' => $shipment->currentLocation->longitude ?? null,
                    'status_after_event' => $shipment->current_status,
                    'description' => "Exception [{$exception->exception_type}] resolved: {$updateData['resolution_notes']}",
                    'is_exception' => false,
                    'created_by' => $userId,
                ]);

                // In-App Notification
                Notification::create([
                    'user_id' => $shipment->customer_id,
                    'shipment_id' => $shipment->id,
                    'title' => "Exception Resolved on {$shipment->shipment_number}",
                    'message' => "The exception ({$exception->exception_type}) has been resolved: {$updateData['resolution_notes']}.",
                    'type' => 'EXCEPTION_RESOLVED',
                    'is_read' => false,
                ]);
            }

            // Audit Log
            AuditLog::record(
                action: 'EXCEPTION_STATUS_CHANGED',
                entityType: 'ShipmentException',
                entityId: $exception->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $status, 'notes' => $resolutionNotes],
                userId: $userId
            );

            return $exception->fresh();
        });
    }
}

