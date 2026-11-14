<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\EtaService;
use App\Services\ExceptionService;
use App\Services\GeocodingService;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use App\Services\WeatherService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CargoTraceCompleteTestSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;
    protected User $customer1;
    protected User $customer2;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base test fixtures
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->operator = User::create([
            'name' => 'Operator User',
            'email' => 'operator@test.com',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $this->customer1 = User::create([
            'name' => 'Customer Alpha',
            'email' => 'customer1@test.com',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->customer2 = User::create([
            'name' => 'Customer Beta',
            'email' => 'customer2@test.com',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->carrier = Carrier::create([
            'code' => 'FST',
            'name' => 'FastShip Express',
            'service_type' => 'Air Freight',
            'is_active' => true,
        ]);

        $this->origin = Location::create([
            'code' => 'US-LAX',
            'name' => 'Los Angeles Hub',
            'city' => 'Los Angeles',
            'country' => 'United States',
            'country_code' => 'US',
            'latitude' => 34.0522,
            'longitude' => -118.2437,
            'type' => 'PORT',
        ]);

        $this->destination = Location::create([
            'code' => 'US-CHI',
            'name' => 'Chicago Intermodal Terminal',
            'city' => 'Chicago',
            'country' => 'United States',
            'country_code' => 'US',
            'latitude' => 41.8781,
            'longitude' => -87.6298,
            'type' => 'DISTRIBUTION_CENTER',
        ]);
    }

    /**
     * TEST 1: Login berhasil
     */
    public function test_1_login_berhasil(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * TEST 2: Login gagal
     */
    public function test_2_login_gagal(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * TEST 3: Akses tanpa autentikasi
     */
    public function test_3_akses_tanpa_autentikasi(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $apiResponse = $this->getJson('/api/shipments');
        $apiResponse->assertStatus(401);
    }

    /**
     * TEST 4: Admin dapat mengakses fitur admin
     */
    public function test_4_admin_dapat_mengakses_fitur_admin(): void
    {
        $responseUsers = $this->actingAs($this->admin)->get('/users');
        $responseUsers->assertStatus(200);

        $responseAudit = $this->actingAs($this->admin)->get('/audit-logs');
        $responseAudit->assertStatus(200);
    }

    /**
     * TEST 5: Operator tidak dapat mengakses fitur admin
     */
    public function test_5_operator_tidak_dapat_mengakses_fitur_admin(): void
    {
        $response = $this->actingAs($this->operator)->get('/users');
        $response->assertStatus(403);

        $responseAudit = $this->actingAs($this->operator)->get('/audit-logs');
        $responseAudit->assertStatus(403);
    }

    /**
     * TEST 6: Customer tidak dapat melihat shipment milik orang lain
     */
    public function test_6_customer_tidak_dapat_melihat_shipment_milik_orang_lain(): void
    {
        $shipmentBeta = Shipment::create([
            'shipment_number' => 'SHP-2024-BETA01',
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Confidential Tech',
            'weight' => 1000,
            'volume' => 5,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_CREATED,
        ]);

        // Customer 1 tries to access Customer 2's shipment
        $response = $this->actingAs($this->customer1)->get("/shipments/{$shipmentBeta->id}");
        $response->assertStatus(403);

        // Also test API data isolation
        $apiResponse = $this->actingAs($this->customer1, 'sanctum')->getJson("/api/shipments/{$shipmentBeta->id}");
        $apiResponse->assertStatus(403);
    }

    /**
     * TEST 7: Shipment CRUD
     */
    public function test_7_shipment_crud(): void
    {
        $shipmentService = app(ShipmentService::class);

        // CREATE
        $shipment = $shipmentService->createShipment([
            'shipment_number' => 'SHP-CRUD-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Apparel',
            'weight' => 500,
            'volume' => 4,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(3),
            'estimated_arrival' => now()->addDays(3),
        ], [
            ['item_name' => 'Cotton Shirts', 'quantity' => 100, 'unit' => 'boxes', 'weight' => 500],
        ], $this->operator->id);

        $this->assertDatabaseHas('shipments', ['shipment_number' => 'SHP-CRUD-001']);
        $this->assertDatabaseHas('shipment_items', ['item_name' => 'Cotton Shirts']);

        // READ
        $response = $this->actingAs($this->customer1)->get("/shipments/{$shipment->id}");
        $response->assertStatus(200);
        $response->assertSee('SHP-CRUD-001');

        // UPDATE
        $updated = $shipmentService->updateShipment($shipment, [
            'cargo_type' => 'Premium Apparel',
        ], $this->operator->id);
        $this->assertEquals('Premium Apparel', $updated->cargo_type);

        // DELETE (Admin only)
        $this->actingAs($this->admin)->delete("/shipments/{$shipment->id}");
        $this->assertDatabaseMissing('shipments', ['id' => $shipment->id]);
    }

    /**
     * TEST 8: Shipment validation
     */
    public function test_8_shipment_validation(): void
    {
        // Fails when origin and destination are the same
        $response = $this->actingAs($this->operator)->post('/shipments', [
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->origin->id, // SAME!
            'cargo_type' => 'Electronics',
            'weight' => 500,
            'volume' => 3,
            'expected_departure' => now()->format('Y-m-d\TH:i'),
            'expected_arrival' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('origin_location_id');
    }

    /**
     * TEST 9: Tracking event creation
     */
    public function test_9_tracking_event_creation(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-TRK-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Auto Parts',
            'weight' => 2000,
            'volume' => 12,
            'expected_departure' => now()->subDay(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_BOOKED,
        ]);

        $trackingService = app(TrackingService::class);
        $event = $trackingService->addEvent($shipment, [
            'event_type' => 'PICKED_UP',
            'event_time' => now(),
            'location_id' => $this->origin->id,
            'location_name' => 'Los Angeles Hub',
            'latitude' => 34.0522,
            'longitude' => -118.2437,
            'status_after_event' => Shipment::STATUS_PICKED_UP,
            'description' => 'Dispatched from origin warehouse.',
        ], $this->operator->id);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'event_type' => 'PICKED_UP',
        ]);
        $this->assertEquals(Shipment::STATUS_PICKED_UP, $shipment->fresh()->current_status);
    }

    /**
     * TEST 10: Tracking chronology validation
     */
    public function test_10_tracking_chronology(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-CHRONO-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Machinery',
            'weight' => 3000,
            'volume' => 15,
            'expected_departure' => now()->subDays(2),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_CREATED,
        ]);

        $trackingService = app(TrackingService::class);

        // Event 1 at 12:00
        $trackingService->addEvent($shipment, [
            'event_type' => 'BOOKED',
            'event_time' => Carbon::now()->subHours(2),
            'location_name' => 'Terminal A',
            'description' => 'Slot booked',
        ], $this->operator->id);

        // Event 2 attempted with earlier timestamp (subHours(5))
        $this->expectException(InvalidArgumentException::class);
        $trackingService->addEvent($shipment, [
            'event_type' => 'INVALID_TIME',
            'event_time' => Carbon::now()->subHours(5), // Earlier!
            'location_name' => 'Terminal A',
            'description' => 'Invalid chronological event',
        ], $this->operator->id);
    }

    /**
     * TEST 11: Invalid status transition
     */
    public function test_11_invalid_status_transition(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-TRANS-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Consumer Goods',
            'weight' => 500,
            'volume' => 3,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_CREATED,
        ]);

        $shipmentService = app(ShipmentService::class);

        // CREATED cannot jump directly to DELIVERED
        $this->expectException(InvalidArgumentException::class);
        $shipmentService->transitionStatus($shipment, Shipment::STATUS_DELIVERED, $this->operator->id);
    }

    /**
     * TEST 12: DELIVERED tidak dapat kembali ke status awal
     */
    public function test_12_delivered_tidak_dapat_kembali_ke_status_awal(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-DELIV-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Medical Supplies',
            'weight' => 400,
            'volume' => 2,
            'expected_departure' => now()->subDays(5),
            'expected_arrival' => now()->subDay(),
            'estimated_arrival' => now()->subDay(),
            'current_status' => Shipment::STATUS_DELIVERED,
        ]);

        $shipmentService = app(ShipmentService::class);

        // Cannot transition from terminal DELIVERED back to CREATED or IN_TRANSIT
        $this->expectException(InvalidArgumentException::class);
        $shipmentService->transitionStatus($shipment, Shipment::STATUS_IN_TRANSIT, $this->operator->id);
    }

    /**
     * TEST 13: ETA calculation and delay detection
     */
    public function test_13_eta_calculation(): void
    {
        $etaService = app(EtaService::class);

        // Case A: On-time (estimated <= expected)
        $shipmentOnTime = Shipment::create([
            'shipment_number' => 'SHP-ONTIME-01',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Cargo',
            'weight' => 100,
            'volume' => 1,
            'expected_departure' => now(),
            'expected_arrival' => Carbon::now()->addHours(24),
            'estimated_arrival' => Carbon::now()->addHours(24),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $evalOnTime = $etaService->evaluateDelay($shipmentOnTime);
        $this->assertFalse($evalOnTime['is_delayed']);
        $this->assertEquals(0, $evalOnTime['delay_duration_minutes']);

        // Case B: Delayed (estimated > expected by 18 hours)
        $shipmentDelayed = Shipment::create([
            'shipment_number' => 'SHP-DELAYED-01',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Cargo',
            'weight' => 100,
            'volume' => 1,
            'expected_departure' => now(),
            'expected_arrival' => Carbon::now()->addHours(24),
            'estimated_arrival' => Carbon::now()->addHours(42), // +18 hours!
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $evalDelayed = $etaService->evaluateDelay($shipmentDelayed);
        $this->assertTrue($evalDelayed['is_delayed']);
        $this->assertEquals(18.0, $evalDelayed['delay_duration_hours']);
        $this->assertEquals(Shipment::SEVERITY_MEDIUM, $evalDelayed['delay_severity']);
    }

    /**
     * TEST 14: Delay severity rules (0-6h LOW, 6-24h MEDIUM, >24h HIGH)
     */
    public function test_14_delay_severity(): void
    {
        $etaService = app(EtaService::class);

        $this->assertEquals(Shipment::SEVERITY_NONE, $etaService->determineSeverity(0));
        $this->assertEquals(Shipment::SEVERITY_LOW, $etaService->determineSeverity(4.5));
        $this->assertEquals(Shipment::SEVERITY_MEDIUM, $etaService->determineSeverity(14.0));
        $this->assertEquals(Shipment::SEVERITY_HIGH, $etaService->determineSeverity(36.0));
    }

    /**
     * TEST 15: Exception management
     */
    public function test_15_exception_management(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-EXC-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Perishable Goods',
            'weight' => 500,
            'volume' => 4,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $exceptionService = app(ExceptionService::class);

        // Create WEATHER_DELAY exception
        $exception = $exceptionService->createException($shipment, [
            'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
            'severity' => 'MEDIUM',
            'description' => 'Blizzard alert across transit route',
        ], $this->operator->id);

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'exception_type' => 'WEATHER_DELAY',
            'status' => 'OPEN',
        ]);

        // Auto-marked delayed
        $this->assertTrue($shipment->fresh()->is_delayed);

        // Move to INVESTIGATING
        $investigating = $exceptionService->updateStatus($exception, 'INVESTIGATING', 'Dispatch investigating alternate highway route', $this->operator->id);
        $this->assertEquals('INVESTIGATING', $investigating->status);

        // Resolve exception
        $resolved = $exceptionService->updateStatus($investigating, 'RESOLVED', 'Road cleared by highway patrol', $this->operator->id);
        $this->assertEquals('RESOLVED', $resolved->status);
        $this->assertNotNull($resolved->resolved_at);
    }

    /**
     * TEST 16: API authorization
     */
    public function test_16_api_authorization(): void
    {
        // Guest denied
        $this->getJson('/api/shipments')->assertStatus(401);

        // Sanctum Bearer token for Customer 1
        $token = $this->customer1->createToken('test_token')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/shipments');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data', 'meta']);
    }

    /**
     * TEST 17: Global coordinate validation (-90..90 and -180..180)
     */
    public function test_17_global_coordinate_validation(): void
    {
        $geocodingService = app(GeocodingService::class);

        // Valid international coordinates (e.g. Sydney Australia, London, Tokyo, Santos Brazil)
        $this->assertTrue($geocodingService->isValidCoordinates(-33.8688, 151.2093)); // Sydney
        $this->assertTrue($geocodingService->isValidCoordinates(51.5074, -0.1278));  // London
        $this->assertTrue($geocodingService->isValidCoordinates(-23.9608, -46.3336)); // Santos

        // Invalid coordinates
        $this->assertFalse($geocodingService->isValidCoordinates(95.0, 100.0));   // Lat > 90
        $this->assertFalse($geocodingService->isValidCoordinates(-95.0, 100.0));  // Lat < -90
        $this->assertFalse($geocodingService->isValidCoordinates(10.0, 195.0));   // Lon > 180
        $this->assertFalse($geocodingService->isValidCoordinates(10.0, -195.0));  // Lon < -180

        // API Endpoint rejects invalid coordinates
        $token = $this->admin->createToken('admin_token')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/locations', [
            'code' => 'TEST-INVALID',
            'name' => 'Invalid Hub',
            'city' => 'Nowhere',
            'country' => 'Fantasy',
            'country_code' => 'FA',
            'latitude' => 110.0, // Invalid!
            'longitude' => 200.0, // Invalid!
            'type' => 'PORT',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 18: Geocoding API error handling & fallback
     */
    public function test_18_geocoding_api_error_handling(): void
    {
        // Mock external API network failure / timeout
        Http::fake([
            'https://nominatim.openstreetmap.org/*' => Http::response(null, 500),
        ]);

        $geocodingService = app(GeocodingService::class);

        // Should gracefully fallback to built-in fallback database without throwing unhandled exception
        $result = $geocodingService->search('rotterdam');

        $this->assertNotNull($result);
        $this->assertEquals(51.924420, $result['lat']);
        $this->assertEquals(4.477733, $result['lon']);
        $this->assertEquals('fallback_database', $result['source']);
    }

    /**
     * TEST 19: ETA Threshold (12h) rules and edge cases (0h, 6h, 11h, exact 12h, 12h 1m, 24h, >24h)
     */
    public function test_19_eta_threshold_12_hours_and_edge_cases(): void
    {
        $etaService = app(EtaService::class);
        $this->assertEquals(12.0, $etaService->getDelayThresholdHours());

        $baseShipment = function(int $addedMinutes) {
            return Shipment::create([
                'shipment_number' => 'SHP-THR-' . $addedMinutes . '-' . uniqid(),
                'customer_id' => $this->customer1->id,
                'carrier_id' => $this->carrier->id,
                'origin_location_id' => $this->origin->id,
                'destination_location_id' => $this->destination->id,
                'cargo_type' => 'Cargo',
                'weight' => 50,
                'volume' => 1,
                'expected_departure' => now(),
                'expected_arrival' => Carbon::now()->addHours(24),
                'estimated_arrival' => Carbon::now()->addHours(24)->addMinutes($addedMinutes),
                'current_status' => Shipment::STATUS_IN_TRANSIT,
            ]);
        };

        // 1. Condition: 0 hours delay
        $eval0 = $etaService->evaluateDelay($baseShipment(0));
        $this->assertFalse($eval0['is_delayed']);
        $this->assertEquals(Shipment::SEVERITY_NONE, $eval0['delay_severity']);

        // 2. Condition: 6 hours delay (<= 12h threshold)
        $eval6 = $etaService->evaluateDelay($baseShipment(6 * 60));
        $this->assertFalse($eval6['is_delayed']); // within SLA buffer
        $this->assertEquals(Shipment::SEVERITY_LOW, $eval6['delay_severity']);

        // 3. Condition: 11 hours delay (<= 12h threshold)
        $eval11 = $etaService->evaluateDelay($baseShipment(11 * 60));
        $this->assertFalse($eval11['is_delayed']);
        $this->assertEquals(Shipment::SEVERITY_MEDIUM, $eval11['delay_severity']);

        // 4. Condition: Exact 12 hours (boundary: <= 12h threshold)
        $eval12 = $etaService->evaluateDelay($baseShipment(12 * 60));
        $this->assertFalse($eval12['is_delayed']); // exactly at threshold limit, not breached
        $this->assertEquals(Shipment::SEVERITY_MEDIUM, $eval12['delay_severity']);

        // 5. Condition: 12 hours 1 minute (strictly > 12h threshold)
        $eval12_1 = $etaService->evaluateDelay($baseShipment(12 * 60 + 1));
        $this->assertTrue($eval12_1['is_delayed']); // SLA breach triggered!
        $this->assertEquals(Shipment::SEVERITY_MEDIUM, $eval12_1['delay_severity']);

        // 6. Condition: 24 hours delay
        $eval24 = $etaService->evaluateDelay($baseShipment(24 * 60));
        $this->assertTrue($eval24['is_delayed']);
        $this->assertEquals(Shipment::SEVERITY_MEDIUM, $eval24['delay_severity']);

        // 7. Condition: More than 24 hours (e.g. 30 hours)
        $eval30 = $etaService->evaluateDelay($baseShipment(30 * 60));
        $this->assertTrue($eval30['is_delayed']);
        $this->assertEquals(Shipment::SEVERITY_HIGH, $eval30['delay_severity']);
    }

    /**
     * TEST 20: On-Time Delivery Rate formula: (on_time_delivered / total_delivered) * 100
     */
    public function test_20_on_time_delivery_rate_calculation(): void
    {
        // Create 2 on-time delivered shipments
        Shipment::create([
            'shipment_number' => 'SHP-ONTIME-1',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Standard',
            'weight' => 10,
            'volume' => 1,
            'expected_departure' => now()->subDays(5),
            'expected_arrival' => now()->subDays(2),
            'estimated_arrival' => now()->subDays(2),
            'actual_delivered_at' => now()->subDays(2),
            'current_status' => Shipment::STATUS_DELIVERED,
            'is_delayed' => false,
        ]);

        Shipment::create([
            'shipment_number' => 'SHP-ONTIME-2',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Standard',
            'weight' => 10,
            'volume' => 1,
            'expected_departure' => now()->subDays(4),
            'expected_arrival' => now()->subDays(1),
            'estimated_arrival' => now()->subDays(1),
            'actual_delivered_at' => now()->subDays(1),
            'current_status' => Shipment::STATUS_DELIVERED,
            'is_delayed' => false,
        ]);

        // Create 1 delayed delivered shipment
        Shipment::create([
            'shipment_number' => 'SHP-LATE-DELIVERED',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Standard',
            'weight' => 10,
            'volume' => 1,
            'expected_departure' => now()->subDays(6),
            'expected_arrival' => now()->subDays(3),
            'estimated_arrival' => now()->subDays(1),
            'actual_delivered_at' => now()->subDays(1),
            'current_status' => Shipment::STATUS_DELIVERED,
            'is_delayed' => true,
            'delay_duration_minutes' => 2880,
            'delay_severity' => Shipment::SEVERITY_HIGH,
        ]);

        // Create 2 in-transit shipments (must NOT be in the delivered denominator!)
        Shipment::create([
            'shipment_number' => 'SHP-TRANSIT-ACTIVE',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Standard',
            'weight' => 10,
            'volume' => 1,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(3),
            'estimated_arrival' => now()->addDays(3),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
            'is_delayed' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // 2 on-time out of 3 delivered = (2 / 3) * 100 = 66.7%
        $response->assertViewHas('onTimeDeliveryRate', 66.7);
    }

    /**
     * TEST 21: Global coordinate limits: Prime Meridian (0,0), negative coords, and boundaries
     */
    public function test_21_coordinate_edge_cases_zero_negative_and_limits(): void
    {
        $geocodingService = app(GeocodingService::class);

        // 1. Coordinates 0.0, 0.0 (Gulf of Guinea / Prime Meridian & Equator) must be valid
        $this->assertTrue($geocodingService->isValidCoordinates(0.0, 0.0));

        // 2. Maximum limits (-90..90, -180..180)
        $this->assertTrue($geocodingService->isValidCoordinates(90.0, 180.0));
        $this->assertTrue($geocodingService->isValidCoordinates(-90.0, -180.0));

        // 3. International hubs
        $this->assertTrue($geocodingService->isValidCoordinates(-6.2088, 106.8456)); // Jakarta
        $this->assertTrue($geocodingService->isValidCoordinates(35.6762, 139.6503));  // Tokyo
        $this->assertTrue($geocodingService->isValidCoordinates(-33.9249, 18.4241));  // Cape Town
    }

    /**
     * TEST 22: Weather API failure handling & unavailable state without fake 22°C
     */
    public function test_22_weather_service_failure_and_unavailable_state(): void
    {
        Cache::flush();

        // Simulate external API outage (e.g. 503 Service Unavailable)
        Http::fake([
            'https://api.open-meteo.com/*' => Http::response(null, 503),
        ]);

        $weatherService = app(WeatherService::class);
        $result = $weatherService->getWeather(37.77, -122.42);

        $this->assertEquals('unavailable', $result['status']);
        $this->assertNull($result['temperature']);
        $this->assertEquals('Weather data unavailable', $result['condition']);
        $this->assertFalse($result['is_adverse']);
    }

    /**
     * TEST 23: CSV export filter application and formula injection prevention
     */
    public function test_23_csv_export_filters_and_formula_injection_prevention(): void
    {
        // Create shipment with potentially hazardous formula injection string in number
        Shipment::create([
            'shipment_number' => '=1+1', // Classic spreadsheet formula injection
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Tech Consignment',
            'weight' => 25,
            'volume' => 1,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
            'is_delayed' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('shipments.export', [
            'status' => Shipment::STATUS_IN_TRANSIT,
            'carrier_id' => $this->carrier->id,
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));

        // Retrieve streamed content
        $content = $response->streamedContent();

        // Verify CSV header
        $this->assertStringContainsString('Shipment Number', $content);

        // Verify sanitization: leading '=' must be safely prefixed with single quote "'"
        $this->assertStringContainsString("'=1+1", $content);
    }

    /**
     * TEST 24: Comprehensive shipment filtering (origin, destination, date range, delayed status, sorting)
     */
    public function test_24_comprehensive_shipment_filters_origin_destination_dates_delayed_and_sorting(): void
    {
        $locTokyo = Location::create([
            'code' => 'NRT-TEST',
            'name' => 'Narita Hub',
            'city' => 'Tokyo',
            'country' => 'Japan',
            'country_code' => 'JP',
            'type' => 'AIRPORT',
            'latitude' => 35.76,
            'longitude' => 140.38,
        ]);

        $locLondon = Location::create([
            'code' => 'LHR-TEST',
            'name' => 'Heathrow Terminal',
            'city' => 'London',
            'country' => 'United Kingdom',
            'country_code' => 'GB',
            'type' => 'AIRPORT',
            'latitude' => 51.47,
            'longitude' => -0.45,
        ]);

        // Shipment A: Tokyo to London, created 3 days ago, delayed, weight 500
        $shipmentA = Shipment::create([
            'shipment_number' => 'SHP-FILTER-AAA',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $locTokyo->id,
            'destination_location_id' => $locLondon->id,
            'cargo_type' => 'Semiconductors',
            'weight' => 500.0,
            'volume' => 5.0,
            'expected_departure' => now()->subDays(3),
            'expected_arrival' => now()->addDays(1),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
            'is_delayed' => true,
            'delay_severity' => 'HIGH',
        ]);
        $shipmentA->created_at = now()->subDays(3);
        $shipmentA->save();

        // Shipment B: Origin to Destination, created 1 day ago, NOT delayed, weight 100
        $shipmentB = Shipment::create([
            'shipment_number' => 'SHP-FILTER-BBB',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Apparel',
            'weight' => 100.0,
            'volume' => 2.0,
            'expected_departure' => now()->subDay(),
            'expected_arrival' => now()->addDays(3),
            'estimated_arrival' => now()->addDays(3),
            'current_status' => Shipment::STATUS_PICKED_UP,
            'is_delayed' => false,
        ]);
        $shipmentB->created_at = now()->subDay();
        $shipmentB->save();

        // 1. Filter by Origin
        $resOrigin = $this->actingAs($this->admin)->get(route('shipments.index', ['origin_id' => $locTokyo->id]));
        $resOrigin->assertStatus(200);
        $resOrigin->assertSee('SHP-FILTER-AAA');
        $resOrigin->assertDontSee('SHP-FILTER-BBB');

        // 2. Filter by Destination
        $resDest = $this->actingAs($this->admin)->get(route('shipments.index', ['destination_id' => $locLondon->id]));
        $resDest->assertStatus(200);
        $resDest->assertSee('SHP-FILTER-AAA');
        $resDest->assertDontSee('SHP-FILTER-BBB');

        // 3. Filter by Delayed
        $resDelayed = $this->actingAs($this->admin)->get(route('shipments.index', ['delayed' => '1']));
        $resDelayed->assertStatus(200);
        $resDelayed->assertSee('SHP-FILTER-AAA');
        $resDelayed->assertDontSee('SHP-FILTER-BBB');

        $resNotDelayed = $this->actingAs($this->admin)->get(route('shipments.index', ['delayed' => '0']));
        $resNotDelayed->assertStatus(200);
        $resNotDelayed->assertSee('SHP-FILTER-BBB');

        // 4. Filter by Date Range
        $resDate = $this->actingAs($this->admin)->get(route('shipments.index', [
            'start_date' => now()->subDays(4)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
        ]));
        $resDate->assertStatus(200);
        $resDate->assertSee('SHP-FILTER-AAA');
        $resDate->assertDontSee('SHP-FILTER-BBB');

        // 5. Sorting by weight ascending
        $resSort = $this->actingAs($this->admin)->get(route('shipments.index', [
            'sort_by' => 'weight',
            'sort_direction' => 'asc',
        ]));
        $resSort->assertStatus(200);
        $shipmentsData = $resSort->viewData('shipments');
        $this->assertEquals('SHP-FILTER-BBB', $shipmentsData->first()->shipment_number);
    }

    /**
     * TEST 25: Customer isolation on web shipment views and CSV exports
     */
    public function test_25_customer_shipment_isolation_on_web_and_csv_export(): void
    {
        $shpCust1 = Shipment::create([
            'shipment_number' => 'SHP-CUST1-ONLY',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Private Cargo 1',
            'weight' => 50,
            'volume' => 1,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $shpCust2 = Shipment::create([
            'shipment_number' => 'SHP-CUST2-SECRET',
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Secret Cargo 2',
            'weight' => 99,
            'volume' => 2,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_BOOKED,
        ]);

        // Customer 1 cannot see Customer 2's shipments on index
        $resWeb = $this->actingAs($this->customer1)->get(route('shipments.index'));
        $resWeb->assertStatus(200);
        $resWeb->assertSee('SHP-CUST1-ONLY');
        $resWeb->assertDontSee('SHP-CUST2-SECRET');

        // Customer 1 export only contains Customer 1's shipments
        $resExport = $this->actingAs($this->customer1)->get(route('shipments.export'));
        $resExport->assertStatus(200);
        $csv = $resExport->streamedContent();
        $this->assertStringContainsString('SHP-CUST1-ONLY', $csv);
        $this->assertStringNotContainsString('SHP-CUST2-SECRET', $csv);
    }

    /**
     * TEST 26: Default tracking map uses actual shipment from database, not demo route
     */
    public function test_26_default_tracking_map_uses_actual_shipment_not_demo(): void
    {
        $actualShipment = Shipment::create([
            'shipment_number' => 'SHP-ACTUAL-MAP',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Live Satellite Components',
            'weight' => 120,
            'volume' => 3,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(3),
            'estimated_arrival' => now()->addDays(3),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        // Access tracking page without any search/shipment_number query string
        $response = $this->actingAs($this->admin)->get(route('tracking.index'));
        $response->assertStatus(200);

        // Verify that the view receives the actual shipment from the database
        $viewShipment = $response->viewData('shipment');
        $this->assertNotNull($viewShipment);
        $this->assertEquals('SHP-ACTUAL-MAP', $viewShipment->shipment_number);
        $response->assertSee('SHP-ACTUAL-MAP');
        $response->assertSee('Leaflet / OpenStreetMap');
        $response->assertDontSee('Google Maps Engine');

        // Verify explicit demo corridor is only loaded when query explicitly requests it
        $demoResponse = $this->actingAs($this->admin)->get(route('tracking.index', ['shipment_number' => 'DEMO-FR-NORMANDY']));
        $demoResponse->assertStatus(200);
        $this->assertEquals('DEMO-FR-NORMANDY', $demoResponse->viewData('search'));
    }

    /**
     * TEST 27: Exception workflow enforces strict OPEN -> INVESTIGATING -> RESOLVED sequence
     */
    public function test_27_exception_workflow_enforces_strict_transition_sequence(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-EX-FLOW',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Medical Equipment',
            'weight' => 50,
            'volume' => 1,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $exceptionService = app(ExceptionService::class);
        $exception = $exceptionService->createException($shipment, [
            'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
            'severity' => 'HIGH',
            'description' => 'Blizzard conditions',
        ], $this->operator->id);

        $this->assertEquals(ShipmentException::STATUS_OPEN, $exception->status);

        // 1. Direct transition from OPEN to RESOLVED must be rejected
        $threw = false;
        try {
            $exceptionService->updateStatus($exception, ShipmentException::STATUS_RESOLVED, 'Attempt direct resolve', $this->operator->id);
        } catch (\InvalidArgumentException $e) {
            $threw = true;
            $this->assertStringContainsString('INVESTIGATING', $e->getMessage());
        }
        $this->assertTrue($threw, 'Direct transition from OPEN to RESOLVED must throw InvalidArgumentException.');

        // 2. Re-applying OPEN must be rejected
        $threw = false;
        try {
            $exceptionService->updateStatus($exception, ShipmentException::STATUS_OPEN, 'Still open', $this->operator->id);
        } catch (\InvalidArgumentException $e) {
            $threw = true;
        }
        $this->assertTrue($threw);

        // 3. Valid transition: OPEN -> INVESTIGATING
        $investigating = $exceptionService->updateStatus($exception, ShipmentException::STATUS_INVESTIGATING, 'Operations reviewing satellite radar', $this->operator->id);
        $this->assertEquals(ShipmentException::STATUS_INVESTIGATING, $investigating->status);

        // 4. Reverting from INVESTIGATING back to OPEN must be rejected
        $threw = false;
        try {
            $exceptionService->updateStatus($investigating, ShipmentException::STATUS_OPEN, 'Revert to open', $this->operator->id);
        } catch (\InvalidArgumentException $e) {
            $threw = true;
            $this->assertStringContainsString('revert', strtolower($e->getMessage()));
        }
        $this->assertTrue($threw, 'Reverting from INVESTIGATING to OPEN must be rejected.');

        // 5. Valid transition: INVESTIGATING -> RESOLVED
        $resolved = $exceptionService->updateStatus($investigating, ShipmentException::STATUS_RESOLVED, 'Storm bypassed, highway open', $this->operator->id);
        $this->assertEquals(ShipmentException::STATUS_RESOLVED, $resolved->status);
        $this->assertNotNull($resolved->resolved_at);

        // 6. Reopening from RESOLVED must be strictly rejected
        $threw = false;
        try {
            $exceptionService->updateStatus($resolved, ShipmentException::STATUS_OPEN, 'Try reopen', $this->operator->id);
        } catch (\InvalidArgumentException $e) {
            $threw = true;
            $this->assertStringContainsString('RESOLVED', $e->getMessage());
        }
        $this->assertTrue($threw, 'Reopening a RESOLVED exception must be strictly rejected.');
    }

    /**
     * TEST 28: Tracking chronology for exception events
     */
    public function test_28_exception_chronology_cannot_precede_latest_tracking_event(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-CHRONO-EX',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Perishables',
            'weight' => 20,
            'volume' => 1,
            'expected_departure' => now()->subDays(2),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        // Add a tracking event at now()->subHours(2)
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => Shipment::STATUS_IN_TRANSIT,
            'event_time' => now()->subHours(2),
            'location_id' => $this->origin->id,
            'location_name' => 'Transit Hub A',
            'latitude' => 37.77,
            'longitude' => -122.42,
            'status_after_event' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Departed facility',
            'is_exception' => false,
            'created_by' => $this->operator->id,
        ]);

        $exceptionService = app(ExceptionService::class);

        // Attempt to create an exception with timestamp 5 hours ago (earlier than the 2 hours ago event)
        $threw = false;
        try {
            $exceptionService->createException($shipment, [
                'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
                'severity' => 'MEDIUM',
                'description' => 'Backdated blizzard',
                'detected_at' => now()->subHours(5)->toDateTimeString(),
            ], $this->operator->id);
        } catch (\InvalidArgumentException $e) {
            $threw = true;
            $this->assertStringContainsString('earlier than the latest tracking event', $e->getMessage());
        }
        $this->assertTrue($threw, 'Creating exception with timestamp earlier than latest event must throw InvalidArgumentException.');

        // Creating exception with timestamp after latest event succeeds
        $validEx = $exceptionService->createException($shipment, [
            'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
            'severity' => 'MEDIUM',
            'description' => 'Chronologically valid blizzard',
            'detected_at' => now()->subHours(1)->toDateTimeString(),
        ], $this->operator->id);

        $this->assertNotNull($validEx);
        $this->assertEquals('WEATHER_DELAY', $validEx->exception_type);
    }

    /**
     * TEST 29: Weather API invalid payload validation and last-known fallback
     */
    public function test_29_weather_service_invalid_payload_validation_and_fallback(): void
    {
        $weatherService = app(WeatherService::class);
        $lat = 48.85;
        $lon = 2.35;
        $roundedLat = round($lat, 2);
        $roundedLon = round($lon, 2);
        Cache::forget("weather_{$roundedLat}_{$roundedLon}");
        Cache::forget("weather_last_known_{$roundedLat}_{$roundedLon}");

        // 1. HTTP 200 with empty payload must NOT return "Clear Sky"
        Http::fake([
            'https://api.open-meteo.com/*' => Http::response([], 200),
        ]);
        $emptyRes = $weatherService->getWeather($lat, $lon);
        $this->assertEquals('unavailable', $emptyRes['status'], 'Empty payload must return unavailable, not Clear Sky.');
        $this->assertNull($emptyRes['temperature']);
        $this->assertNotEquals('Clear Sky', $emptyRes['condition']);

        // 2. HTTP 200 with incomplete payload (e.g. missing weather_code or temperature)
        Cache::forget("weather_{$roundedLat}_{$roundedLon}");
        Http::fake([
            'https://api.open-meteo.com/*' => Http::response([
                'current' => ['wind_speed_10m' => 15.0], // missing weather_code and temperature_2m
            ], 200),
        ]);
        $incompleteRes = $weatherService->getWeather($lat, $lon);
        $this->assertEquals('unavailable', $incompleteRes['status'], 'Incomplete payload must return unavailable.');

        // 3. HTTP 500 server error
        Cache::forget("weather_{$roundedLat}_{$roundedLon}");
        Http::fake([
            'https://api.open-meteo.com/*' => Http::response('Internal Server Error', 500),
        ]);
        $serverErrorRes = $weatherService->getWeather($lat, $lon);
        $this->assertEquals('unavailable', $serverErrorRes['status']);

        // 4. Stale fallback: when a valid last-known observation exists in persistent cache
        Cache::forget("weather_{$roundedLat}_{$roundedLon}");
        Cache::put("weather_last_known_{$roundedLat}_{$roundedLon}", [
            'status' => 'available',
            'temperature' => 18.5,
            'wind_speed' => 12.0,
            'precipitation' => 0.0,
            'condition' => 'Mainly Clear / Partly Cloudy',
            'weather_code' => 1,
            'is_adverse' => false,
            'timestamp' => now()->subHours(2)->toIso8601String(),
            'is_stale' => false,
            'notice' => 'Historical observation',
        ], now()->addDays(7));

        // When external API times out or fails with 500
        Http::fake([
            'https://api.open-meteo.com/*' => Http::response('Gateway Timeout', 504),
        ]);
        $staleRes = $weatherService->getWeather($lat, $lon);
        $this->assertEquals('stale', $staleRes['status'], 'Service must serve last-known observation tagged as stale.');
        $this->assertTrue($staleRes['is_stale']);
        $this->assertEquals(18.5, $staleRes['temperature']);
    }

    /**
     * TEST 30: API exception status transition rules and customer authorization
     */
    public function test_30_api_exception_status_transition_rules_and_authorization(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHP-API-EX',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_location_id' => $this->origin->id,
            'destination_location_id' => $this->destination->id,
            'cargo_type' => 'Industrial Parts',
            'weight' => 75,
            'volume' => 2,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(2),
            'estimated_arrival' => now()->addDays(2),
            'current_status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $exception = app(ExceptionService::class)->createException($shipment, [
            'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
            'severity' => 'MEDIUM',
            'description' => 'Container terminal queue',
        ], $this->operator->id);

        // Customer cannot update exceptions via API (403 Forbidden)
        Sanctum::actingAs($this->customer1);
        $custRes = $this->patchJson("/api/exceptions/{$exception->id}", ['status' => 'INVESTIGATING']);
        $custRes->assertStatus(403);

        // Operator attempting direct transition to RESOLVED without INVESTIGATING (422 Unprocessable)
        Sanctum::actingAs($this->operator);
        $illegalRes = $this->patchJson("/api/exceptions/{$exception->id}", [
            'status' => 'RESOLVED',
            'resolution_notes' => 'Attempt illegal jump',
        ]);
        $illegalRes->assertStatus(422);
        $illegalRes->assertJsonFragment(['success' => false]);
        $this->assertStringContainsString('INVESTIGATING', $illegalRes->json('message'));

        // Operator valid transition to INVESTIGATING (200 OK)
        $validRes = $this->patchJson("/api/exceptions/{$exception->id}", [
            'status' => 'INVESTIGATING',
            'resolution_notes' => 'Dispatcher reviewing queue metrics',
        ]);
        $validRes->assertStatus(200);
        $validRes->assertJsonFragment(['success' => true]);
        $this->assertEquals('INVESTIGATING', $exception->fresh()->status);
    }
}
