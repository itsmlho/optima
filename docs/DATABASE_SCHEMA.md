# OPTIMA System - Current Database Schema Reference
*Last Updated: 2026-01-26*

This document reflects the **active** database structure used by the CodeIgniter 4 application. 
> **Note:** This schema supersedes any previous planning documents. All field names are verified against `App\Models`.

---

## 1. Asset & Inventory (`inventory_unit`)
The central table for all fleet units (Assets & Non-Assets).

| Column | Type | Description |
|--------|------|-------------|
| `id_inventory_unit` | INT (PK) | Auto-increment Primary Key |
| `no_unit` | VARCHAR | Asset Number (e.g., F-1234) |
| `serial_number` | VARCHAR | Manufacturer Serial Number |
| `status_unit_id` | INT | FK to `status_unit`. defines Rent, Ready, Breakdown status. |
| `kontrak_id` | INT | FK to `kontrak`. Current active contract. |
| `spk_id` | INT | FK to `spk`. Current active job. |
| `model_unit_id` | INT | FK to `model_unit`. |
| `departemen_id` | INT | FK to `departemen`. |
| `created_at` | DATETIME | Record creation timestamp. |

**Related Tables:**
*   `status_unit`: (`id_status`, `status_unit`) -> Values: "Ready", "Rent", "Breakdown", "Scrap".
*   `model_unit`: (`id`, `model`, `brand`).

---

## 2. Sales & Contracts (`kontrak`)
Manages long-term rentals and customer agreements.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Auto-increment Primary Key |
| `no_kontrak` | VARCHAR | Unique Contract Number |
| `customer_location_id` | INT | FK to `customer_locations` (which links to `customers`). |
| `status` | ENUM | 'Aktif', 'Berakhir', 'Pending', 'Dibatalkan' |
| `tanggal_mulai` | DATE | Contract Start Date |
| `tanggal_berakhir` | DATE | Contract End Date |
| `total_units` | INT | Count of units in this contract |
| `dibuat_pada` | DATETIME | **Creation Time** (Note: Indonesian column name) |

**Related Tables:**
*   `customers`: (`id`, `customer_code`, `customer_name`).
*   `customer_locations`: (`id`, `customer_id`, `location_name`).

---

## 3. Order Processing (`spk`)
Surat Perintah Kerja - The internal work order from Marketing to Ops.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Auto-increment Primary Key |
| `nomor_spk` | VARCHAR | Unique SPK Number (SPK/YYYYMM/XXX) |
| `status` | VARCHAR | Status flow: 'DIAJUKAN' -> 'DISETUJUI' -> 'SELESAI' |
| `kontrak_id` | INT | Link to Contract (if Rental) |
| `dibuat_pada` | DATETIME | Creation timestamp |

---

## 4. Logistics (`delivery_instructions`)
Manages unit delivery and logistics workflow.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Auto-increment Primary Key |
| `nomor_di` | VARCHAR | DO/DI Number |
| `spk_id` | INT | Source SPK |
| `status_di` | VARCHAR | Status: 'PENDING', 'ON_DELIVERY', 'SELESAI', 'COMPLETED' |
| `tanggal_kirim` | DATE | Scheduled delivery date |
| `nama_supir` | VARCHAR | Driver Name |
| `no_polisi_kendaraan`| VARCHAR | Vehicle Plate Number |

---

## 5. Maintenance (`work_orders`)
Service, repair, and maintenance tracking.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Primary Key |
| `work_order_number` | VARCHAR | Unique WO Number |
| `unit_id` | INT | FK to `inventory_unit` |
| `order_type` | ENUM | 'COMPLAINT', 'PMPS', 'FABRIKASI' |
| `status_id` | INT | FK to Work Order Status |
| `complaint_description`| TEXT | Issue reported |
| `created_at` | DATETIME | Log time |

---

## 6. Spareparts Inventory (`sparepart` & `inventory_spareparts`)

**`sparepart` (Master Data)**
| Column | Type | Description |
|--------|------|-------------|
| `id_sparepart` | INT (PK) | Primary Key |
| `kode` | VARCHAR | Part Number |
| `desc_sparepart` | VARCHAR | Part Name/Description |

**`inventory_spareparts` (Stock Data)**
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Primary Key |
| `sparepart_id` | INT | FK to `sparepart.id_sparepart` |
| `stok` | INT | Current Quantity |
| `lokasi_rak` | VARCHAR | Warehouse Bin Location |
| `updated_at` | DATETIME | Last Stock Update |

---

## Important Usage Notes
1.  **Column Names:** Pay attention that some tables use English (`created_at`) while others use Indonesian (`dibuat_pada`). Always check the Model `$allowedFields` if unsure.
2.  **Status Handling:**
    *   `inventory_unit` uses a **Foreign Key** (`status_unit_id`) for status.
    *   `kontrak` uses an **ENUM** String column (`status`).
    *   `delivery_instructions` uses a String column (`status_di`).
