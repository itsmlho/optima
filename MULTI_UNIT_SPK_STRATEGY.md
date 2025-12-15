# Multi-Unit SPK Strategy & Implementation Guide

## Overview
Dokumentasi lengkap untuk implementasi handling multi-unit SPK dalam sistem OPTIMA, mulai dari Unit Preparation workflow hingga Item Selection dan display coordination.

## Current Status ✅

### Completed Components
1. **Multi-Mechanic Selection System**
   - ✅ SPKMechanicMultiSelect JavaScript component
   - ✅ Role-based filtering (mekanik_maintenance, mekanik_fabrikasi, mekanik_pengiriman)
   - ✅ Limit validation per role
   - ✅ Database schema: `spk_stage_mechanics` table
   - ✅ API integration: `/service/employeesByRoles`

2. **Database Structure**
   - ✅ `spk_unit_stages` table untuk tracking per-unit workflow
   - ✅ `spk_stage_mechanics` table untuk multi-mechanic assignments
   - ✅ JSON storage dalam `spesifikasi` field untuk prepared_units

3. **SPK Detail Modal Fixes**
   - ✅ Fixed 500 errors by removing `delivery_instruction_items` references
   - ✅ Unified workflow display between Service and Marketing views
   - ✅ Enhanced stage status tracking with mechanic/date information

4. **"Item yang dipilih" Display Unification**
   - ✅ Copied exact Service view structure to Marketing
   - ✅ `prepared_units_detail` vs fallback `renderItemBlock` logic
   - ✅ Consistent CSS styling with `svcUnitDetailBlock`

## Multi-Unit SPK Architecture

### Data Flow Structure
```
Single SPK → Multiple Units → Individual Unit Stages → Per-Unit Display

SPK {
  id: 123,
  jumlah_unit: 3,
  jenis_spk: 'UNIT'
}
↓
spk_unit_stages {
  spk_id: 123, unit_index: 1, stage_name: 'persiapan_unit', unit_id: 456
  spk_id: 123, unit_index: 2, stage_name: 'persiapan_unit', unit_id: 789
  spk_id: 123, unit_index: 3, stage_name: 'persiapan_unit', unit_id: 012
}
↓
prepared_units_detail[] {
  [0]: {unit_label: "F001", serial_number: "SN123", mekanik: "John", ...},
  [1]: {unit_label: "F002", serial_number: "SN456", mekanik: "Jane", ...},
  [2]: {unit_label: "F003", serial_number: "SN789", mekanik: "Bob", ...}
}
```

### Unit Preparation Workflow
```
1. SPK Created (Marketing) → status: DRAFT
2. Unit Selection Phase (Service) → Select individual units for each unit_index
3. Multi-Mechanic Assignment → Assign mechanics per stage per unit
4. Unit Preparation Complete → Each unit tracked individually
5. Quality Check → Per-unit quality verification
6. Ready Status → All units completed and verified
```

## Implementation Strategy

### 1. Unit Preparation Modal Enhancement 🚧

**Current State**: Single unit preparation modal
**Target State**: Multi-unit preparation with individual unit tracking

#### Required Changes:
- **Modal UI**: Add unit selector dropdown in Unit Preparation modal
- **Unit Progress Tracking**: Show preparation progress per unit (1/3, 2/3, 3/3)
- **Individual Unit Assignment**: Each unit gets its own preparation entry
- **Mechanic Assignment per Unit**: Different mechanics can work on different units

#### Code Changes Needed:
```javascript
// In spk_service.php - Unit Preparation Modal
function openPersiapanUnitModal(spkId) {
  // Fetch SPK details to determine total units
  fetch(`/service/spk/detail/${spkId}`)
  .then(response => response.json())
  .then(data => {
    const totalUnits = parseInt(data.spk.jumlah_unit || 1);
    const completedUnits = getCompletedUnits(spkId);
    
    // Show unit selector for multi-unit SPKs
    if (totalUnits > 1) {
      showUnitSelector(totalUnits, completedUnits);
    }
    
    // Load existing prepared units
    populateExistingUnits(data.prepared_units_detail);
  });
}
```

### 2. Unit Selection Interface 🚧

**Current State**: Single unit selection
**Target State**: Individual unit selection per unit_index

#### Required Changes:
- **Unit Counter**: "Preparing Unit 2 of 5" indicator
- **Unit-Specific Data**: Each unit gets individual attachment, mekanik, catatan
- **Progress Bar**: Visual indication of completion status
- **Navigation**: Previous/Next unit buttons for easier navigation

### 3. "Item yang dipilih" Multi-Unit Display ✅

**Current State**: ✅ Implemented with exact Service structure
**Features**: 
- Displays each unit individually with full details
- Consistent styling between Service and Marketing
- Proper fallback for legacy workflow

### 4. Database Enhancements 🚧

