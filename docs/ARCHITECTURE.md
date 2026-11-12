# WorldCargo 360 – Architecture Specification & Guideline
### *Track Beyond Borders.*

## 1. High-Level Architecture Flow

Following the strict architecture guidelines of the course:
`Browser / Client → Routes + Middleware → Controllers → Service Layer → Models / Eloquent → MySQL / External Services`

```mermaid
flowchart TD
    subgraph ClientLayer["Client & Consumer Layer"]
        A1["Desktop & Mobile Browser (Blade Templates, Tailwind, Leaflet.js, Chart.js)"]
        A2["REST API Consumer (Postman, Mobile App, Third-party System)"]
    end

    subgraph RoutingAndSecurity["Routing & Security Boundary"]
        B1["Laravel Route Dispatcher (routes/web.php & routes/api.php)"]
        B2["Middleware Pipeline: RoleMiddleware, auth:sanctum, CSRF, Session"]
        B3["ShipmentPolicy & Form Requests (Authorization & Validation)"]
    end

    subgraph ControllerLayer["HTTP Controller Layer"]
        C1["DashboardController (KPI Cards, Analytics, Role Scoping)"]
        C2["ShipmentController & Api/ShipmentApiController"]
        C3["TrackingController (Waypoints, Search, Leaflet Maps)"]
        C4["ExceptionController & Api/ExceptionApiController"]
        C5["CarrierController, LocationController, ReportController"]
    end

    subgraph ServiceLayer["Business Service Layer (Clean Architecture)"]
        D1["ShipmentService: Lifecycle transitions, number generation, transactions"]
        D2["TrackingService: Chronology checks, waypoint logging, status sync"]
        D3["EtaService: Deterministic delay calculation, SLA vs ETA, severity levels"]
        D4["ExceptionService: Incident logging, auto-delay triggering, resolution SLA"]
        D5["GeocodingService: OpenStreetMap Nominatim, User-Agent, 30d Cache, Fallback DB"]
        D6["WeatherService: Open-Meteo Global Forecast Models, 1h Cache, Fallback"]
    end

    subgraph PersistenceLayer["Data & External Layer"]
        E1["Eloquent ORM Models (Users, Shipments, Locations, Events, AuditLogs)"]
        E2[("MySQL 8 / MariaDB (cargo_trace DB)")]
        E3["OpenStreetMap Tile Servers & Nominatim Geocoding API"]
        E4["Open-Meteo Global Numerical Weather Prediction API"]
    end

    ClientLayer --> RoutingAndSecurity
    RoutingAndSecurity --> ControllerLayer
    ControllerLayer --> ServiceLayer
    ServiceLayer --> PersistenceLayer
    E1 <--> E2
    D5 <--> E3
    D6 <--> E4
```

---

## 2. Service Layer Responsibilities

### `ShipmentService`
- Generates unique tracking numbers matching format `SHP-YYYY-XXXXXX`.
- Manages DB Transactions when creating shipments with multi-item manifests.
- Enforces valid lifecycle status flow:
  `CREATED` &rarr; `BOOKED` &rarr; `PICKED_UP` &rarr; `IN_TRANSIT` &rarr; `DELIVERED`.
- Enforces terminal status lock: once marked `DELIVERED`, status cannot jump backwards.
- Triggers ETA delay recalculation and audit logging.

### `TrackingService`
- Ensures chronological consistency: new event timestamp `event_time` must not be earlier than existing historical waypoints.
- Automatically updates shipment's `current_location_id` and `current_status`.
- Creates in-app notifications and records audit logs.

### `EtaService`
- Compares `estimated_arrival` against `expected_arrival` (scheduled delivery deadline).
- Evaluates delay duration in minutes and hours.
- Maps delay duration to severity:
  - $\le 0$ hours: `NONE`
  - $0 < \text{delay} \le 6$ hours: `LOW`
  - $6 < \text{delay} \le 24$ hours: `MEDIUM` (12h recommended scenario threshold)
  - $> 24$ hours: `HIGH`
- Configurable via `config/cargotrace.php`.

### `ExceptionService`
- Manages incident lifecycle: `OPEN` &rarr; `INVESTIGATING` &rarr; `RESOLVED`.
- Logs matching tracking events flagged as `is_exception = true`.
- Automatically raises shipment delay status and records resolution audit trail with timestamps.

### `GeocodingService`
- Connects to OpenStreetMap Nominatim via Laravel HTTP Client with proper user agent header.
- Implements 30-day response caching (`Cache::remember`).
- Features robust fallback database of major global ports and airports (Rotterdam, Los Angeles, Tokyo, Singapore, Jakarta, Santos, Durban, Frankfurt, etc.) for offline and rate-limited operation.

### `WeatherService`
- Connects to Open-Meteo live API for worldwide weather observations (temperature, wind, precipitation, condition).
- Implements 1-hour caching.
- Handles timeouts and failures without interrupting application flow.

