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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
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

        // Resolve exception
        $resolved = $exceptionService->updateStatus($exception, 'RESOLVED', 'Road cleared by highway patrol', $this->operator->id);
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
}