#### Required Schema Updates:
```sql
-- Add unit progress tracking
ALTER TABLE spk_unit_stages 
ADD COLUMN progress_percentage INT DEFAULT 0,
ADD COLUMN estimated_completion DATETIME NULL,
ADD COLUMN notes TEXT NULL;

-- Add multi-unit coordination table
CREATE TABLE spk_multi_unit_coordination (
  id INT PRIMARY KEY AUTO_INCREMENT,
  spk_id INT NOT NULL,
  total_units INT NOT NULL,
  completed_units INT DEFAULT 0,
  coordination_notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (spk_id) REFERENCES spk(id)
);
```

## Implementation Phases

### Phase 1: ✅ Foundation (COMPLETED)
- [x] Multi-mechanic dropdown component
- [x] Database schema setup
- [x] SPK detail modal fixes
- [x] Display unification between Service/Marketing

### Phase 2: 🚧 Unit Preparation Enhancement (NEXT)
- [ ] Multi-unit preparation modal
- [ ] Unit progress tracking
- [ ] Individual unit assignment workflow
- [ ] Unit navigation interface

### Phase 3: 🔄 Workflow Integration
- [ ] Quality check per unit
- [ ] Delivery coordination for multi-unit SPKs
- [ ] Status synchronization across units
- [ ] Notification system for multi-unit completion

### Phase 4: 📊 Reporting & Analytics
- [ ] Multi-unit performance metrics
- [ ] Individual unit tracking reports
- [ ] Resource allocation analytics
- [ ] Timeline optimization suggestions

## Technical Considerations

### Performance Optimization
- **Lazy Loading**: Load unit details only when needed
- **Batch Operations**: Process multiple units in single requests where possible
- **Caching**: Cache prepared_units_detail to avoid repeated database queries
- **Pagination**: For SPKs with many units (>10), implement pagination

### Error Handling
- **Partial Failures**: Handle cases where some units complete but others fail
- **Recovery Mechanisms**: Allow resuming multi-unit preparation from interruption
- **Validation**: Ensure all units are prepared before marking SPK as READY
- **Rollback Support**: Ability to rollback individual unit preparations

### User Experience
- **Progress Indication**: Clear visual feedback on multi-unit completion
- **Batch Actions**: Allow applying same settings to multiple units
- **Shortcuts**: Quick navigation between units
- **Auto-save**: Prevent data loss during multi-unit workflows

## API Endpoints Required

### New Endpoints Needed:
```php
// Get multi-unit preparation status
GET /service/spk/{id}/multi-unit-status

// Save unit preparation (individual)
POST /service/spk/{id}/prepare-unit/{unitIndex}

// Get unit preparation details
GET /service/spk/{id}/unit/{unitIndex}/details

// Batch update multiple units
POST /service/spk/{id}/batch-prepare-units
```

### Enhanced Existing Endpoints:
```php
// Enhanced to include unit_index parameter
POST /service/spk/approval/{stage}
// Add unit_index to track which specific unit is being approved

// Enhanced to return multi-unit progress
GET /service/spk/detail/{id}
// Include unit completion percentage and individual unit statuses
```

## Testing Strategy

### Test Cases Required:
1. **Single Unit SPK**: Ensure backward compatibility
2. **Multi Unit SPK (2-5 units)**: Standard multi-unit workflow
3. **Large Multi Unit SPK (10+ units)**: Performance and pagination
4. **Partial Completion**: Some units ready, others in progress
5. **Error Recovery**: Handle failures gracefully
6. **Cross-browser**: Ensure compatibility across browsers
7. **Mobile Responsive**: Multi-unit interface on mobile devices

## Success Metrics

### Performance Targets:
- **Modal Load Time**: <2 seconds for up to 10 units
- **Unit Switching**: <500ms navigation between units
- **Batch Operations**: Process 5+ units in <5 seconds
- **Database Queries**: Minimize N+1 queries for multi-unit data

### User Experience Targets:
- **Task Completion**: 95% success rate for multi-unit preparation
- **User Satisfaction**: Positive feedback on workflow efficiency
- **Error Rate**: <5% user errors in multi-unit workflows
- **Training Time**: <30 minutes for users to master multi-unit features

## Next Steps Immediate Action Items

1. **🎯 Implement Unit Selector in Preparation Modal**
   - Add dropdown showing "Unit 1 of 3", "Unit 2 of 3", etc.
   - Load existing unit data when switching between units
   - Save progress per unit individually

2. **📊 Add Unit Progress Indicators**
   - Progress bar showing "2/5 units prepared"
   - Color coding: Green (complete), Yellow (in progress), Gray (pending)
   - Unit status summary in SPK detail view

3. **🔄 Enhance Navigation Flow**
   - "Previous Unit" / "Next Unit" buttons
   - "Save and Next" workflow for efficient processing
   - Skip to specific unit functionality

4. **📝 Update Documentation**
   - User guide for multi-unit SPK workflow
   - Technical documentation for developers
   - API documentation updates

---

**Last Updated**: December 2024
**Status**: Foundation Complete, Ready for Phase 2 Implementation
**Next Milestone**: Multi-Unit Preparation Modal Enhancement