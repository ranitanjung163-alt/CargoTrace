# WorldCargo 360 – REST API Documentation
### *Track Beyond Borders.*

**WorldCargo 360** provides a complete RESTful API built on Laravel 12 and authenticated with **Laravel Sanctum**.

All requests and responses use standard JSON formatting:
- Content-Type: `application/json`
- Accept: `application/json`
- Base URL: `http://127.0.0.1:8000/api`

---

## 1. Authentication Endpoints

### 1.1 Register New Customer
- **Endpoint:** `POST /api/register`
- **Access:** Public
- **Request Body:**
  ```json
  {
    "name": "Pacific Freight Corp",
    "email": "pacific@cargotrace.com",
    "password": "password123",
    "password_confirmation": "password123",
    "company_name": "Pacific Freight Corp",
    "phone": "+1-800-555-0199"
  }
  ```
- **Response (201 Created):**
  ```json
  {
    "success": true,
    "message": "User registered successfully.",
    "data": {
      "user": {
        "id": 8,
        "name": "Pacific Freight Corp",
        "email": "pacific@cargotrace.com",
        "role": "customer"
      },
      "token": "1|qXy...API_TOKEN",
      "token_type": "Bearer"
    }
  }
  ```

### 1.2 Login
- **Endpoint:** `POST /api/login`
- **Access:** Public
- **Request Body:**
  ```json
  {
    "email": "admin@cargotrace.com",
    "password": "password123"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "success": true,
    "message": "Login successful.",
    "data": {
      "user": {
        "id": 1,
        "name": "System Administrator",
        "email": "admin@cargotrace.com",
        "role": "admin"
      },
      "token": "2|abcdef...TOKEN",
      "token_type": "Bearer"
    }
  }
  ```

### 1.3 Logout
- **Endpoint:** `POST /api/logout`
- **Access:** Bearer Token
- **Response (200 OK):**
  ```json
  {
    "success": true,
    "message": "Successfully logged out and token invalidated."
  }
  ```

### 1.4 Get Current User
- **Endpoint:** `GET /api/user`
- **Access:** Bearer Token

---

## 2. Shipments Endpoints

### 2.1 List Shipments
- **Endpoint:** `GET /api/shipments?page=1&per_page=10&status=IN_TRANSIT&search=electronics`
- **Access:** Bearer Token (Customers only receive their own shipments; Admins/Operators receive all)
- **Response (200 OK):**
  ```json
  {
    "success": true,
    "message": "Shipments retrieved successfully.",
    "data": [
      {
        "id": 1,
        "shipment_number": "SHP-2024-001284",
        "customer": { "id": 3, "name": "Global Industries Ltd", "email": "customer@cargotrace.com" },
        "carrier": { "id": 1, "name": "FastShip Express", "code": "FST" },
        "origin": { "id": 1, "name": "Los Angeles Port Hub", "city": "Los Angeles", "country": "United States" },
        "destination": { "id": 2, "name": "Chicago Intermodal Terminal", "city": "Chicago", "country": "United States" },
        "cargo_type": "Electronics & Computing Components",
        "current_status": "IN_TRANSIT",
        "is_delayed": true,
        "delay_duration_minutes": 1080,
        "delay_severity": "MEDIUM",
        "delay_reason": "Severe weather blizzard on Midwest corridor (WEATHER_DELAY)"
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 10
    }
  }
  ```

### 2.2 Create Shipment
- **Endpoint:** `POST /api/shipments`
- **Access:** Operator / Admin
- **Request Body:**
  ```json
  {
    "customer_id": 3,
    "carrier_id": 1,
    "origin_location_id": 1,
    "destination_location_id": 2,
    "cargo_type": "Precision Sensors",
    "weight": 2400.0,
    "volume": 12.5,
    "expected_departure": "2026-11-10 08:00:00",
    "expected_arrival": "2026-11-14 18:00:00",
    "estimated_arrival": "2026-11-14 18:00:00",
    "description": "Fragile items packed on wooden pallets.",
    "items": [
      {
        "item_name": "Sensor Modules",
        "quantity": 20,
        "unit": "boxes",
        "weight": 2400.0
      }
    ]
  }
  ```
- **Response (201 Created)**

### 2.3 Get Shipment Detail
- **Endpoint:** `GET /api/shipments/{id}`
- **Access:** Bearer Token (Data isolation check: 403 if accessed by another customer)

### 2.4 Update Shipment
- **Endpoint:** `PUT/PATCH /api/shipments/{id}`
- **Access:** Operator / Admin

### 2.5 Delete Shipment
- **Endpoint:** `DELETE /api/shipments/{id}`
- **Access:** Admin only

---

## 3. Tracking & Waypoints Endpoints

### 3.1 Get Tracking History
- **Endpoint:** `GET /api/shipments/{id}/tracking`
- **Access:** Bearer Token
- **Response (200 OK):**
  ```json
  {
    "success": true,
    "data": {
      "shipment_number": "SHP-2024-001284",
      "current_status": "IN_TRANSIT",
      "events": [
        {
          "id": 1,
          "event_type": "CREATED",
          "event_time": "2026-11-04 13:48:57",
          "location_name": "Los Angeles Port Hub",
          "latitude": 34.052235,
          "longitude": -118.243683,
          "description": "Shipment booking generated and processed in WorldCargo 360."
        }
      ]
    }
  }
  ```

### 3.2 Add Tracking Waypoint Event
- **Endpoint:** `POST /api/shipments/{id}/tracking`
- **Access:** Operator / Admin
- **Request Body:**
  ```json
  {
    "event_type": "IN_TRANSIT",
    "event_time": "2026-11-08 14:00:00",
    "location_name": "Denver Intermodal Station",
    "latitude": 39.739236,
    "longitude": -104.990251,
    "status_after_event": "IN_TRANSIT",
    "description": "Transshipment check completed successfully."
  }
  ```
- **Response (201 Created)**

---

## 4. Exceptions Endpoints

### 4.1 Get Shipment Exceptions
- **Endpoint:** `GET /api/shipments/{id}/exceptions`
- **Access:** Bearer Token

### 4.2 Report Exception
- **Endpoint:** `POST /api/shipments/{id}/exceptions`
- **Access:** Operator / Admin
- **Request Body:**
  ```json
  {
    "exception_type": "WEATHER_DELAY",
    "severity": "MEDIUM",
    "description": "Blizzard warning causing temporary road transport restriction."
  }
  ```
- **Response (201 Created)**

### 4.3 Resolve Exception
- **Endpoint:** `PATCH /api/exceptions/{id}`
- **Access:** Operator / Admin
- **Request Body:**
  ```json
  {
    "status": "RESOLVED",
    "resolution_notes": "Corridor reopened, vehicle resumed transit route."
  }
  ```
- **Response (200 OK)**

---

## 5. Carriers & Locations Endpoints

### 5.1 List Carriers: `GET /api/carriers`
### 5.2 Create Carrier: `POST /api/carriers` (Admin only)
### 5.3 List Locations: `GET /api/locations`
### 5.4 Register Location: `POST /api/locations` (Admin only)

