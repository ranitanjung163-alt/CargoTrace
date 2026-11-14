<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic international logistics data.
     * All entities use idempotent firstOrCreate to protect operational data from being overwritten.
     * Tracking events are generated in strict chronological progression:
     * CREATED -> BOOKED -> PICKED_UP -> IN_TRANSIT -> [EXCEPTIONS] -> DELIVERED.
     */
    public function run(): void
    {
        // Concurrency Guard: Enforce single-process execution strategy using an atomic cache lock.
        // This prevents race conditions on child tables (tracking_events, shipment_items, exceptions, notifications)
        // which legitimately permit multiple operational records and therefore do not have DB-level composite unique keys.
        try {
            $seederLock = Cache::lock('worldcargo360_database_seeder_mutex', 60);
            $hasLock = $seederLock->get();
        } catch (\Throwable $e) {
            $seederLock = null;
            $hasLock = true;
        }

        if (! $hasLock) {
            return;
        }

        try {
            // 1. DEMO USERS (Admin, Operator, Customer - created only if not present, never overwritten)
        $admin = User::firstOrCreate(
            ['email' => 'admin@cargotrace.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
                'phone' => '+1-555-0100',
                'company_name' => 'WorldCargo 360 Global HQ',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Admin+HQ&background=3182CE&color=fff',
            ]
        );

        $operator = User::firstOrCreate(
            ['email' => 'operator@cargotrace.com'],
            [
                'name' => 'Logistics Dispatcher',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_OPERATOR,
                'phone' => '+1-555-0101',
                'company_name' => 'Port Operations Center',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Logistics+Op&background=16A36A&color=fff',
            ]
        );

        $customer1 = User::firstOrCreate(
            ['email' => 'customer@cargotrace.com'],
            [
                'name' => 'Global Industries Ltd',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '+1-555-0102',
                'company_name' => 'Global Industries Ltd',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Global+Ind&background=E59A32&color=fff',
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'tech@cargotrace.com'],
            [
                'name' => 'Tech Solutions Corp',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '+1-555-0103',
                'company_name' => 'Tech Solutions Corp',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Tech+Sol&background=64748B&color=fff',
            ]
        );

        $customer3 = User::firstOrCreate(
            ['email' => 'retail@cargotrace.com'],
            [
                'name' => 'Retail Hub International',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '+1-555-0104',
                'company_name' => 'Retail Hub International',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Retail+Hub&background=DC6262&color=fff',
            ]
        );

        $customer4 = User::firstOrCreate(
            ['email' => 'alpha@cargotrace.com'],
            [
                'name' => 'Alpha Manufacturing',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '+1-555-0105',
                'company_name' => 'Alpha Manufacturing',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Alpha+Mfg&background=3182CE&color=fff',
            ]
        );

        $customer5 = User::firstOrCreate(
            ['email' => 'omega@cargotrace.com'],
            [
                'name' => 'Omega Retail Group',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '+1-555-0106',
                'company_name' => 'Omega Retail Group',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Omega+Ret&background=183653&color=fff',
            ]
        );

        // 2. CARRIERS (Created only if code does not exist; master updates by admin preserved)
        $fastShip = Carrier::firstOrCreate(
            ['code' => 'FST'],
            [
                'name' => 'FastShip Express',
                'contact_email' => 'dispatch@fastship.com',
                'contact_phone' => '+1-800-327-8744',
                'service_type' => 'Air Freight & Express Road',
                'is_active' => true,
            ]
        );

        $cargoPro = Carrier::firstOrCreate(
            ['code' => 'CGP'],
            [
                'name' => 'CargoPro Global',
                'contact_email' => 'ops@cargopro-global.com',
                'contact_phone' => '+1-800-427-4677',
                'service_type' => 'Ocean & Multimodal',
                'is_active' => true,
            ]
        );

        $logisticsPartners = Carrier::firstOrCreate(
            ['code' => 'LGP'],
            [
                'name' => 'Logistics Partners Worldwide',
                'contact_email' => 'info@logisticspartners.com',
                'contact_phone' => '+1-800-564-4787',
                'service_type' => 'Global Logistics Network',
                'is_active' => true,
            ]
        );

        $dhl = Carrier::firstOrCreate(
            ['code' => 'DHL'],
            [
                'name' => 'DHL Global Forwarding',
                'contact_email' => 'cargo@dhl.com',
                'contact_phone' => '+49-228-1820',
                'service_type' => 'Air & Ocean Freight',
                'is_active' => true,
            ]
        );

        $maersk = Carrier::firstOrCreate(
            ['code' => 'MSK'],
            [
                'name' => 'Maersk Line',
                'contact_email' => 'freight@maersk.com',
                'contact_phone' => '+45-3363-3363',
                'service_type' => 'Ocean Container Shipping',
                'is_active' => true,
            ]
        );

        // 3. LOCATIONS (Worldwide coverage across all continents - created only if code missing)
        $locations = [
            'LAX' => ['code' => 'US-LAX', 'name' => 'Los Angeles Port Hub', 'city' => 'Los Angeles', 'state_province' => 'CA', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 34.052235, 'longitude' => -118.243683, 'type' => 'PORT'],
            'CHI' => ['code' => 'US-CHI', 'name' => 'Chicago Intermodal Terminal', 'city' => 'Chicago', 'state_province' => 'IL', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 41.878113, 'longitude' => -87.629799, 'type' => 'DISTRIBUTION_CENTER'],
            'SFO' => ['code' => 'US-SFO', 'name' => 'San Francisco Bay Logistics', 'city' => 'San Francisco', 'state_province' => 'CA', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 37.774929, 'longitude' => -122.419416, 'type' => 'AIRPORT'],
            'DAL' => ['code' => 'US-DAL', 'name' => 'Dallas Freight Gateway', 'city' => 'Dallas', 'state_province' => 'TX', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 32.776664, 'longitude' => -96.796988, 'type' => 'DISTRIBUTION_CENTER'],
            'NYC' => ['code' => 'US-NYC', 'name' => 'New York Marine Terminal', 'city' => 'New York', 'state_province' => 'NY', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 40.712776, 'longitude' => -74.005974, 'type' => 'PORT'],
            'MIA' => ['code' => 'US-MIA', 'name' => 'Miami Cargo Hub', 'city' => 'Miami', 'state_province' => 'FL', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 25.761680, 'longitude' => -80.191790, 'type' => 'PORT'],
            'DET' => ['code' => 'US-DET', 'name' => 'Detroit Logistics Depot', 'city' => 'Detroit', 'state_province' => 'MI', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 42.331427, 'longitude' => -83.045754, 'type' => 'WAREHOUSE'],
            'SEA' => ['code' => 'US-SEA', 'name' => 'Seattle Northwest Port', 'city' => 'Seattle', 'state_province' => 'WA', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 47.606209, 'longitude' => -122.332071, 'type' => 'PORT'],
            'ATX' => ['code' => 'US-ATX', 'name' => 'Austin Distribution Center', 'city' => 'Austin', 'state_province' => 'TX', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 30.267153, 'longitude' => -97.743061, 'type' => 'DISTRIBUTION_CENTER'],
            'DEN' => ['code' => 'US-DEN', 'name' => 'Denver Central Hub', 'city' => 'Denver', 'state_province' => 'CO', 'country' => 'United States', 'country_code' => 'US', 'latitude' => 39.739236, 'longitude' => -104.990251, 'type' => 'WAREHOUSE'],
            'RTM' => ['code' => 'NL-RTM', 'name' => 'Port of Rotterdam', 'city' => 'Rotterdam', 'state_province' => 'South Holland', 'country' => 'Netherlands', 'country_code' => 'NL', 'latitude' => 51.924420, 'longitude' => 4.477733, 'type' => 'PORT'],
            'FRA' => ['code' => 'DE-FRA', 'name' => 'Frankfurt CargoCity South', 'city' => 'Frankfurt', 'state_province' => 'Hesse', 'country' => 'Germany', 'country_code' => 'DE', 'latitude' => 50.110922, 'longitude' => 8.682127, 'type' => 'AIRPORT'],
            'SIN' => ['code' => 'SG-SIN', 'name' => 'PSA Singapore Terminals', 'city' => 'Singapore', 'state_province' => null, 'country' => 'Singapore', 'country_code' => 'SG', 'latitude' => 1.352083, 'longitude' => 103.819836, 'type' => 'PORT'],
            'JKT' => ['code' => 'ID-JKT', 'name' => 'Tanjung Priok Container Terminal', 'city' => 'Jakarta', 'state_province' => 'DKI Jakarta', 'country' => 'Indonesia', 'country_code' => 'ID', 'latitude' => -6.208763, 'longitude' => 106.845599, 'type' => 'PORT'],
            'SUB' => ['code' => 'ID-SUB', 'name' => 'Tanjung Perak Port', 'city' => 'Surabaya', 'state_province' => 'Jawa Timur', 'country' => 'Indonesia', 'country_code' => 'ID', 'latitude' => -7.257472, 'longitude' => 112.752088, 'type' => 'PORT'],
            'TYO' => ['code' => 'JP-TYO', 'name' => 'Port of Tokyo Oi Terminal', 'city' => 'Tokyo', 'state_province' => 'Kanto', 'country' => 'Japan', 'country_code' => 'JP', 'latitude' => 35.676192, 'longitude' => 139.650311, 'type' => 'PORT'],
            'SHA' => ['code' => 'CN-SHA', 'name' => 'Shanghai Yangshan Deepwater Port', 'city' => 'Shanghai', 'state_province' => null, 'country' => 'China', 'country_code' => 'CN', 'latitude' => 31.230416, 'longitude' => 121.473701, 'type' => 'PORT'],
            'DXB' => ['code' => 'AE-DXB', 'name' => 'DP World Jebel Ali Hub', 'city' => 'Dubai', 'state_province' => 'Dubai', 'country' => 'United Arab Emirates', 'country_code' => 'AE', 'latitude' => 25.204849, 'longitude' => 55.270783, 'type' => 'PORT'],
            'SYD' => ['code' => 'AU-SYD', 'name' => 'Port Botany Sydney', 'city' => 'Sydney', 'state_province' => 'NSW', 'country' => 'Australia', 'country_code' => 'AU', 'latitude' => -33.868820, 'longitude' => 151.209296, 'type' => 'PORT'],
            'SSZ' => ['code' => 'BR-SSZ', 'name' => 'Port of Santos', 'city' => 'Santos', 'state_province' => 'São Paulo', 'country' => 'Brazil', 'country_code' => 'BR', 'latitude' => -23.960833, 'longitude' => -46.333611, 'type' => 'PORT'],
            'DUR' => ['code' => 'ZA-DUR', 'name' => 'Port of Durban Marine Terminal', 'city' => 'Durban', 'state_province' => 'KwaZulu-Natal', 'country' => 'South Africa', 'country_code' => 'ZA', 'latitude' => -29.858680, 'longitude' => 31.021840, 'type' => 'PORT'],
        ];

        $locModels = [];
        foreach ($locations as $key => $loc) {
            $locModels[$key] = Location::firstOrCreate(['code' => $loc['code']], $loc);
        }

        // 4. SHIPMENTS (Exact matches to lecturer reference table + worldwide shipments)
        $shipmentsData = [
            // Row 1 from Lecturer Screen: SHP-2024-001284 | Global Industries | FastShip | Los Angeles, CA | Chicago, IL | DELAYED
            [
                'shipment_number' => 'SHP-2024-001284',
                'customer_id' => $customer1->id,
                'carrier_id' => $fastShip->id,
                'origin_location_id' => $locModels['LAX']->id,
                'destination_location_id' => $locModels['CHI']->id,
                'current_location_id' => $locModels['LAX']->id,
                'cargo_type' => 'Electronics & Computing Components',
                'weight' => 4500.00,
                'volume' => 28.50,
                'expected_departure' => Carbon::now()->subDays(3),
                'actual_departure' => Carbon::now()->subDays(3)->addHours(2),
                'expected_arrival' => Carbon::now()->addDays(2),
                'estimated_arrival' => Carbon::now()->addDays(2)->addHours(18), // Delayed by 18 hours (>12h threshold = MEDIUM)
                'current_status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => true,
                'delay_duration_minutes' => 1080,
                'delay_severity' => Shipment::SEVERITY_MEDIUM,
                'delay_reason' => 'Severe weather blizzard on Midwest corridor (WEATHER_DELAY)',
                'description' => 'Priority automotive computer modules dispatch.',
            ],
            // Row 2: SHP-2024-001223 | Tech Solutions | CargoPro | San Francisco, CA | Dallas, TX | IN TRANSIT
            [
                'shipment_number' => 'SHP-2024-001223',
                'customer_id' => $customer2->id,
                'carrier_id' => $cargoPro->id,
                'origin_location_id' => $locModels['SFO']->id,
                'destination_location_id' => $locModels['DAL']->id,
                'current_location_id' => $locModels['SFO']->id,
                'cargo_type' => 'Telecommunications Equipment',
                'weight' => 2800.00,
                'volume' => 15.00,
                'expected_departure' => Carbon::now()->subDays(2),
                'actual_departure' => Carbon::now()->subDays(2)->addHours(1),
                'expected_arrival' => Carbon::now()->addDays(1),
                'estimated_arrival' => Carbon::now()->addDays(1),
                'current_status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Standard route telecommunications infrastructure shipment.',
            ],
            // Row 3: SHP-2024-001222 | Retail Hub | Logistics Partners | New York, NY | Miami, FL | IN TRANSIT
            [
                'shipment_number' => 'SHP-2024-001222',
                'customer_id' => $customer3->id,
                'carrier_id' => $logisticsPartners->id,
                'origin_location_id' => $locModels['NYC']->id,
                'destination_location_id' => $locModels['MIA']->id,
                'current_location_id' => $locModels['NYC']->id,
                'cargo_type' => 'Consumer Apparel & Footwear',
                'weight' => 6200.00,
                'volume' => 42.00,
                'expected_departure' => Carbon::now()->subDays(1),
                'actual_departure' => Carbon::now()->subDays(1),
                'expected_arrival' => Carbon::now()->addDays(3),
                'estimated_arrival' => Carbon::now()->addDays(3),
                'current_status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Seasonal retail catalog replenishment.',
            ],
            // Row 4: SHP-2024-001281 | Alpha Manufacturing | FastShip | Detroit, MI | Seattle, WA | IN TRANSIT
            [
                'shipment_number' => 'SHP-2024-001281',
                'customer_id' => $customer4->id,
                'carrier_id' => $fastShip->id,
                'origin_location_id' => $locModels['DET']->id,
                'destination_location_id' => $locModels['SEA']->id,
                'current_location_id' => $locModels['DET']->id,
                'cargo_type' => 'Automotive Precision Stampings',
                'weight' => 9100.00,
                'volume' => 36.00,
                'expected_departure' => Carbon::now()->subHours(18),
                'actual_departure' => Carbon::now()->subHours(18),
                'expected_arrival' => Carbon::now()->addDays(2),
                'estimated_arrival' => Carbon::now()->addDays(2),
                'current_status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Assembly plant scheduled deliveries.',
            ],
            // Row 5: SHP-2024-001280 | Omega Retail | CargoPro | Austin, TX | Denver, CO | IN TRANSIT
            [
                'shipment_number' => 'SHP-2024-001280',
                'customer_id' => $customer5->id,
                'carrier_id' => $cargoPro->id,
                'origin_location_id' => $locModels['ATX']->id,
                'destination_location_id' => $locModels['DEN']->id,
                'current_location_id' => $locModels['ATX']->id,
                'cargo_type' => 'Medical Diagnostic Equipment',
                'weight' => 1950.00,
                'volume' => 12.00,
                'expected_departure' => Carbon::now()->subHours(10),
                'actual_departure' => Carbon::now()->subHours(10),
                'expected_arrival' => Carbon::now()->addDays(1),
                'estimated_arrival' => Carbon::now()->addDays(1),
                'current_status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Temperature-controlled clinical instruments.',
            ],
            // Row 6 (Worldwide Maritime: Rotterdam -> Jakarta): IN TRANSIT
            [
                'shipment_number' => 'SHP-2024-002010',
                'customer_id' => $customer1->id,
                'carrier_id' => $maersk->id,
                'origin_location_id' => $locModels['RTM']->id,
                'destination_location_id' => $locModels['JKT']->id,
                'current_location_id' => $locModels['DXB']->id,
                'cargo_type' => 'Industrial Marine Turbines',
                'weight' => 24500.00,
                'volume' => 85.00,
                'expected_departure' => Carbon::now()->subDays(12),
                'actual_departure' => Carbon::now()->subDays(12),
                'expected_arrival' => Carbon::now()->addDays(8),
                'estimated_arrival' => Carbon::now()->addDays(8),
                'current_status' => Shipment::STATUS_IN_TRANSIT,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Heavy industrial project cargo maritime route via Suez & Dubai.',
            ],
            // Row 7 (Worldwide Air Freight: Tokyo -> Los Angeles): DELIVERED
            [
                'shipment_number' => 'SHP-2024-002011',
                'customer_id' => $customer2->id,
                'carrier_id' => $fastShip->id,
                'origin_location_id' => $locModels['TYO']->id,
                'destination_location_id' => $locModels['LAX']->id,
                'current_location_id' => $locModels['LAX']->id,
                'cargo_type' => 'Semiconductors & Sensors',
                'weight' => 1200.00,
                'volume' => 8.50,
                'expected_departure' => Carbon::now()->subDays(5),
                'actual_departure' => Carbon::now()->subDays(5),
                'expected_arrival' => Carbon::now()->subDays(1),
                'estimated_arrival' => Carbon::now()->subDays(1),
                'actual_delivered_at' => Carbon::now()->subDays(1),
                'current_status' => Shipment::STATUS_DELIVERED,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Trans-Pacific express semiconductor air consignment.',
            ],
            // Row 8 (Worldwide: Shanghai -> Frankfurt): BOOKED
            [
                'shipment_number' => 'SHP-2024-002012',
                'customer_id' => $customer3->id,
                'carrier_id' => $dhl->id,
                'origin_location_id' => $locModels['SHA']->id,
                'destination_location_id' => $locModels['FRA']->id,
                'current_location_id' => $locModels['SHA']->id,
                'cargo_type' => 'Pharmaceutical Vaccines',
                'weight' => 850.00,
                'volume' => 6.20,
                'expected_departure' => Carbon::now()->addDay(),
                'actual_departure' => null,
                'expected_arrival' => Carbon::now()->addDays(5),
                'estimated_arrival' => Carbon::now()->addDays(5),
                'current_status' => Shipment::STATUS_BOOKED,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Cold-chain air charter booking confirmed.',
            ],
            // Row 9 (Worldwide: Singapore -> Sydney): DELIVERED
            [
                'shipment_number' => 'SHP-2024-002013',
                'customer_id' => $customer4->id,
                'carrier_id' => $logisticsPartners->id,
                'origin_location_id' => $locModels['SIN']->id,
                'destination_location_id' => $locModels['SYD']->id,
                'current_location_id' => $locModels['SYD']->id,
                'cargo_type' => 'Specialty Food & Beverages',
                'weight' => 5400.00,
                'volume' => 32.00,
                'expected_departure' => Carbon::now()->subDays(8),
                'actual_departure' => Carbon::now()->subDays(8),
                'expected_arrival' => Carbon::now()->subDays(2),
                'estimated_arrival' => Carbon::now()->subDays(2),
                'actual_delivered_at' => Carbon::now()->subDays(2),
                'current_status' => Shipment::STATUS_DELIVERED,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Direct Oceania liner dispatch completed.',
            ],
            // Row 10 (Worldwide: Dubai -> Durban): CREATED
            [
                'shipment_number' => 'SHP-2024-002014',
                'customer_id' => $customer5->id,
                'carrier_id' => $maersk->id,
                'origin_location_id' => $locModels['DXB']->id,
                'destination_location_id' => $locModels['DUR']->id,
                'current_location_id' => $locModels['DXB']->id,
                'cargo_type' => 'Automotive Assembly Kits',
                'weight' => 18000.00,
                'volume' => 60.00,
                'expected_departure' => Carbon::now()->addDays(2),
                'actual_departure' => null,
                'expected_arrival' => Carbon::now()->addDays(14),
                'estimated_arrival' => Carbon::now()->addDays(14),
                'current_status' => Shipment::STATUS_CREATED,
                'is_delayed' => false,
                'delay_duration_minutes' => 0,
                'delay_severity' => Shipment::SEVERITY_NONE,
                'delay_reason' => null,
                'description' => 'Indian Ocean shipping lane booking pending vessel assignment.',
            ],
        ];

        foreach ($shipmentsData as $sData) {
            $shipmentNumber = $sData['shipment_number'];

            // Compute strictly chronological event timestamps based on shipment operational schedule
            $baseDeparture = Carbon::parse($sData['actual_departure'] ?? $sData['expected_departure'] ?? now());
            $actualDeparture = ! empty($sData['actual_departure']) ? Carbon::parse($sData['actual_departure']) : null;
            $actualDelivered = ! empty($sData['actual_delivered_at']) ? Carbon::parse($sData['actual_delivered_at']) : null;

            // 1. BOOKED: 12 hours prior to scheduled departure
            $bookedTime = $baseDeparture->copy()->subHours(12);

            // 2. CREATED: Strictly precedes BOOKED by 12 hours (24h before base departure)
            // For shipments still in initial STATUS_CREATED, booking was generated recently
            if ($sData['current_status'] === Shipment::STATUS_CREATED) {
                $createdTime = Carbon::now()->subHours(8);
            } else {
                $createdTime = $bookedTime->copy()->subHours(12);
            }

            // 3. PICKED_UP: Occurs at actual departure time (or base departure)
            $pickedUpTime = $actualDeparture ? $actualDeparture->copy() : $baseDeparture->copy();

            // 4. IN_TRANSIT: Occurs 6 hours after departure
            $inTransitTime = $pickedUpTime->copy()->addHours(6);

            // 5. DELIVERED: Occurs at actual delivered timestamp (strictly after in_transit)
            $deliveredTime = $actualDelivered ? $actualDelivered->copy() : Carbon::parse($sData['expected_arrival'] ?? now());

            // Create shipment only if it does not already exist, preserving all operational data on existing records
            $shipment = Shipment::firstOrCreate(
                ['shipment_number' => $shipmentNumber],
                $sData
            );

            // Create items only if not already present, preserving operational edits
            ShipmentItem::firstOrCreate(
                ['shipment_id' => $shipment->id, 'item_name' => $shipment->cargo_type . ' Unit Pack'],
                [
                    'quantity' => 10,
                    'unit' => 'pallets',
                    'weight' => $shipment->weight,
                    'hs_code' => '8471.30.01',
                    'description' => 'Primary sealed logistics packaging.',
                ]
            );

            // EVENT 1: CREATED (Initial order creation milestone)
            TrackingEvent::firstOrCreate(
                [
                    'shipment_id' => $shipment->id,
                    'event_type' => 'CREATED',
                    'description' => 'Shipment booking generated and processed in WorldCargo 360.',
                ],
                [
                    'event_time' => $createdTime,
                    'location_id' => $shipment->origin_location_id,
                    'location_name' => $shipment->origin->name,
                    'latitude' => $shipment->origin->latitude,
                    'longitude' => $shipment->origin->longitude,
                    'status_after_event' => 'CREATED',
                    'is_exception' => false,
                    'created_by' => $operator->id,
                ]
            );

            // EVENT 2: BOOKED (Carrier slot confirmed milestone)
            if ($shipment->current_status !== Shipment::STATUS_CREATED) {
                TrackingEvent::firstOrCreate(
                    [
                        'shipment_id' => $shipment->id,
                        'event_type' => 'BOOKED',
                        'description' => 'Carrier booking slot confirmed. Awaiting terminal pickup.',
                    ],
                    [
                        'event_time' => $bookedTime,
                        'location_id' => $shipment->origin_location_id,
                        'location_name' => $shipment->origin->name,
                        'latitude' => $shipment->origin->latitude,
                        'longitude' => $shipment->origin->longitude,
                        'status_after_event' => 'BOOKED',
                        'is_exception' => false,
                        'created_by' => $operator->id,
                    ]
                );
            }

            // EVENT 3: PICKED_UP (Consignment picked up and loaded milestone)
            if (in_array($shipment->current_status, [Shipment::STATUS_PICKED_UP, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_DELIVERED], true)) {
                TrackingEvent::firstOrCreate(
                    [
                        'shipment_id' => $shipment->id,
                        'event_type' => 'PICKED_UP',
                        'description' => 'Consignment picked up and loaded by carrier dispatch.',
                    ],
                    [
                        'event_time' => $pickedUpTime,
                        'location_id' => $shipment->origin_location_id,
                        'location_name' => $shipment->origin->name,
                        'latitude' => $shipment->origin->latitude,
                        'longitude' => $shipment->origin->longitude,
                        'status_after_event' => 'PICKED_UP',
                        'is_exception' => false,
                        'created_by' => $operator->id,
                    ]
                );
            }

            // EVENT 4: IN_TRANSIT (En route via primary corridor milestone)
            if (in_array($shipment->current_status, [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_DELIVERED], true)) {
                TrackingEvent::firstOrCreate(
                    [
                        'shipment_id' => $shipment->id,
                        'event_type' => 'IN_TRANSIT',
                        'description' => 'Shipment is en route through scheduled international transit corridor.',
                    ],
                    [
                        'event_time' => $inTransitTime,
                        'location_id' => $shipment->current_location_id,
                        'location_name' => $shipment->currentLocation->name ?? 'En Route Waypoint',
                        'latitude' => $shipment->currentLocation->latitude ?? 35.0,
                        'longitude' => $shipment->currentLocation->longitude ?? -100.0,
                        'status_after_event' => 'IN_TRANSIT',
                        'is_exception' => false,
                        'created_by' => $operator->id,
                    ]
                );
            }

            // EVENT 5: DELIVERED (Final delivery confirmed milestone)
            // Strict check: if shipment already has ANY tracking event with event_type = DELIVERED, never create duplicate
            $hasDeliveredEvent = TrackingEvent::where('shipment_id', $shipment->id)
                ->where('event_type', Shipment::STATUS_DELIVERED)
                ->exists();

            if ($shipment->current_status === Shipment::STATUS_DELIVERED && ! $hasDeliveredEvent) {
                // Ensure deliveredTime is strictly chronological and never in the future
                $validDeliveredTime = $shipment->actual_delivered_at
                    ? Carbon::parse($shipment->actual_delivered_at)
                    : ($actualDelivered ? $actualDelivered->copy() : Carbon::parse($sData['expected_arrival'] ?? now()));

                if ($validDeliveredTime->isFuture()) {
                    $validDeliveredTime = Carbon::now();
                }

                TrackingEvent::firstOrCreate(
                    [
                        'shipment_id' => $shipment->id,
                        'event_type' => Shipment::STATUS_DELIVERED,
                        'description' => 'Package successfully delivered and signed by consignee.',
                    ],
                    [
                        'event_time' => $validDeliveredTime,
                        'location_id' => $shipment->destination_location_id,
                        'location_name' => $shipment->destination->name,
                        'latitude' => $shipment->destination->latitude,
                        'longitude' => $shipment->destination->longitude,
                        'status_after_event' => Shipment::STATUS_DELIVERED,
                        'is_exception' => false,
                        'created_by' => $operator->id,
                    ]
                );
            }
        }

        // 5. EXCEPTIONS & NOTIFICATIONS FOR THE DELAYED SHIPMENT (SHP-2024-001284)
        $delayedShipment = Shipment::where('shipment_number', 'SHP-2024-001284')->first();
        if ($delayedShipment) {
            // Verify shipment is still demonstrably in its original demo delayed state:
            // 1. Must still be IN_TRANSIT (not DELIVERED or cancelled)
            // 2. Must still have is_delayed = true
            // 3. Must still have weather delay specified in delay_reason
            $isDemoWeatherDelayState = $delayedShipment->current_status === Shipment::STATUS_IN_TRANSIT
                && (bool) $delayedShipment->is_delayed === true
                && (
                    empty($delayedShipment->delay_reason) ||
                    str_contains(strtolower($delayedShipment->delay_reason), 'weather') ||
                    str_contains(strtolower($delayedShipment->delay_reason), 'blizzard')
                );

            if ($isDemoWeatherDelayState) {
                // Check if an exception of type WEATHER_DELAY already exists
                $existingWeatherException = ShipmentException::where('shipment_id', $delayedShipment->id)
                    ->where('exception_type', ShipmentException::TYPE_WEATHER_DELAY)
                    ->first();

                $inTransitEvt = TrackingEvent::where('shipment_id', $delayedShipment->id)
                    ->where('event_type', 'IN_TRANSIT')
                    ->first();

                $inTransitTime = $inTransitEvt ? Carbon::parse($inTransitEvt->event_time) : Carbon::parse($delayedShipment->expected_departure)->addHours(6);
                $weatherDetectedAt = $inTransitTime->copy()->addHours(24);

                // Create exception only if not already present; never reopen resolved exceptions
                if (! $existingWeatherException) {
                    ShipmentException::create([
                        'shipment_id' => $delayedShipment->id,
                        'exception_type' => ShipmentException::TYPE_WEATHER_DELAY,
                        'severity' => 'MEDIUM',
                        'status' => ShipmentException::STATUS_OPEN,
                        'description' => 'Severe blizzard alert across Rocky Mountain & Midwest transit zone. Highway safety hold in place.',
                        'detected_at' => $weatherDetectedAt,
                        'created_by' => $operator->id,
                    ]);
                }

                // Exception Tracking Event: create only if no WEATHER_DELAY event exists
                $hasWeatherEvent = TrackingEvent::where('shipment_id', $delayedShipment->id)
                    ->where('event_type', 'WEATHER_DELAY')
                    ->exists();

                if (! $hasWeatherEvent) {
                    TrackingEvent::create([
                        'shipment_id' => $delayedShipment->id,
                        'event_type' => 'WEATHER_DELAY',
                        'description' => 'EXCEPTION [WEATHER_DELAY]: Blizzard alert hold in Denver corridor. ETA rescheduled +18 hours.',
                        'event_time' => $weatherDetectedAt,
                        'location_id' => $locModels['DEN']->id,
                        'location_name' => 'Denver Regional Hold Area',
                        'latitude' => 39.739236,
                        'longitude' => -104.990251,
                        'status_after_event' => 'IN_TRANSIT',
                        'is_exception' => true,
                        'created_by' => $operator->id,
                    ]);
                }

                // Notification: create only if no SHIPMENT_DELAYED notification exists
                $hasWeatherNotif = Notification::where('shipment_id', $delayedShipment->id)
                    ->where('type', 'SHIPMENT_DELAYED')
                    ->exists();

                if (! $hasWeatherNotif) {
                    Notification::create([
                        'shipment_id' => $delayedShipment->id,
                        'title' => 'Severe Weather Delay Alert',
                        'user_id' => $delayedShipment->customer_id,
                        'message' => 'Your shipment SHP-2024-001284 has encountered a weather delay (Blizzard hold) near Denver. Estimated arrival adjusted.',
                        'type' => 'SHIPMENT_DELAYED',
                        'is_read' => false,
                    ]);
                }
            }
        }

        // Additional Exceptions for other shipments
        $shp2010 = Shipment::where('shipment_number', 'SHP-2024-002010')->first();
        if ($shp2010 && $shp2010->current_status === Shipment::STATUS_IN_TRANSIT) {
            $hasPortCongestion = ShipmentException::where('shipment_id', $shp2010->id)
                ->where('exception_type', ShipmentException::TYPE_PORT_CONGESTION)
                ->exists();

            if (! $hasPortCongestion) {
                $inTransit2010 = TrackingEvent::where('shipment_id', $shp2010->id)
                    ->where('event_type', 'IN_TRANSIT')
                    ->where('description', 'Shipment is en route through scheduled international transit corridor.')
                    ->first();

                $inTransit2010Time = $inTransit2010 ? Carbon::parse($inTransit2010->event_time) : Carbon::parse($shp2010->expected_departure)->addHours(6);
                $portCongestionDetectedAt = $inTransit2010Time->copy()->addDays(3);

                ShipmentException::create([
                    'shipment_id' => $shp2010->id,
                    'exception_type' => ShipmentException::TYPE_PORT_CONGESTION,
                    'severity' => 'LOW',
                    'status' => ShipmentException::STATUS_INVESTIGATING,
                    'description' => 'Berthing wait time at Jebel Ali Hub extended by 8 hours due to high vessel queue.',
                    'detected_at' => $portCongestionDetectedAt,
                    'created_by' => $operator->id,
                ]);
            }
        }

        $shp2011 = Shipment::where('shipment_number', 'SHP-2024-002011')->first();
        if ($shp2011) {
            $hasCustomsHold = ShipmentException::where('shipment_id', $shp2011->id)
                ->where('exception_type', ShipmentException::TYPE_CUSTOMS_HOLD)
                ->exists();

            if (! $hasCustomsHold) {
                $inTransit2011 = TrackingEvent::where('shipment_id', $shp2011->id)
                    ->where('event_type', 'IN_TRANSIT')
                    ->where('description', 'Shipment is en route through scheduled international transit corridor.')
                    ->first();

                $inTransit2011Time = $inTransit2011 ? Carbon::parse($inTransit2011->event_time) : Carbon::parse($shp2011->expected_departure)->addHours(6);
                $customsDetectedAt = $inTransit2011Time->copy()->addHours(24);
                $customsResolvedAt = $customsDetectedAt->copy()->addHours(18);

                ShipmentException::create([
                    'shipment_id' => $shp2011->id,
                    'exception_type' => ShipmentException::TYPE_CUSTOMS_HOLD,
                    'severity' => 'LOW',
                    'status' => ShipmentException::STATUS_RESOLVED,
                    'description' => 'Routine CBP customs documentation check at LAX Air Cargo terminal.',
                    'detected_at' => $customsDetectedAt,
                    'resolved_at' => $customsResolvedAt,
                    'resolution_notes' => 'Import duty declaration cleared. Released for final delivery.',
                    'created_by' => $operator->id,
                    'resolved_by' => $admin->id,
                ]);
            }
        }

        // 6. INITIAL AUDIT LOGS (Immutable append-only demo baseline records)
        AuditLog::firstOrCreate(
            ['action' => 'SYSTEM_INITIALIZATION', 'user_id' => $admin->id],
            [
                'entity_type' => 'System',
                'entity_id' => null,
                'old_values' => null,
                'new_values' => ['version' => '1.0.0', 'environment' => 'production_ready', 'seeded_baseline' => true],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'WorldCargo360-System/1.0',
                'created_at' => now()->subDays(7),
            ]
        );

        AuditLog::firstOrCreate(
            ['action' => 'SHIPMENT_DISPATCH_AUTHORIZED', 'user_id' => $operator->id],
            [
                'entity_type' => 'Shipment',
                'entity_id' => $delayedShipment->id ?? 1,
                'old_values' => ['status' => 'BOOKED'],
                'new_values' => ['status' => 'IN_TRANSIT', 'seeded_baseline' => true],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'WorldCargo360-System/1.0',
                'created_at' => now()->subDays(2),
            ]
        );
        } finally {
            if ($seederLock) {
                try {
                    $seederLock->release();
                } catch (\Throwable $e) {}
            }
        }
    }
}
