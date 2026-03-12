# WorkOrderController Fix - Inventory Attachment Table Migration

## Problem
File `app/Controllers/WorkOrderController.php` masih menggunakan table `inventory_attachment` (old schema) di 8 lokasi berbeda (lines 2656-2860).

## Root Cause
Saat SPK Service approval selesai, system mungkin me-load work order detail atau edit form, yang memanggil queries di WorkOrderController yang masih pakai table lama.

## Solution Strategy
Gunakan **UNION ALL** untuk menggabungkan data dari 3 table baru:
- `inventory_batteries`
- `inventory_chargers`
- `inventory_attachments`

Menjadi satu result set yang compatible dengan code existing.

## Files to Fix
1. **Line 2656-2683**: Main attachmentRows query (get current unit components)
2. **Line 2756-2777**: attachmentOptions query (dropdown available)
3. **Line 2779-2793**: bateraiOptions query (dropdown available)
4. **Line 2795-2809**: chargerOptions query (dropdown available)  
5. **Line 2812-2827**: currentAttachments query (currently assigned)
6. **Line 2829-2843**: currentBaterais query (currently assigned)
7.  **Line 2845-2859**: currentChargers query (currently assigned)

## Query Mappings

### Old Schema → New Schema
| Old Table | Old Column | New Table | New Column |
|-----------|-----------|-----------|------------|
| inventory_attachment | id_inventory_attachment | inventory_batteries | id |
| inventory_attachment | tipe_item | (removed) | Use separate queries |
| inventory_attachment | sn_attachment | inventory_attachments | serial_number |
| inventory_attachment | sn_baterai | inventory_batteries | serial_number |
| inventory_attachment | sn_charger | inventory_chargers | serial_number |
| inventory_attachment | attachment_id | inventory_attachments | attachment_type_id |
| inventory_attachment | baterai_id | inventory_batteries | battery_type_id |
| inventory_attachment | charger_id | inventory_chargers | charger_type_id |
| inventory_attachment | attachment_status | inventory_* | status |
| inventory_attachment | id_inventory_unit | inventory_* | inventory_unit_id |

## Status

⏸️ **ON HOLD - Awaiting User Confirmation**

Reason: This file has 8 complex raw SQL queries that need careful refactoring. Need to confirm:
1. Is this WorkOrder edit modal actually called after SPK approval?
2. Can we test this safely without breaking work order module?

---
**Date**: March 12, 2026  
**Reporter**: GitHub Copilot  
**Priority**: HIGH (blocks SPK Service approval)
