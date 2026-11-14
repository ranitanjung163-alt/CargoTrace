<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\TrackingService;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class DatabaseSeederRobustnessAndChronologyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement 7.1: Seeder can be run repeatedly without duplication (idempotency).
     */
    public function test_1_seeder_is_idempotent_and_creates_no_duplicates(): void
    {
        $this->seed(DatabaseSeeder::class);

        $initialEventCount = TrackingEvent::count();
        $initialShipmentCount = Shipment::count();
        $initialUserCount = User::count();
        $initialCarrierCount = Carrier::count();
        $initialLocationCount = Location::count();

        // Run seeder a second and third time
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals($initialEventCount, TrackingEvent::count(), 'TrackingEvent count must remain identical after multiple seed runs.');
        $this->assertEquals($initialShipmentCount, Shipment::count(), 'Shipment count must remain identical.');
        $this->assertEquals($initialUserCount, User::count(), 'User count must remain identical.');
        $this->assertEquals($initialCarrierCount, Carrier::count(), 'Carrier count must remain identical.');
        $this->assertEquals($initialLocationCount, Location::count(), 'Location count must remain identical.');
    }

    /**
     * Requirement 7.2: Operational events logged by operator are never overwritten by seeder.
     */
    public function test_2_operator_events_are_never_overwritten_by_seeder(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();

        // Operator logs an operational waypoint in the field
        $operatorEvent = TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => 'IN_TRANSIT',
            'event_time' => Carbon::now()->subHours(2),
            'location_name' => 'Omaha Regional Waypoint Terminal',
            'latitude' => 41.2565,
            'longitude' => -95.9345,
            'status_after_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Operator scanned container at Omaha checkpoint gate 4.',
            'is_exception' => false,
            'created_by' => $operator->id,
        ]);

        $originalOperatorEventId = $operatorEvent->id;

        // Re-run the seeder
        $this->seed(DatabaseSeeder::class);

        $freshOperatorEvent = TrackingEvent::find($originalOperatorEventId);
        $this->assertNotNull($freshOperatorEvent, 'Operator-created event must not be deleted.');
        $this->assertEquals('Omaha Regional Waypoint Terminal', $freshOperatorEvent->location_name);
        $this->assertEquals('Operator scanned container at Omaha checkpoint gate 4.', $freshOperatorEvent->description);
        $this->assertEquals(41.2565, $freshOperatorEvent->latitude);
        $this->assertEquals(-95.9345, $freshOperatorEvent->longitude);
    }

    /**
     * Requirement 7.3: Multiple events with the same event_type (e.g. IN_TRANSIT) can coexist.
     */
    public function test_3_multiple_in_transit_events_can_coexist_on_single_shipment(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();

        // Add 2 additional distinct in-transit waypoints
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => 'IN_TRANSIT',
            'event_time' => Carbon::now()->subHours(8),
            'location_name' => 'Salt Lake City Waypoint Depot',
            'latitude' => 40.7608,
            'longitude' => -111.8910,
            'status_after_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Intermediate refueling waypoint stop.',
            'is_exception' => false,
            'created_by' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => 'IN_TRANSIT',
            'event_time' => Carbon::now()->subHours(4),
            'location_name' => 'Cheyenne Rail Intermodal',
            'latitude' => 41.1400,
            'longitude' => -104.8202,
            'status_after_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Rail carrier interchange inspection passed.',
            'is_exception' => false,
            'created_by' => $operator->id,
        ]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $inTransitEvents = TrackingEvent::where('shipment_id', $shipment->id)
            ->where('event_type', 'IN_TRANSIT')
            ->get();

        // 1 canonical seeded IN_TRANSIT + 2 operational operator waypoints = 3 IN_TRANSIT events
        $this->assertGreaterThanOrEqual(3, $inTransitEvents->count(), 'Multiple IN_TRANSIT events must comfortably coexist without collision.');
    }

    /**
     * Requirement 7.4: Valid historical events are never deleted by seeder.
     */
    public function test_4_valid_historical_events_are_never_deleted(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();

        // Custom historical customs check
        $historicalEvent = TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => 'CUSTOMS_HOLD',
            'event_time' => Carbon::now()->subHours(20),
            'location_name' => 'Barstow Inspection Station',
            'status_after_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Weight station axle scale re-check verified.',
            'is_exception' => false,
            'created_by' => $operator->id,
        ]);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('tracking_events', [
            'id' => $historicalEvent->id,
            'description' => 'Weight station axle scale re-check verified.',
        ]);
    }

    /**
     * Requirement 7.5: shipments.created_at of existing shipments is NOT mutated by seeder.
     */
    public function test_5_existing_shipment_created_at_is_not_mutated_by_seeder(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();

        // Set an arbitrary historical created_at timestamp
        $historicalCreatedAt = Carbon::create(2025, 1, 15, 10, 30, 0);
        $shipment->timestamps = false;
        $shipment->created_at = $historicalCreatedAt;
        $shipment->save();
        $shipment->timestamps = true;

        // Re-run the seeder
        $this->seed(DatabaseSeeder::class);

        $freshShipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $this->assertEquals(
            $historicalCreatedAt->toDateTimeString(),
            $freshShipment->created_at->toDateTimeString(),
            'Existing shipment created_at must not be mutated by seeder.'
        );
    }

    /**
     * Requirement 7.6: Events (CREATED, BOOKED, PICKED_UP, IN_TRANSIT) are strictly chronologically ordered.
     */
    public function test_6_events_are_strictly_chronologically_ordered(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipments = Shipment::with('trackingEvents')->get();

        foreach ($shipments as $shipment) {
            $events = $shipment->trackingEvents;
            $this->assertNotEmpty($events, "Shipment {$shipment->shipment_number} must have events.");

            // 1. First event is always CREATED
            $this->assertEquals('CREATED', $events->first()->event_type, "Shipment {$shipment->shipment_number} first event must be CREATED.");

            // 2. Strict ascending timestamps
            $previousTime = null;
            foreach ($events as $event) {
                if ($previousTime !== null) {
                    $this->assertTrue(
                        $event->event_time->gte($previousTime),
                        "Event {$event->event_type} ({$event->event_time}) on shipment {$shipment->shipment_number} cannot be earlier than previous event ({$previousTime})."
                    );
                }
                $previousTime = $event->event_time;
            }
        }
    }

    /**
     * Requirement 7.7: WEATHER_DELAY occurs strictly AFTER IN_TRANSIT.
     */
    public function test_7_weather_delay_strictly_occurs_after_in_transit(): void
    {
        $this->seed(DatabaseSeeder::class);

        $delayedShipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();

        $inTransitEvent = TrackingEvent::where('shipment_id', $delayedShipment->id)
            ->where('event_type', 'IN_TRANSIT')
            ->firstOrFail();

        $weatherDelayEvent = TrackingEvent::where('shipment_id', $delayedShipment->id)
            ->where('event_type', 'WEATHER_DELAY')
            ->firstOrFail();

        $this->assertTrue(
            $weatherDelayEvent->event_time->gt($inTransitEvent->event_time),
            'WEATHER_DELAY event must strictly post-date the IN_TRANSIT departure event.'
        );
    }

    /**
     * Requirement 7.8: WEATHER_DELAY does not occur on or after DELIVERED.
     */
    public function test_8_weather_delay_does_not_occur_after_delivered(): void
    {
        $this->seed(DatabaseSeeder::class);

        $deliveredShipments = Shipment::where('current_status', Shipment::STATUS_DELIVERED)->get();
        $this->assertNotEmpty($deliveredShipments, 'Delivered demo shipments must exist.');

        foreach ($deliveredShipments as $delivered) {
            $deliveredEvent = TrackingEvent::where('shipment_id', $delivered->id)
                ->where('event_type', Shipment::STATUS_DELIVERED)
                ->first();

            $exceptions = TrackingEvent::where('shipment_id', $delivered->id)
                ->where('is_exception', true)
                ->get();

            foreach ($exceptions as $ex) {
                if ($deliveredEvent) {
                    $this->assertTrue(
                        $ex->event_time->lt($deliveredEvent->event_time),
                        "Exception {$ex->event_type} must occur prior to delivery on delivered shipment {$delivered->shipment_number}."
                    );
                }
            }
        }

        // Specifically check the seeded WEATHER_DELAY exception: it must strictly precede any DELIVERED event or estimated delivery
        $weatherEvent = TrackingEvent::where('event_type', 'WEATHER_DELAY')->firstOrFail();
        $weatherShipment = Shipment::findOrFail($weatherEvent->shipment_id);
        
        $deliveredOnDelayed = TrackingEvent::where('shipment_id', $weatherShipment->id)
            ->where('event_type', Shipment::STATUS_DELIVERED)
            ->first();

        if ($deliveredOnDelayed) {
            $this->assertTrue(
                $weatherEvent->event_time->lt($deliveredOnDelayed->event_time),
                'WEATHER_DELAY must precede DELIVERED timestamp.'
            );
        } else {
            // Shipment is still IN_TRANSIT with delay, verify it has not received an unexpected premature DELIVERED event
            $this->assertNull($deliveredOnDelayed, 'In-transit delayed shipment must not have DELIVERED event.');
            if ($weatherShipment->estimated_delivery) {
                $this->assertTrue($weatherEvent->event_time->lt($weatherShipment->estimated_delivery), 'Delay must occur before estimated delivery.');
            } else {
                $this->assertTrue(true);
            }
        }
    }

    /**
     * Requirement 7.9: DELIVERED remains the final terminal event for delivered shipments.
     */
    public function test_9_delivered_remains_terminal_event(): void
    {
        $this->seed(DatabaseSeeder::class);

        $deliveredShipments = Shipment::where('current_status', Shipment::STATUS_DELIVERED)->get();
        $this->assertNotEmpty($deliveredShipments, 'Delivered demo shipments must exist.');

        foreach ($deliveredShipments as $shipment) {
            $events = $shipment->trackingEvents;
            $this->assertEquals(
                Shipment::STATUS_DELIVERED,
                $events->last()->event_type,
                "Delivered shipment {$shipment->shipment_number} last event must be DELIVERED."
            );
        }

        $nonDeliveredShipments = Shipment::where('current_status', '!=', Shipment::STATUS_DELIVERED)->get();
        foreach ($nonDeliveredShipments as $shipment) {
            $hasDelivered = TrackingEvent::where('shipment_id', $shipment->id)
                ->where('event_type', Shipment::STATUS_DELIVERED)
                ->exists();

            $this->assertFalse($hasDelivered, "Non-delivered shipment {$shipment->shipment_number} must not have a DELIVERED event.");
        }
    }

    /**
     * Requirement 7.10: Event exception does not illegally alter shipment status.
     */
    public function test_10_exception_events_do_not_illegally_mutate_shipment_status(): void
    {
        $this->seed(DatabaseSeeder::class);

        $delayedShipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $this->assertEquals(
            Shipment::STATUS_IN_TRANSIT,
            $delayedShipment->current_status,
            'Shipment with WEATHER_DELAY must keep its current_status as IN_TRANSIT.'
        );

        // Operator adds an exception event via TrackingService
        $trackingService = app(TrackingService::class);
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();

        $trackingService->addEvent($delayedShipment, [
            'event_type' => 'ROAD_CLOSURE',
            'event_time' => Carbon::now(),
            'location_name' => 'Interstate 80 Pass',
            'status_after_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Temporary road closure for snow clearance.',
            'is_exception' => true,
        ], $operator->id);

        $this->assertEquals(
            Shipment::STATUS_IN_TRANSIT,
            $delayedShipment->fresh()->current_status,
            'Adding an exception event must not mutate current_status to an invalid state.'
        );
    }

    /**
     * Requirement 7.11: Integrated coordinate and foreign key validation works.
     */
    public function test_11_integrated_coordinate_and_foreign_key_validation(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();
        $trackingService = app(TrackingService::class);

        // 1. Invalid Latitude (> 90)
        try {
            $trackingService->addEvent($shipment, [
                'event_type' => 'IN_TRANSIT',
                'event_time' => Carbon::now()->addMinute(),
                'location_name' => 'North Pole Out of Range',
                'latitude' => 95.0,
                'longitude' => 0.0,
            ], $operator->id);
            $this->fail('Expected InvalidArgumentException for latitude > 90 was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Latitude', $e->getMessage());
        }

        // 2. Invalid Longitude (> 180)
        try {
            $trackingService->addEvent($shipment, [
                'event_type' => 'IN_TRANSIT',
                'event_time' => Carbon::now()->addMinute(),
                'location_name' => 'Antimeridian Out of Range',
                'latitude' => 10.0,
                'longitude' => 195.0,
            ], $operator->id);
            $this->fail('Expected InvalidArgumentException for longitude > 180 was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Longitude', $e->getMessage());
        }

        // 3. Non-existent User ID
        try {
            $trackingService->addEvent($shipment, [
                'event_type' => 'IN_TRANSIT',
                'event_time' => Carbon::now()->addMinute(),
                'location_name' => 'Depot Alpha',
            ], 999999);
            $this->fail('Expected InvalidArgumentException for non-existent user ID was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('user ID', $e->getMessage());
        }

        // 4. Non-existent Location ID
        try {
            $trackingService->addEvent($shipment, [
                'event_type' => 'IN_TRANSIT',
                'event_time' => Carbon::now()->addMinute(),
                'location_id' => 999999,
                'location_name' => 'Depot Beta',
            ], $operator->id);
            $this->fail('Expected InvalidArgumentException for non-existent location ID was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsStringIgnoringCase('location ID', $e->getMessage());
        }
    }

    /**
     * Requirement 10.1, 10.2, 10.3: Shipment modified by operator (status, DELIVERED, ETA, location) is not overwritten.
     */
    public function test_12_operational_shipment_modifications_are_preserved_on_seeder_rerun(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $chicagoLocation = Location::where('code', 'US-CHI')->firstOrFail();

        // Operator delivers the shipment and updates ETA and delay status
        $deliveredTimestamp = Carbon::now()->subHour();
        $updatedEta = Carbon::now()->addDays(5);

        $shipment->update([
            'current_status' => Shipment::STATUS_DELIVERED,
            'actual_delivered_at' => $deliveredTimestamp,
            'estimated_arrival' => $updatedEta,
            'current_location_id' => $chicagoLocation->id,
            'is_delayed' => false,
            'delay_duration_minutes' => 0,
            'delay_severity' => Shipment::SEVERITY_NONE,
            'delay_reason' => null,
        ]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $fresh = $shipment->fresh();
        $this->assertEquals(Shipment::STATUS_DELIVERED, $fresh->current_status, 'Shipment status must remain DELIVERED.');
        $this->assertEquals($deliveredTimestamp->toDateTimeString(), $fresh->actual_delivered_at->toDateTimeString(), 'Delivered timestamp must remain intact.');
        $this->assertEquals($updatedEta->toDateTimeString(), $fresh->estimated_arrival->toDateTimeString(), 'Estimated arrival must not be overwritten.');
        $this->assertEquals($chicagoLocation->id, $fresh->current_location_id, 'Current location must not be reset.');
        $this->assertFalse($fresh->is_delayed, 'Delay flag must not be reset to true.');
        $this->assertEquals(0, $fresh->delay_duration_minutes);
        $this->assertEquals(Shipment::SEVERITY_NONE, $fresh->delay_severity);
        $this->assertNull($fresh->delay_reason);
    }

    /**
     * Requirement 10.4: Shipment items edited by operator are preserved.
     */
    public function test_13_shipment_items_edited_by_operator_are_preserved(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $existingItem = $shipment->items()->firstOrFail();

        // Operator modifies quantity and weight
        $existingItem->update([
            'quantity' => 77,
            'weight' => 8888.50,
            'hs_code' => '9999.00.00',
        ]);

        // Operator adds a custom extra item
        $customItem = ShipmentItem::create([
            'shipment_id' => $shipment->id,
            'item_name' => 'Custom Hydraulic Spare Component',
            'quantity' => 5,
            'unit' => 'crates',
            'weight' => 250.0,
            'hs_code' => '8412.21.00',
            'description' => 'Field operator added replacement kit.',
        ]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $freshExisting = $existingItem->fresh();
        $this->assertEquals(77, $freshExisting->quantity, 'Item quantity must not be reverted to demo 10.');
        $this->assertEquals(8888.50, $freshExisting->weight);
        $this->assertEquals('9999.00.00', $freshExisting->hs_code);

        $this->assertNotNull(ShipmentItem::find($customItem->id), 'Custom operator item must not be deleted.');
    }

    /**
     * Requirement 10.5: Exception RESOLVED does not revert to OPEN.
     */
    public function test_14_resolved_exceptions_are_not_reopened_to_open(): void
    {
        $this->seed(DatabaseSeeder::class);

        $delayedShipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $weatherEx = ShipmentException::where('shipment_id', $delayedShipment->id)
            ->where('exception_type', ShipmentException::TYPE_WEATHER_DELAY)
            ->firstOrFail();

        // Operator resolves the weather exception
        $weatherEx->update([
            'status' => ShipmentException::STATUS_RESOLVED,
            'resolved_at' => Carbon::now(),
            'resolution_notes' => 'Highway 80 snow cleared by state DOT. Escort convoy departed.',
        ]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $freshEx = $weatherEx->fresh();
        $this->assertEquals(ShipmentException::STATUS_RESOLVED, $freshEx->status, 'Resolved exception must remain RESOLVED.');
        $this->assertNotNull($freshEx->resolved_at);
        $this->assertEquals('Highway 80 snow cleared by state DOT. Escort convoy departed.', $freshEx->resolution_notes);
    }

    /**
     * Requirement 10.6: Notification is_read = true remains true.
     */
    public function test_15_read_notifications_remain_read(): void
    {
        $this->seed(DatabaseSeeder::class);

        $delayedShipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $notif = Notification::where('shipment_id', $delayedShipment->id)->firstOrFail();

        // Customer reads the notification
        $notif->update(['is_read' => true]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $this->assertTrue($notif->fresh()->is_read, 'Notification read state must not be reset to unread.');
    }

    /**
     * Requirement 10.7: Existing audit logs are immutable and unchanged.
     */
    public function test_16_audit_logs_are_immutable_and_append_only(): void
    {
        $this->seed(DatabaseSeeder::class);

        $initialLog = AuditLog::where('action', 'SYSTEM_INITIALIZATION')->firstOrFail();
        $initialCreatedAt = $initialLog->created_at->toDateTimeString();

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $freshLog = $initialLog->fresh();
        $this->assertEquals($initialCreatedAt, $freshLog->created_at->toDateTimeString(), 'Audit log creation timestamp must not be mutated.');
    }

    /**
     * Requirement 10.8: User password and profile are not reset.
     */
    public function test_17_user_passwords_and_profiles_are_not_reset(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@cargotrace.com')->firstOrFail();

        // Administrator changes password, phone, and company
        $admin->update([
            'password' => Hash::make('CustomSecurePass2026!'),
            'phone' => '+62-812-9988-7766',
            'company_name' => 'WorldCargo 360 Executive Board',
        ]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $freshAdmin = $admin->fresh();
        $this->assertTrue(Hash::check('CustomSecurePass2026!', $freshAdmin->password), 'Custom password must not be overwritten.');
        $this->assertEquals('+62-812-9988-7766', $freshAdmin->phone, 'User phone must not be overwritten.');
        $this->assertEquals('WorldCargo 360 Executive Board', $freshAdmin->company_name);
    }

    /**
     * Requirement 10.9 & 10.10: Carrier and location master data edits are preserved.
     */
    public function test_18_carrier_and_location_master_data_edits_are_preserved(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Edit carrier FST
        $carrier = Carrier::where('code', 'FST')->firstOrFail();
        $carrier->update([
            'is_active' => false,
            'contact_phone' => '+1-800-999-9999',
            'contact_email' => 'custom-ops@fastship.com',
        ]);

        // Edit location US-LAX coordinates and name
        $location = Location::where('code', 'US-LAX')->firstOrFail();
        $location->update([
            'latitude' => 33.942500,
            'longitude' => -118.408056,
            'name' => 'Los Angeles International Gateway Facility',
        ]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $freshCarrier = $carrier->fresh();
        $this->assertFalse($freshCarrier->is_active, 'Carrier deactivated state must not be reset to true.');
        $this->assertEquals('+1-800-999-9999', $freshCarrier->contact_phone);
        $this->assertEquals('custom-ops@fastship.com', $freshCarrier->contact_email);

        $freshLoc = $location->fresh();
        $this->assertEquals(33.942500, $freshLoc->latitude, 'Corrected latitude must not be overwritten.');
        $this->assertEquals(-118.408056, $freshLoc->longitude, 'Corrected longitude must not be overwritten.');
        $this->assertEquals('Los Angeles International Gateway Facility', $freshLoc->name);
    }

    /**
     * Requirement 10.13: Illegal status transitions are rejected before event creation.
     */
    public function test_19_illegal_status_transitions_are_rejected_before_event_creation(): void
    {
        $this->seed(DatabaseSeeder::class);

        $trackingService = app(TrackingService::class);
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();

        // 1. Illegal jump: CREATED directly to DELIVERED
        $createdShipment = Shipment::where('current_status', Shipment::STATUS_CREATED)->firstOrFail();
        try {
            $trackingService->addEvent($createdShipment, [
                'event_type' => 'DELIVERED',
                'event_time' => Carbon::now()->addHour(),
                'location_name' => 'Destination Hub',
                'status_after_event' => Shipment::STATUS_DELIVERED,
            ], $operator->id);
            $this->fail('Direct transition from CREATED to DELIVERED must throw InvalidArgumentException.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Invalid status transition', $e->getMessage());
        }

        // Verify no TrackingEvent was created for this illegal attempt
        $this->assertDatabaseMissing('tracking_events', [
            'shipment_id' => $createdShipment->id,
            'status_after_event' => Shipment::STATUS_DELIVERED,
        ]);

        // 2. Illegal status regression: IN_TRANSIT back to BOOKED
        $inTransitShipment = Shipment::where('current_status', Shipment::STATUS_IN_TRANSIT)->firstOrFail();
        try {
            $trackingService->addEvent($inTransitShipment, [
                'event_type' => 'BOOKED',
                'event_time' => Carbon::now()->addHour(),
                'location_name' => 'Origin Hub',
                'status_after_event' => Shipment::STATUS_BOOKED,
            ], $operator->id);
            $this->fail('Status regression from IN_TRANSIT to BOOKED must throw InvalidArgumentException.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Invalid status transition', $e->getMessage());
        }

        // 3. Status change attempted on already terminal DELIVERED shipment
        $deliveredShipment = Shipment::where('current_status', Shipment::STATUS_DELIVERED)->firstOrFail();
        try {
            $trackingService->addEvent($deliveredShipment, [
                'event_type' => 'IN_TRANSIT',
                'event_time' => Carbon::now()->addHour(),
                'location_name' => 'Transit Hub',
                'status_after_event' => Shipment::STATUS_IN_TRANSIT,
            ], $operator->id);
            $this->fail('Transition attempted on terminal DELIVERED shipment must throw InvalidArgumentException.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('terminal status', $e->getMessage());
        }

        // 4. Exception event attempting illegal status transition
        try {
            $trackingService->addEvent($inTransitShipment, [
                'event_type' => 'CUSTOMS_HOLD',
                'event_time' => Carbon::now()->addHour(),
                'location_name' => 'Customs Terminal',
                'status_after_event' => Shipment::STATUS_BOOKED,
                'is_exception' => true,
            ], $operator->id);
            $this->fail('Exception event with illegal status change must throw InvalidArgumentException.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Exception event cannot alter shipment status', $e->getMessage());
        }
    }

    /**
     * Requirement 5.1 & 5.2: Existing custom DELIVERED event prevents duplicate demo DELIVERED event and preserves timestamp.
     */
    public function test_20_custom_delivered_event_prevents_duplicate_demo_delivered_event(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();
        $trackingService = app(TrackingService::class);

        // Operator logs custom delivery event in the past
        $customDeliveredTime = Carbon::now()->subHours(3);
        $trackingService->addEvent($shipment, [
            'event_type' => Shipment::STATUS_DELIVERED,
            'event_time' => $customDeliveredTime,
            'location_name' => 'Chicago Receiving Bay 4',
            'status_after_event' => Shipment::STATUS_DELIVERED,
            'description' => 'Consignment signed for by Receiving Manager J. Doe',
        ], $operator->id);

        $this->assertEquals(1, TrackingEvent::where('shipment_id', $shipment->id)->where('event_type', Shipment::STATUS_DELIVERED)->count());

        // Re-run the seeder
        $this->seed(DatabaseSeeder::class);

        // Verify still exactly 1 DELIVERED event, and it is the operator's custom event
        $deliveredEvents = TrackingEvent::where('shipment_id', $shipment->id)
            ->where('event_type', Shipment::STATUS_DELIVERED)
            ->get();

        $this->assertCount(1, $deliveredEvents, 'There must NEVER be duplicate DELIVERED events on a delivered shipment.');
        $this->assertEquals('Consignment signed for by Receiving Manager J. Doe', $deliveredEvents->first()->description);
        $this->assertEquals($customDeliveredTime->toDateTimeString(), $deliveredEvents->first()->event_time->toDateTimeString());
    }

    /**
     * Requirement 5.3: No future delivery timestamp is inserted for an already delivered shipment.
     */
    public function test_21_no_future_delivery_timestamp_is_inserted_for_delivered_shipment(): void
    {
        $this->seed(DatabaseSeeder::class);

        $deliveredShipments = Shipment::where('current_status', Shipment::STATUS_DELIVERED)->get();
        $this->assertNotEmpty($deliveredShipments);

        foreach ($deliveredShipments as $shipment) {
            $deliveredEvent = TrackingEvent::where('shipment_id', $shipment->id)
                ->where('event_type', Shipment::STATUS_DELIVERED)
                ->firstOrFail();

            $this->assertFalse(
                $deliveredEvent->event_time->isFuture(),
                "Delivered event on {$shipment->shipment_number} must not be in the future (was: {$deliveredEvent->event_time})."
            );
        }
    }

    /**
     * Requirement 5.4: Operationally modified shipments do not receive irrelevant Denver weather exceptions.
     */
    public function test_22_operationally_modified_shipment_does_not_receive_irrelevant_weather_exception(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001284')->firstOrFail();

        // Operator marks delay as cleared and changes route away from Denver
        $shipment->update([
            'is_delayed' => false,
            'delay_duration_minutes' => 0,
            'delay_severity' => Shipment::SEVERITY_NONE,
            'delay_reason' => null,
            'current_status' => Shipment::STATUS_DELIVERED,
            'actual_delivered_at' => Carbon::now()->subHours(2),
        ]);

        // Remove any existing weather exception to test if seeder erroneously re-imposes it
        ShipmentException::where('shipment_id', $shipment->id)->delete();
        TrackingEvent::where('shipment_id', $shipment->id)->where('event_type', 'WEATHER_DELAY')->delete();
        Notification::where('shipment_id', $shipment->id)->where('type', 'SHIPMENT_DELAYED')->delete();

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        // Verify seeder did NOT inject Denver weather exception, event, or notification
        $this->assertDatabaseMissing('shipment_exceptions', [
            'shipment_id' => $shipment->id,
            'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
        ]);

        $this->assertDatabaseMissing('tracking_events', [
            'shipment_id' => $shipment->id,
            'event_type' => 'WEATHER_DELAY',
        ]);

        $this->assertDatabaseMissing('notifications', [
            'shipment_id' => $shipment->id,
            'type' => 'SHIPMENT_DELAYED',
        ]);
    }

    /**
     * Requirement 5.9: Repeated legitimate exception incidents can coexist.
     */
    public function test_23_repeated_legitimate_exception_incidents_are_supported(): void
    {
        $this->seed(DatabaseSeeder::class);

        $shipment = Shipment::where('shipment_number', 'SHP-2024-001223')->firstOrFail();
        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();

        // Operator logs incident 1: CUSTOMS_HOLD
        $ex1 = ShipmentException::create([
            'shipment_id' => $shipment->id,
            'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => 'LOW',
            'status' => ShipmentException::STATUS_RESOLVED,
            'description' => 'Border check passed.',
            'detected_at' => Carbon::now()->subDays(2),
            'resolved_at' => Carbon::now()->subDays(1),
            'created_by' => $operator->id,
        ]);

        // Operator logs incident 2: PORT_CONGESTION
        $ex2 = ShipmentException::create([
            'shipment_id' => $shipment->id,
            'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
            'severity' => 'MEDIUM',
            'status' => ShipmentException::STATUS_INVESTIGATING,
            'description' => 'Container yard backlog.',
            'detected_at' => Carbon::now()->subHours(12),
            'created_by' => $operator->id,
        ]);

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        // Both exceptions still exist
        $this->assertNotNull(ShipmentException::find($ex1->id));
        $this->assertNotNull(ShipmentException::find($ex2->id));
        $this->assertEquals(2, ShipmentException::where('shipment_id', $shipment->id)->count());
    }

    /**
     * Requirement 5.12: Operational audit logs remain append-only and are never overwritten.
     */
    public function test_24_operational_audit_logs_remain_append_only(): void
    {
        $this->seed(DatabaseSeeder::class);

        $operator = User::where('role', User::ROLE_OPERATOR)->firstOrFail();

        $log1 = AuditLog::record('DISPATCH_CHECK', 'Shipment', 1, null, ['status' => 'OK'], $operator->id);
        $log2 = AuditLog::record('DISPATCH_CHECK', 'Shipment', 1, null, ['status' => 'OK_CONFIRMED'], $operator->id);

        $this->assertNotEquals($log1->id, $log2->id, 'Repeated operational actions must generate distinct append-only records.');

        // Re-run seeder
        $this->seed(DatabaseSeeder::class);

        $this->assertNotNull(AuditLog::find($log1->id));
        $this->assertNotNull(AuditLog::find($log2->id));
    }

    /**
     * Requirement Concurrency Guard 1: Seeder is not executed when lock is already held by another process.
     */
    public function test_25_seeder_is_not_executed_when_lock_is_held_by_another_process(): void
    {
        // Simulate external process holding the lock
        $externalLock = Cache::lock('worldcargo360_database_seeder_mutex', 300);
        $this->assertTrue($externalLock->get(), 'External lock must be successfully acquired.');

        try {
            // Attempt to run seeder while lock is held
            $seeder = new DatabaseSeeder();
            $seeder->run();

            // Verify no seeding occurred
            $this->assertEquals(0, User::count(), 'Seeder must not execute any insertions when lock is held by another process.');
            $this->assertEquals(0, Shipment::count(), 'No shipments should be created.');
        } finally {
            $externalLock->release();
        }
    }

    /**
     * Requirement Concurrency Guard 2: Seeder fails closed when lock acquisition throws an exception.
     */
    public function test_26_seeder_fails_closed_when_lock_acquisition_encounters_exception(): void
    {
        Cache::shouldReceive('lock')
            ->with('worldcargo360_database_seeder_mutex', \Mockery::any())
            ->once()
            ->andThrow(new \RuntimeException('Simulated cache engine connection failure'));

        $seeder = new DatabaseSeeder();
        $seeder->run();

        // Verify seeder halted safely without executing in unprotected state
        $this->assertEquals(0, User::count(), 'Seeder must fail closed and insert nothing when lock acquisition throws an exception.');
        $this->assertEquals(0, Shipment::count(), 'No shipments should be created on lock acquisition failure.');
    }

    /**
     * Requirement Concurrency Guard 3: Lock is released after seeder successfully completes.
     */
    public function test_27_lock_is_released_after_seeder_successfully_completes(): void
    {
        $seeder = new DatabaseSeeder();
        $seeder->run();

        // Verify data was seeded
        $this->assertGreaterThan(0, User::count());

        // Immediately verify lock can be acquired by another process
        $testLock = Cache::lock('worldcargo360_database_seeder_mutex', 10);
        $acquired = $testLock->get();
        $this->assertTrue($acquired, 'Lock must be completely released after DatabaseSeeder finishes successfully.');

        $testLock->release();
    }

    /**
     * Requirement Concurrency Guard 4: Lock is released even when seeder encounters an unhandled exception.
     */
    public function test_28_lock_is_released_when_seeder_encounters_exception(): void
    {
        // Hook into User::saving to force an exception midway during seeding
        User::saving(function () {
            throw new \RuntimeException('Simulated unexpected crash during database seeding');
        });

        try {
            $seeder = new DatabaseSeeder();

            $threwException = false;
            try {
                $seeder->run();
            } catch (\RuntimeException $e) {
                $threwException = true;
                $this->assertStringContainsString('Simulated unexpected crash', $e->getMessage());
            }

            $this->assertTrue($threwException, 'Seeder should have propagated the exception.');

            // Verify lock is released despite the crash
            $testLock = Cache::lock('worldcargo360_database_seeder_mutex', 10);
            $acquired = $testLock->get();
            $this->assertTrue($acquired, 'Lock must be released via finally block even when seeder crashes with an exception.');

            $testLock->release();
        } finally {
            User::flushEventListeners();
        }
    }
}
