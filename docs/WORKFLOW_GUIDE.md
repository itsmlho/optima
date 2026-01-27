# OPTIMA Application Workflow Guide
*Guide to internal processes for OPTIMA ERP.*

## 1. Rental Operations Workflow

### A. Customer & Contract Setup
1.  **New Customer**: 
    *   Navigate to `Marketing > Customers`.
    *   Create `Customer` profile (PT Name, Code).
    *   Add `Customer Location` (Site address where units will be deployed).
2.  **Create Contract**:
    *   Navigate to `Marketing > Contracts`.
    *   Input `Contract No`, `PO Marketing No`, `Dates`.
    *   **Crucial**: Set Status to `Aktif` for it to appear in the Dashboard.

### B. Unit Preparation (SPK)
1.  **Create SPK**:
    *   Marketing creates **SPK** (Surat Perintah Kerja) referenced to a Contract.
    *   Select `Unit Types` and `Specs` required.
2.  **SPK Approval**:
    *   Operational Manager approves SPK.
    *   SPK status moves from `DIAJUKAN` -> `DISETUJUI`.

### C. Logistics (Delivery)
1.  **Generate DI**:
    *   Logistics team sees approved SPK.
    *   Create **Delivery Instruction (DI)**.
    *   Assign specific `inventory_unit` (Assets) to the DI.
2.  **Dispatch**:
    *   Input Driver & License Plate.
    *   Set status to `ON_DELIVERY` -> Unit status updates to `In Transit` (workflow dependent).
3.  **Completion**:
    *   When unit arrives, set DI status to `SELESAI` or `COMPLETED`.
    *   Unit Status automatically becomes `Rent` / `Sewaan` based on trigger/logic.

---

## 2. Maintenance Workflow (Work Order)

### A. Issue Reporting
1.  **Complaint**: 
    *   Admin/Mechanic inputs a `Complaint` WO.
    *   Selects `Unit` (e.g., F-1234).
    *   Describes issue ("Leaking oil").
    *   Initial Status: `OPEN`.

### B. Execution
1.  **Assignment**:
    *   Foreman assigns Mechanic to the WO.
2.  **Sparepart Usage**:
    *   Mechanic requests parts.
    *   Warehouse approves -> Stock deducts from `inventory_spareparts`.
    *   Parts recorded in `work_order_spareparts`.

### C. Finalization
1.  **Completion**:
    *   Mechanic finishes job.
    *   Updates `HM` (Hour Meter) of the unit.
    *   Status set to `CLOSED` / `FINISH`.
    *   Unit Status updates back to `Ready` or `Rent`.

---

## 3. Dashboard Logic (Command Center)

The Executive Dashboard aggregates data from the above flows:

*   **Fleet Utilization**:
    *   Calculates % of Units where `status_unit` is "Rent" vs Total Units.
    *   Source: `inventory_unit` joined with `status_unit`.
*   **Active Contracts**:
    *   Count of Contracts with `status = 'Aktif'`.
*   **Pending Logistics**:
    *   Count of DIs where `status_di` is NOT 'SELESAI'/'Completed'.
*   **Low Stock Alerts**:
    *   Real-time check of `inventory_spareparts` where `stok <= 5` (or min_stock).
