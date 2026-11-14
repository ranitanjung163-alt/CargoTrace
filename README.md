# WorldCargo 360 – Global Logistics & Shipment Tracking System
### *Track Beyond Borders.*

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange.svg)](https://mariadb.org)
[![Testing](https://img.shields.io/badge/Tests-60%20Passed%20(100%25)-brightgreen.svg)](tests/Feature)
[![License](https://img.shields.io/badge/License-Academic%20Permissive-green.svg)](LICENSE)

> **Individual Final Project (UAS) – Web Programming 2**  
> **Course Lecturer:** Pak Ikhwan  
> **Platform Name:** WorldCargo 360 (WORLDCARGO 360)  
> **Official Tagline:** Track Beyond Borders.  
> **Scenario Configuration:** Worldwide Route Coverage, 12-Hour Delay Threshold, `WEATHER_DELAY` Primary Disruption Model, `On-time delivery rate` Core KPI.

---

## 1. Project Overview & Business Problem

International logistics faces critical visibility challenges:
- Cargo information is fragmented across disparate carrier systems.
- Consignees and customers lack real-time geographical awareness and accurate ETAs.
- Route delays and customs/weather exceptions are detected late, resulting in costly SLA breaches.
- Operations dispatchers lack prioritized dashboards to proactively mitigate in-transit issues.

**WorldCargo 360** solves these challenges by providing a centralized global visibility and tracking platform. The application models end-to-end shipment lifecycles, delivers interactive worldwide maps, calculates deterministic delay metrics against contractual SLAs, integrates live weather and geocoding services, and exposes a hardened RESTful API with strict role-based access control (RBAC).

---

## 2. Key Features

1. **Light-Theme Enterprise Dashboard:**
   - Designed strictly following the lecturer reference layout.
   - **5 Dynamic KPI Cards:** Total Shipments, In Transit, Delivered, Delayed, and Open Exceptions.
   - **Shipments by Status Chart:** Bar chart powered by Chart.js displaying active lifecycle distribution.
   - **Delay Rate by Carrier Chart:** Donut chart showing delay percentages per carrier with centered average delay metric.
   - **Shipment Map Widget:** Embedded Leaflet.js interactive worldwide map showing active global routes.
   - **Recent Shipments Table:** Searchable, filterable, and exportable live shipment register.

2. **Strict Shipment Lifecycle Engine:**
   - Valid forward transitions only: `CREATED` &rarr; `BOOKED` &rarr; `PICKED_UP` &rarr; `IN_TRANSIT` &rarr; `DELIVERED`.
   - Backend validation blocks illegal skips and prevents reversion once terminal `DELIVERED` status is reached.
   - Database transactions ensure atomicity during order creation and manifest logging.

3. **Historical Waypoint Tracking & Chronology:**
   - Chronological validation: new waypoint timestamps cannot precede historical events.
   - Automatic synchronization of shipment current status and current location coordinates.
   - Distinct classification of normal transit scans versus exception alerts.

4. **Deterministic ETA & Delay Engine (`EtaService`):**
   - Automatically compares estimated arrival ($ETA$) against scheduled arrival deadline ($SLA$).
   - Categorizes delay severity:
     - $0 < \text{delay} \le 6\text{h}$: `LOW`
     - $6\text{h} < \text{delay} \le 24\text{h}$: `MEDIUM` (12h default threshold)
     - $> 24\text{h}$: `HIGH`

5. **Operational Exception Management (`ExceptionService`):**
   - Disruption types: `WEATHER_DELAY`, `CUSTOMS_HOLD`, `PORT_CONGESTION`, `DAMAGED`, `MISROUTED`, `DOCUMENTATION_ISSUE`.
   - Full incident workflow: `OPEN` &rarr; `INVESTIGATING` &rarr; `RESOLVED`.
   - Automatic delay triggering and resolution audit trail.

6. **Worldwide Interactive Map & External APIs:**
   - **Leaflet.js + OpenStreetMap:** True global coverage (-90 to +90 latitude, -180 to +180 longitude) across Asia, Europe, Americas, Africa, and Oceania.
   - **Geocoding API:** OpenStreetMap Nominatim integration with User-Agent compliance, 30-day caching, and local fallback database.
   - **Weather Intelligence API:** Open-Meteo live numerical weather model integration for transit waypoints.

7. **In-App Notifications & Audit Trail:**
   - Customer notification center for order milestones and exception alerts.
   - Immutable audit logging for security compliance and administrator governance.

8. **Hardened RESTful API:**
   - Laravel Sanctum token-based authentication.
   - Server-side data isolation: Customers can never access another customer's shipment data.
   - Includes Postman Collection v2.1.

---

## 3. User Roles & Access Control

| Role | Permissions & Scope | Restricted Actions |
|---|---|---|
| **Administrator** | Full system governance, User CRUD, Role assignment, Carrier & Location management, All Shipments, Audit Logs, Global Reports. | None. |
| **Logistics Operator** | Order dispatch, Tracking waypoint logging, Status transitions, Exception logging & resolution, Operational Dashboard. | Cannot modify user accounts or system security settings. |
| **Customer** | View own shipments only, Track own waypoints & route map, View weather & ETA, View own in-app notifications. | **Strictly blocked** from other customers' data, modifying tracking/statuses, or viewing administrative controls. |

---

## 4. Technology Stack

- **Backend Framework:** Laravel 12.12.2 (Framework 12.71.0)
- **Language:** PHP 8.2.12
- **Database:** MySQL 8.0+ / MariaDB 10.4.32 (XAMPP compatible)
- **API Security:** Laravel Sanctum (Bearer Token Authentication)
- **HTTP Client:** Guzzle 7 via Laravel HTTP Client
- **Frontend:** Blade Templates, Tailwind CSS (Custom Light Theme), Vanilla JavaScript (Fetch API)
- **Mapping & Charts:** Leaflet.js v1.9.4 + OpenStreetMap tiles, Chart.js v4.4.1
- **Testing:** PHPUnit 11.5 with In-Memory SQLite test database

---

## 5. Demo Accounts

For grading, demonstration, and interview evaluation:

| Role | Email | Password | Scope |
|---|---|---|---|
| **Administrator** | `admin@cargotrace.com` | `password123` | Full System Access |
| **Logistics Operator** | `operator@cargotrace.com` | `password123` | Operational Dispatch |
| **Customer 1** | `customer@cargotrace.com` | `password123` | Global Industries Ltd (Delayed SHP-2024-001284) |
| **Customer 2** | `tech@cargotrace.com` | `password123` | Tech Solutions Corp (Trans-Pacific SHP-2024-001223) |
| **Customer 3** | `retail@cargotrace.com` | `password123` | Retail Hub International (SHP-2024-001222) |

*(A 1-click quick login selector is available directly on the `/login` page for convenience during evaluation).*

---

## 6. Installation & Local Setup

### Step 1: Clone the Repository
```bash
# Clone the repository [Pending Repository Setup / Academic Submission]
git clone <repository-url>
cd WorldCargo_360
```

### Step 2: Install Composer Dependencies
```bash
composer install
```

### Step 3: Configure Environment
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
Ensure database credentials point to your local MySQL/MariaDB server:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cargo_trace
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Migrations & Seed Realistic Demo Data
```bash
php artisan migrate
php artisan db:seed
```

### Step 6: Start the Development Server
```bash
php artisan serve
```
Open your browser and navigate to `http://127.0.0.1:8000`.

---

## 7. Automated Test Suite

The test suite covers all 18 mandatory evaluation criteria required by the course brief:

```bash
php artisan test
```

### Test Evidence (60/60 Passing, 295 Assertions, 0 Failures)
```text
PASS  Tests\Unit\ExampleTest (1 test)
PASS  Tests\Feature\ExampleTest (1 test)
PASS  Tests\Feature\CargoTraceCompleteTestSuiteTest (30 tests)
  - Auth, Role-Based Access Control, Customer Data Isolation
  - Shipment CRUD, Validations, Coordinates, and Filter Matrix
  - Tracking Chronology, ETA SLA Thresholds, and Delay Severity
  - Exception Workflow (OPEN -> INVESTIGATING -> RESOLVED)
  - Weather API Resilience, Payloads, Stale Caching & Fallback
  - REST API Sanctum Authorization & Data Protection
PASS  Tests\Feature\DatabaseSeederRobustnessAndChronologyTest (28 tests)
  - Seeder Idempotency, Concurrency Mutex Lock & Fail-Closed Guards
  - Chronological Tracking Milestones (Created -> Booked -> Picked Up -> In Transit -> Delivered)
  - Operational Mutation Preservation & Anti-Duplication Controls

Tests:    60 passed (295 assertions)
Duration: ~7.65s (Executed on isolated in-memory SQLite database)
```

---

## 8. REST API & Postman Collection

A complete Postman Collection v2.1 is located at [`docs/cargotrace_postman_collection.json`](docs/cargotrace_postman_collection.json).

### Key Endpoints:
- `POST /api/register` – Register customer account
- `POST /api/login` – Authenticate and receive Bearer Token
- `GET /api/shipments` – Paginated and filtered shipments list
- `POST /api/shipments` – Create shipment order
- `GET /api/shipments/{id}` – Shipment details with strict access control
- `GET /api/shipments/{id}/tracking` – Chronological tracking waypoints
- `POST /api/shipments/{id}/tracking` – Append tracking waypoint event
- `POST /api/shipments/{id}/exceptions` – Log operational exception
- `PATCH /api/exceptions/{id}` – Resolve operational exception
- `GET /api/carriers` & `POST /api/carriers` – Carrier management
- `GET /api/locations` & `POST /api/locations` – Location management

---

## 9. Technical Documentation References

- **Entity-Relationship Diagram & Data Dictionary:** [`docs/ERD.md`](docs/ERD.md)
- **Architecture Diagram & Lifecycle Specification:** [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)
- **Full REST API Documentation:** [`docs/API_DOCUMENTATION.md`](docs/API_DOCUMENTATION.md)
- **Production Deployment Guide:** [`docs/DEPLOYMENT_GUIDE.md`](docs/DEPLOYMENT_GUIDE.md)

---

## 10. Known Limitations & Future Work

1. **Hardware IoT GPS Integration:** Real-time physical GPS tracking from onboard maritime transponders is currently simulated via chronological tracking waypoints. Future work could integrate AIS (Automatic Identification System) vessel feeds.
2. **Predictive AI/ML ETAs:** The current ETA engine uses a deterministic SLA comparison. Future milestones can incorporate machine learning models trained on seasonal maritime transit data.
3. **Automated Webhook Subscriptions:** External ERP webhook notifications can be added for third-party B2B consumers.

---

## 11. Third-Party Attributions

- [Laravel Framework](https://laravel.com) (Taylor Otwell, MIT License)
- [Leaflet.js](https://leafletjs.com) (Vladimir Agafonkin, BSD-2-Clause License)
- [OpenStreetMap](https://www.openstreetmap.org) (OpenStreetMap Foundation, ODbL License)
- [Chart.js](https://www.chartjs.org) (Chart.js contributors, MIT License)
- [Open-Meteo](https://open-meteo.com) (Free Open-Source Weather API)
- [Tailwind CSS](https://tailwindcss.com) (Tailwind Labs Inc., MIT License)
