# COLOR MAPPING STANDARDS - OPTIMA STATISTICS CARDS

## Business Logic Color Mapping

### SEMANTIC COLOR RULES
1. **PRIMARY (bg-primary)** - Total/Overall Count
   - TOTAL [anything] (e.g., TOTAL SPK, TOTAL WORK ORDERS, TOTAL UNITS)
   - Master data counts (customers, suppliers, products)

2. **SUCCESS (bg-success)** - Positive/Completed Status  
   - READY, COMPLETED, SELESAI, AKTIF
   - Successful operations, finished tasks
   - Active/online status

3. **WARNING (bg-warning)** - In Progress/Pending Status
   - IN PROGRESS, PENDING, DIRENCANAKAN
   - Process ongoing, waiting for action
   - Medium priority items

4. **DANGER (bg-danger)** - Critical/Problem Items
   - REJECTED, CRITICAL, ALERTS, URGENT
   - Error conditions, high priority issues
   - Items requiring immediate attention

5. **INFO (bg-info)** - Information/Secondary Metrics
   - SPAREPART (inventory related)
   - DALAM PERJALANAN (in transit)
   - Reference data, secondary information

6. **SECONDARY (bg-secondary)** - Inactive/Disabled Items
   - INACTIVE, DISABLED
   - Items not currently in use

## CURRENT INCONSISTENCIES TO FIX

### Admin Dashboard (admin/index.php)
- Current: System Status (PRIMARY), Active Users (SUCCESS), Database Size (INFO), System Load (WARNING)
- Should be: System Status (SUCCESS), Active Users (PRIMARY), Database Size (INFO), System Load (WARNING)

### Marketing SPK (marketing/spk.php)  
- Current: TOTAL SPK (PRIMARY), IN PROGRESS (WARNING), READY (SUCCESS), [fourth card INFO]
- ✅ Correct - follows standard logic

### Marketing DI (marketing/di.php)
- Current: TOTAL (PRIMARY), DIRENCANAKAN (WARNING), DALAM PERJALANAN (INFO), SELESAI (SUCCESS) 
- ✅ Correct - follows standard logic

### Service Dashboard (dashboard/service.php)
- Current: TOTAL WORK ORDERS (PRIMARY), PENDING PMPS (WARNING), COMPLETED SERVICES (SUCCESS), MAINTENANCE ALERTS (DANGER)
- ✅ Correct - follows standard logic

### Warehouse Dashboard (dashboard/warehouse.php)
- Current: TOTAL STOCK (PRIMARY), AVAILABLE ITEMS (SUCCESS), LOW STOCK (WARNING), STOCK LOCATIONS (INFO)
- ✅ Correct - follows standard logic

### Rejected Items (warehouse/purchase_orders/rejected_items.php)
- Current: UNIT (DANGER), ATTACHMENT (WARNING), SPAREPART (INFO), TOTAL (PRIMARY)
- Should be: UNIT (DANGER), ATTACHMENT (DANGER), SPAREPART (DANGER), TOTAL (PRIMARY)
- Logic: All rejected items should be DANGER since they represent problems

## IMPLEMENTATION PRIORITY

### HIGH PRIORITY
1. Fix admin/index.php - System Status should be SUCCESS not PRIMARY
2. Fix rejected items page - all rejection categories should use DANGER
3. Standardize any remaining dashboard inconsistencies

### MEDIUM PRIORITY  
1. Verify all dashboard pages follow the color logic
2. Check inventory and operational pages
3. Ensure badge colors in tables follow same logic

## COLOR ACCESSIBILITY
- Maintain current text-white for readability
- Keep opacity-75 for icons
- Ensure sufficient contrast ratios
- Test with colorblind-friendly palette

## VALIDATION CHECKLIST
- [ ] All TOTAL metrics use PRIMARY
- [ ] All COMPLETED/READY use SUCCESS  
- [ ] All IN PROGRESS/PENDING use WARNING
- [ ] All REJECTED/CRITICAL use DANGER
- [ ] All SPAREPART/INFO metrics use INFO
- [ ] Consistent across all modules (admin, warehouse, service, marketing, purchasing)