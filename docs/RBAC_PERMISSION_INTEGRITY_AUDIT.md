# RBAC Permission Integrity Audit

## Langkah Jalankan Audit

1. Generate daftar key dari kode:

```bash
python tools/rbac/extract_permission_keys.py
```

2. Import isi `docs/RBAC_PERMISSION_CODE_KEYS.sql` ke database (membuat `tmp_code_permissions`).
3. Jalankan query-query di bawah.

## Query Audit

```sql
-- A. Key dipakai di kode tapi tidak ada di DB
SELECT c.key_name AS missing_in_db
FROM tmp_code_permissions c
LEFT JOIN permissions p ON p.key_name = c.key_name
WHERE p.id IS NULL
ORDER BY c.key_name;
```

```sql
-- B. Key ada di DB tapi tidak dipakai di kode
SELECT p.key_name AS unused_in_code
FROM permissions p
LEFT JOIN tmp_code_permissions c ON c.key_name = p.key_name
WHERE c.key_name IS NULL
ORDER BY p.key_name;
```

```sql
-- C. Orphan role_permissions
SELECT 'missing_permission_ref' AS issue, COUNT(*) AS total
FROM role_permissions rp
LEFT JOIN permissions p ON p.id = rp.permission_id
WHERE p.id IS NULL
UNION ALL
SELECT 'missing_role_ref' AS issue, COUNT(*) AS total
FROM role_permissions rp
LEFT JOIN roles r ON r.id = rp.role_id
WHERE r.id IS NULL;
```

```sql
-- D. Orphan + expired user_permissions
SELECT 'missing_permission_ref' AS issue, COUNT(*) AS total
FROM user_permissions up
LEFT JOIN permissions p ON p.id = up.permission_id
WHERE p.id IS NULL
UNION ALL
SELECT 'expired_overrides' AS issue, COUNT(*) AS total
FROM user_permissions up
WHERE up.expires_at IS NOT NULL AND up.expires_at <= NOW();
```

```sql
-- E. Konflik override user (grant + revoke)
SELECT
    up.user_id,
    p.key_name,
    SUM(CASE WHEN up.granted = 1 THEN 1 ELSE 0 END) AS grants,
    SUM(CASE WHEN up.granted = 0 THEN 1 ELSE 0 END) AS revokes
FROM user_permissions up
JOIN permissions p ON p.id = up.permission_id
GROUP BY up.user_id, p.key_name
HAVING grants > 0 AND revokes > 0
ORDER BY up.user_id, p.key_name;
```

```sql
-- F. Snapshot role grants modul prioritas
SELECT
    r.slug AS role_slug,
    p.key_name,
    rp.granted
FROM role_permissions rp
JOIN roles r ON r.id = rp.role_id
JOIN permissions p ON p.id = rp.permission_id
WHERE p.module IN ('marketing', 'purchasing', 'warehouse')
  AND rp.granted = 1
ORDER BY r.slug, p.key_name;
```
