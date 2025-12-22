# Quick Reference: Trigger Events System

## 🎯 Untuk Developer

### Menambahkan Event Baru

**Step 1: Tambah ke tabel trigger_events**
```sql
INSERT INTO trigger_events (event_code, event_name, description, category, module, is_active)
VALUES (
    'new_event_code',
    'Nama Event yang Readable',
    'Deskripsi kapan event ini trigger',
    'category_name',
    'module_name',
    1
);
```

**Step 2: Buat helper function**
```php
// File: app/Helpers/notification_helper.php
if (!function_exists('notify_new_event')) {
    function notify_new_event($data)
    {
        return send_notification('new_event_code', [
            'module' => 'module_name',
            'id' => $data['id'] ?? null,
            'variable_name' => $data['variable_name'] ?? '',
            'url' => $data['url'] ?? ''
        ]);
    }
}
```

**Step 3: Buat notification rule**
```sql
INSERT INTO notification_rules (
    name, trigger_event, title_template, message_template,
    target_divisions, type, is_active
) VALUES (
    'Rule Name',
    'new_event_code',
    'Title: {{variable_name}}',
    'Message: {{variable_name}}',
    'Division1,Division2',
    'info',
    1
);
```

**Step 4: Panggil di controller**
```php
if (function_exists('notify_new_event')) {
    notify_new_event([
        'id' => $id,
        'variable_name' => $value,
        'url' => base_url('path')
    ]);
}
```

---

## 🔍 Query Berguna

### Cek event tersedia
```sql
SELECT event_code, event_name, category 
FROM trigger_events 
WHERE category = 'work_order';
```

### Cek notification rules untuk event
```sql
SELECT name, target_divisions, target_roles, type
FROM notification_rules
WHERE trigger_event = 'event_code';
```

### Cek event yang belum ada rulesnya
```sql
SELECT te.event_code, te.event_name
FROM trigger_events te
LEFT JOIN notification_rules nr ON te.event_code = nr.trigger_event
WHERE nr.id IS NULL;
```

### Event paling banyak dipakai
```sql
SELECT te.event_code, COUNT(nr.id) as usage
FROM trigger_events te
LEFT JOIN notification_rules nr ON te.event_code = nr.trigger_event
GROUP BY te.id
ORDER BY usage DESC
LIMIT 10;
```

---

## ⚠️ Penting!

1. **Jangan hardcode event names** - selalu gunakan yang ada di `trigger_events`
2. **Foreign key akan mencegah** typo event names
3. **Gunakan function_exists()** sebelum memanggil notify function
4. **Test notifikasi** sebelum deploy ke production

---

## 📋 Work Order Verification Example

```php
// Get old data first
$oldUnitData = $db->table('inventory_unit')->where('id', $unitId)->get()->getRowArray();

// Update data
$db->table('inventory_unit')->where('id', $unitId)->update($newData);

// Detect changes
$allChanges = [];
if ($oldUnitData['serial_number'] != $newData['serial_number']) {
    $allChanges[] = "Serial Number: {$oldUnitData['serial_number']} → {$newData['serial_number']}";
}

// Send notification if changes exist
if (!empty($allChanges)) {
    notify_work_order_unit_verified([
        'work_order_id' => $woId,
        'wo_number' => 'WO-2024-001',
        'unit_code' => 'FL-001',
        'changes_count' => count($allChanges),
        'changes_list' => implode("\n- ", $allChanges),
        'created_by' => session('username')
    ]);
}
```

---

## 📞 Need Help?

- Dokumentasi lengkap: `TRIGGER_EVENTS_DOCUMENTATION.md`
- Implementation details: `WORK_ORDER_VERIFICATION_COMPLETE.md`
- Test queries: `test_notification_system.sql`
