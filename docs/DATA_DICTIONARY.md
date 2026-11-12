# WorldCargo 360 – Database Data Dictionary
## Global Logistics & Shipment Tracking System (MySQL / MariaDB)

This document provides a comprehensive technical reference for the nine core database tables, column specifications, data types, constraints, default values, and relational integrity rules implemented in **WorldCargo 360**.

---

### Table 1: `users`
Stores user identities, hashed credentials, and role tiers for Role-Based Access Control (RBAC).

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique user identifier. |
| `name` | `VARCHAR(255)` | `NOT NULL` | None | Full legal or operational name of user. |
| `email` | `VARCHAR(255)` | `NOT NULL`, `UNIQUE`, `INDEX` | None | Corporate email address used for login. |
| `password` | `VARCHAR(255)` | `NOT NULL` | None | Bcrypt hashed cryptographic password. |
| `role` | `ENUM` | `NOT NULL` (`admin`, `operator`, `customer`) | `'customer'` | Privilege authorization tier. |
| `phone` | `VARCHAR(50)` | `NULLABLE` | `NULL` | International contact telephone number. |
| `company_name` | `VARCHAR(255)` | `NULLABLE` | `NULL` | Organization, shipper, or consignee name. |
| `avatar_url` | `VARCHAR(255)` | `NULLABLE` | `NULL` | URI path to user avatar icon. |
| `remember_token` | `VARCHAR(100)` | `NULLABLE` | `NULL` | Laravel persistent session cookie token. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 2: `carriers`
Stores contracted commercial freight carriers and transport operators.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique carrier identifier. |
| `code` | `VARCHAR(20)` | `NOT NULL`, `UNIQUE`, `INDEX` | None | International carrier identifier (e.g., `FST`, `CGP`, `DHL`). |
| `name` | `VARCHAR(100)` | `NOT NULL` | None | Registered legal name of logistics provider. |
| `contact_email` | `VARCHAR(255)` | `NULLABLE` | `NULL` | Central operations dispatch email. |
| `contact_phone` | `VARCHAR(50)` | `NULLABLE` | `NULL` | 24/7 emergency dispatch hotline. |
| `service_type` | `VARCHAR(50)` | `NOT NULL` | `'Multimodal'` | Modal classification (Air, Ocean, Road, Rail, Multimodal). |
| `is_active` | `BOOLEAN` | `NOT NULL` | `TRUE` | Carrier operational routing status. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 3: `locations`
Worldwide transport nodes, maritime seaports, air freight terminals, and customs clearance facilities.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique location identifier. |
| `code` | `VARCHAR(30)` | `NOT NULL`, `UNIQUE`, `INDEX` | None | Standard port/hub code (e.g., `US-LAX`, `NL-RTM`, `ID-JKT`). |
| `name` | `VARCHAR(150)` | `NOT NULL` | None | Full facility or port authority name. |
| `city` | `VARCHAR(100)` | `NOT NULL` | None | City name where hub resides. |
| `state_province`| `VARCHAR(100)` | `NULLABLE` | `NULL` | State, prefecture, or province. |
| `country` | `VARCHAR(100)` | `NOT NULL` | None | Sovereign nation name. |
| `country_code` | `VARCHAR(10)` | `NOT NULL` | None | ISO 3166-1 alpha-2 country code. |
| `latitude` | `DECIMAL(10, 7)` | `NOT NULL` | None | Geographical latitude ($-90.0000000$ to $+90.0000000$). |
| `longitude` | `DECIMAL(10, 7)` | `NOT NULL` | None | Geographical longitude ($-180.0000000$ to $+180.0000000$). |
| `type` | `ENUM` | `NOT NULL` | `'PORT'` | Node type (`PORT`, `AIRPORT`, `WAREHOUSE`, `DISTRIBUTION_CENTER`, `CUSTOMS`). |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 4: `shipments`
Master shipment manifest and consignment lifecycle ledger.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique internal shipment record identifier. |
| `shipment_number`| `VARCHAR(50)` | `NOT NULL`, `UNIQUE`, `INDEX` | None | Public tracking reference (`SHP-YYYY-XXXXXX`). |
| `customer_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`users.id`) | None | Customer user owning the cargo consignment. |
| `carrier_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`carriers.id`) | None | Contracted carrier executing the transport. |
| `origin_location_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`locations.id`) | None | Departure origin hub. |
| `destination_location_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`locations.id`) | None | Final destination consignee port/hub. |
| `current_location_id` | `BIGINT UNSIGNED` | `NULLABLE`, `FK` (`locations.id`) | `NULL` | Latest scanned tracking checkpoint. |
| `cargo_type` | `VARCHAR(100)` | `NOT NULL` | None | Commodity classification (e.g. Electronics, Perishables). |
| `weight` | `DECIMAL(10, 2)` | `NOT NULL` | None | Consignment gross weight in kilograms. |
| `volume` | `DECIMAL(10, 2)` | `NOT NULL` | None | Consignment cubic volume in cubic meters ($m^3$). |
| `expected_departure` | `DATETIME` | `NOT NULL` | None | Contracted departure timestamp. |
| `actual_departure` | `DATETIME` | `NULLABLE` | `NULL` | Verified origin dispatch timestamp. |
| `expected_arrival` | `DATETIME` | `NOT NULL` | None | Contractual SLA arrival deadline. |
| `estimated_arrival` | `DATETIME` | `NOT NULL` | None | Dynamic estimated time of arrival (ETA). |
| `actual_delivered_at`| `DATETIME` | `NULLABLE` | `NULL` | Terminal receipt timestamp upon final delivery. |
| `current_status`| `ENUM` | `NOT NULL` | `'CREATED'` | Lifecycle state: `CREATED`, `BOOKED`, `PICKED_UP`, `IN_TRANSIT`, `DELIVERED`. |
| `is_delayed` | `BOOLEAN` | `NOT NULL` | `FALSE` | Set to `TRUE` if delay exceeds the SLA threshold (12h). |
| `delay_duration_minutes` | `INT` | `NOT NULL` | `0` | Quantified minutes elapsed past `expected_arrival`. |
| `delay_severity`| `ENUM` | `NOT NULL` | `'NONE'` | Impact tier: `NONE`, `LOW`, `MEDIUM`, `HIGH`. |
| `delay_reason` | `TEXT` | `NULLABLE` | `NULL` | Structured description of disruption cause. |
| `description` | `TEXT` | `NULLABLE` | `NULL` | Operational notes and consignment handling rules. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 5: `shipment_items`
Itemized cargo packaging breakdown for multi-commodity consignments.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique manifest item identifier. |
| `shipment_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`shipments.id`) | None | Associated parent shipment record (`CASCADE DELETE`). |
| `item_name` | `VARCHAR(150)` | `NOT NULL` | None | Description of commodity/goods. |
| `quantity` | `INT` | `NOT NULL` | None | Number of physical packaging units. |
| `unit` | `VARCHAR(30)` | `NOT NULL` | `'units'` | Packaging unit (e.g. pallets, boxes, drums, containers). |
| `weight` | `DECIMAL(10, 2)` | `NULLABLE` | `NULL` | Individual or sub-total gross weight in kg. |
| `hs_code` | `VARCHAR(30)` | `NULLABLE` | `NULL` | International Harmonized System customs tariff code. |
| `description` | `TEXT` | `NULLABLE` | `NULL` | Hazardous material flags or special handling specs. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 6: `tracking_events`
Chronological, append-only historical transit scans and waypoint logs.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique tracking milestone record identifier. |
| `shipment_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`shipments.id`) | None | Associated parent shipment record (`CASCADE DELETE`). |
| `event_type` | `VARCHAR(50)` | `NOT NULL` | None | Milestone classification (e.g. `DEPARTED`, `IN_TRANSIT`, `CUSTOMS_INSPECTED`). |
| `event_time` | `DATETIME` | `NOT NULL` | None | Clock timestamp of physical scan occurrence. |
| `location_id` | `BIGINT UNSIGNED` | `NULLABLE`, `FK` (`locations.id`) | `NULL` | Matching location facility if mapped (`SET NULL`). |
| `location_name`| `VARCHAR(150)` | `NOT NULL` | None | Freeform or geocoded checkpoint descriptor. |
| `latitude` | `DECIMAL(10, 7)` | `NULLABLE` | `NULL` | GPS coordinate latitude of scan. |
| `longitude` | `DECIMAL(10, 7)` | `NULLABLE` | `NULL` | GPS coordinate longitude of scan. |
| `status_after_event` | `VARCHAR(50)` | `NOT NULL` | None | Resulting lifecycle state after milestone registration. |
| `description` | `TEXT` | `NOT NULL` | None | Dispatcher narration of physical cargo condition. |
| `is_exception` | `BOOLEAN` | `NOT NULL` | `FALSE` | Set to `TRUE` if event represents an operational anomaly. |
| `created_by` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`users.id`) | None | Operator or system actor logging the milestone. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 7: `shipment_exceptions`
Disruption records for incidents affecting operational timelines and integrity.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique exception record identifier. |
| `shipment_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`shipments.id`) | None | Associated affected shipment (`CASCADE DELETE`). |
| `exception_type` | `ENUM` | `NOT NULL` | None | Incident type (`CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`, `DAMAGED`, `MISROUTED`, `DOCUMENTATION_ISSUE`). |
| `severity` | `ENUM` | `NOT NULL` | `'MEDIUM'` | Disruption scale (`LOW`, `MEDIUM`, `HIGH`). |
| `status` | `ENUM` | `NOT NULL` | `'OPEN'` | Investigation lifecycle (`OPEN`, `INVESTIGATING`, `RESOLVED`). |
| `description` | `TEXT` | `NOT NULL` | None | Root cause incident analysis report. |
| `detected_at` | `DATETIME` | `NOT NULL` | None | Incident detection timestamp. |
| `resolved_at` | `DATETIME` | `NULLABLE` | `NULL` | Formal operational clearance timestamp. |
| `resolution_notes`| `TEXT` | `NULLABLE` | `NULL` | Audit log of actions taken to mitigate incident. |
| `created_by` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`users.id`) | None | User or operator who flagged the incident. |
| `resolved_by` | `BIGINT UNSIGNED` | `NULLABLE`, `FK` (`users.id`) | `NULL` | Administrator or supervisor signing off resolution. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 8: `notifications`
In-app alerts delivered to customers and operators regarding milestones and delay alerts.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique notification record identifier. |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK` (`users.id`) | None | Recipient user account (`CASCADE DELETE`). |
| `shipment_id` | `BIGINT UNSIGNED` | `NULLABLE`, `FK` (`shipments.id`) | `NULL` | Associated shipment (`CASCADE DELETE`). |
| `title` | `VARCHAR(150)` | `NOT NULL` | None | Header title of the alert banner. |
| `message` | `TEXT` | `NOT NULL` | None | Body content of the notification. |
| `type` | `VARCHAR(50)` | `NOT NULL` | None | System classification category. |
| `is_read` | `BOOLEAN` | `NOT NULL` | `FALSE` | Read acknowledgement boolean flag. |
| `read_at` | `DATETIME` | `NULLABLE` | `NULL` | Timestamp when user opened the alert. |
| `created_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | `NULL` | Record last modification timestamp. |

---

### Table 9: `audit_logs`
Immutable compliance and security trail recording critical data mutations.

| Column Name | Data Type | Constraints & Flags | Default Value | Description |
|---|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | None | Unique audit trail record identifier. |
| `user_id` | `BIGINT UNSIGNED` | `NULLABLE`, `FK` (`users.id`) | `NULL` | Actor executing the mutation (`SET NULL`). |
| `action` | `VARCHAR(100)` | `NOT NULL` | None | Event name (`USER_LOGIN`, `STATUS_TRANSITION`, `EXCEPTION_LOGGED`). |
| `entity_type` | `VARCHAR(100)` | `NOT NULL` | None | Affected Eloquent model class name. |
| `entity_id` | `BIGINT UNSIGNED` | `NULLABLE` | `NULL` | Primary key of modified entity. |
| `old_values` | `JSON` | `NULLABLE` | `NULL` | Pre-mutation state snapshot. |
| `new_values` | `JSON` | `NULLABLE` | `NULL` | Post-mutation state snapshot. |
| `ip_address` | `VARCHAR(45)` | `NULLABLE` | `NULL` | Client IPv4 or IPv6 network address. |
| `user_agent` | `TEXT` | `NULLABLE` | `NULL` | Client browser / device user-agent string. |
| `created_at` | `TIMESTAMP` | `NOT NULL` | `CURRENT_TIMESTAMP` | System log timestamp. |

---

### 10. Referential Integrity & Foreign Key Policies

1. **`shipments` $\to$ `users` (`customer_id`):** `onDelete('restrict')` prevents customer account deletion while shipments remain active.
2. **`shipments` $\to$ `carriers` (`carrier_id`):** `onDelete('restrict')` prevents carrier removal while active freight routes rely on the entity.
3. **`shipments` $\to$ `locations` (`origin_location_id`, `destination_location_id`):** `onDelete('restrict')` protects global infrastructure hubs from accidental deletion.
4. **`shipment_items` $\to$ `shipments` (`shipment_id`):** `onDelete('cascade')` ensures manifest line items are cleaned up automatically if parent shipment is removed.
5. **`tracking_events` $\to$ `shipments` (`shipment_id`):** `onDelete('cascade')` cleans up chronological scans upon shipment cancellation.
6. **`shipment_exceptions` $\to$ `shipments` (`shipment_id`):** `onDelete('cascade')` links exception lifecycles to parent orders.
7. **`audit_logs` $\to$ `users` (`user_id`):** `onDelete('set null')` permanently preserves forensic audit records even if an operator account is purged.

