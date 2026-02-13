# Unit Accessories Reference

**Last Updated:** February 14, 2026  
**Purpose:** Standardized list of unit accessories for quotation and inventory management

---

## Accessories List (21 Items)

| No | Display Name | JSON Key (Database) | Description |
|----|--------------|---------------------|-------------|
| 1  | Main Light (Main, Reverse, Signal, Stop) | `main_light` | Lampu utama dan sinyal |
| 2  | Blue Spot | `blue_spot` | Lampu sorot biru keselamatan |
| 3  | Red Line | `red_line` | Lampu garis merah zona bahaya |
| 4  | Work Light (Lampu Sorot) | `work_light` | Lampu kerja tambahan |
| 5  | Rotary Lamp | `rotary_lamp` | Lampu rotari/hazard |
| 6  | Back Buzzer | `back_buzzer` | Alarm mundur |
| 7  | Camera AI | `camera_ai` | Kamera dengan deteksi AI |
| 8  | Camera | `camera` | Kamera standar (non-AI) |
| 9  | Sensor Parking | `sensor_parking` | Sensor parkir/jarak |
| 10 | Speed Limiter | `speed_limiter` | Pembatas kecepatan unit |
| 11 | Laser Fork | `laser_fork` | Penunjuk laser pada garpu |
| 12 | Voice Announcer | `voice_announcer` | Pengeras suara peringatan |
| 13 | Horn Speaker | `horn_speaker` | Klakson tipe speaker |
| 14 | Horn Klason | `horn_klason` | Klakson standar |
| 15 | Bio Metric | `bio_metric` | Fingerprint/Face ID starter |
| 16 | Acrylic | `acrylic` | Atap/pelindung akrilik |
| 17 | First Aid Kit | `first_aid_kit` | Kotak P3K |
| 18 | Safety Belt Standar | `safety_belt` | Sabuk pengaman biasa |
| 19 | Safety Belt Interlock | `safety_belt_interlock` | Sabuk pengaman sistem kunci mesin |
| 20 | Spark Arrestor | `spark_arrestor` | Knalpot anti-percikan api |
| 21 | Mirror (Spion) | `mirror` | Kaca spion unit |

---

## Database Storage

**Field:** `unit_accessories` (TEXT/JSON)  
**Format:** JSON array of selected keys

**Example:**
```json
["main_light", "blue_spot", "camera_ai", "speed_limiter", "safety_belt_interlock"]
```

---

## Implementation Locations

### 1. Marketing - Add Unit Specification
**File:** `app/Views/marketing/quotations.php` (Lines ~420-560)
- Form with 21 checkboxes (3 columns layout)
- Input name: `aksesoris[]`
- Value: JSON key (snake_case)

### 2. Database Tables
- **`quotation_specifications.unit_accessories`** - Stores selected accessories for quotation specs
- **`inventory_unit.aksesoris`** - Stores selected accessories for actual units

### 3. Display Components
- **Print SPK:** Shows accessories in SPK document
- **Service View:** Shows required accessories for unit preparation
- **Inventory:** Shows installed accessories on units

---

## Usage Guidelines

### For Marketing Team
1. Select accessories that customer specifically requests
2. Use snake_case keys for database consistency
3. If unusual accessory needed, add to Notes field instead

### For Service Team
- Verify selected accessories when preparing units
- Match installed accessories with quotation requirements
- Report missing accessories to procurement

### For Developers
- Always use JSON key (snake_case) for programmatic access
- Use Display Name for user interfaces
- Validate accessories array before saving to database

---

## Migration Notes

**Old Format (UPPERCASE):**
```
["LAMPU UTAMA", "BLUE SPOT", "CAMERA AI"]
```

**New Format (snake_case):**
```
["main_light", "blue_spot", "camera_ai"]
```

**Migration Status:**
- ✅ Form updated to use new format (Feb 14, 2026)
- ⏳ Existing data migration - to be scheduled
- ⏳ Display mapping for old format - backward compatibility

---

## Adding New Accessories

**To add a new accessory:**

1. Choose appropriate JSON key (snake_case, descriptive)
2. Add to form in `app/Views/marketing/quotations.php`
3. Update this reference document
4. Test in quotation creation workflow
5. Verify display in print SPK and service views

**Example:**
```html
<div class="col-md-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" 
               name="aksesoris[]" 
               value="new_accessory_key" 
               id="acc_new_accessory">
        <label class="form-check-label" for="acc_new_accessory">
            New Accessory Display Name
        </label>
    </div>
</div>
```

---

## Related Files

- **Form:** `app/Views/marketing/quotations.php`
- **Model:** `app/Models/QuotationSpecificationModel.php`
- **Print View:** `app/Views/marketing/print_spk.php`
- **Service View:** `app/Views/service/spk_service.php`

---

## Changelog

### 2026-02-14
- ✅ Updated accessories list from 18 to 21 items
- ✅ Added: Safety Belt Standar, Mirror (Spion), Horn Klason separate from Horn Speaker
- ✅ Changed format from UPPERCASE to snake_case for consistency
- ✅ Updated layout from 4 columns to 3 columns for better readability
- ✅ Standardized display names with descriptive suffixes
