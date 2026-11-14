<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'event_type',
        'event_time',
        'location_id',
        'location_name',
        'latitude',
        'longitude',
        'status_after_event',
        'description',
        'is_exception',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'is_exception' => 'boolean',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Validate tracking event attributes against system business rules.
     */
    public static function validateEventPayload(array $data, ?Shipment $shipment = null): void
    {
        // 1. Coordinates validation (-90..90, -180..180)
        if (isset($data['latitude']) && $data['latitude'] !== null && $data['latitude'] !== '') {
            $lat = (float) $data['latitude'];
            if ($lat < -90.0 || $lat > 90.0) {
                throw new \InvalidArgumentException("Latitude [{$lat}] must be between -90.0 and 90.0 degrees.");
            }
        }

        if (isset($data['longitude']) && $data['longitude'] !== null && $data['longitude'] !== '') {
            $lon = (float) $data['longitude'];
            if ($lon < -180.0 || $lon > 180.0) {
                throw new \InvalidArgumentException("Longitude [{$lon}] must be between -180.0 and 180.0 degrees.");
            }
        }

        // 2. Status after event validation & Lifecycle Transition rules
        if (isset($data['status_after_event']) && ! empty($data['status_after_event'])) {
            $statusAfter = $data['status_after_event'];
            $isException = ! empty($data['is_exception']);

            if (! $isException && ! in_array($statusAfter, Shipment::STATUSES, true)) {
                throw new \InvalidArgumentException("Status after event [{$statusAfter}] is invalid.");
            }

            if ($shipment !== null && $shipment->exists) {
                // If attempting to alter status
                if ($statusAfter !== $shipment->current_status) {
                    if ($shipment->isTerminal()) {
                        throw new \InvalidArgumentException(
                            "Shipment {$shipment->shipment_number} has reached terminal status '{$shipment->current_status}' and cannot receive further status transitions."
                        );
                    }

                    if ($isException) {
                        throw new \InvalidArgumentException(
                            "Exception event cannot alter shipment status from '{$shipment->current_status}' to '{$statusAfter}'."
                        );
                    }

                    if (! $shipment->canTransitionTo($statusAfter)) {
                        $allowed = implode(', ', Shipment::ALLOWED_TRANSITIONS[$shipment->current_status] ?? []);
                        $allowedStr = empty($allowed) ? 'none' : $allowed;
                        throw new \InvalidArgumentException(
                            "Invalid status transition from '{$shipment->current_status}' to '{$statusAfter}'. Allowed transition(s): [{$allowedStr}]."
                        );
                    }
                }
            }
        }

        // Terminal shipment protection: terminal shipments cannot receive exception events
        if ($shipment !== null && $shipment->exists && $shipment->isTerminal()) {
            if (! empty($data['is_exception'])) {
                throw new \InvalidArgumentException(
                    "Shipment {$shipment->shipment_number} has reached terminal status '{$shipment->current_status}' and cannot receive exception events."
                );
            }
        }

        // 3. Location foreign key validation
        if (isset($data['location_id']) && ! empty($data['location_id'])) {
            $locId = (int) $data['location_id'];
            if (! Location::where('id', $locId)->exists()) {
                throw new \InvalidArgumentException("Referenced location ID [{$locId}] does not exist.");
            }
        }

        // 4. Location Name validation
        if (array_key_exists('location_name', $data) && empty(trim($data['location_name'] ?? ''))) {
            throw new \InvalidArgumentException("Location name is required for tracking events.");
        }

        // 5. Creator user foreign key validation
        if (isset($data['created_by']) && ! empty($data['created_by'])) {
            $userId = (int) $data['created_by'];
            if (! User::where('id', $userId)->exists()) {
                throw new \InvalidArgumentException("Tracking event creator with user ID [{$userId}] does not exist.");
            }
        }
    }
}
