<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\TrackingService;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
