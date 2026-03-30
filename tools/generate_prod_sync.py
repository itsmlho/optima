#!/usr/bin/env python3
"""Generate production sync SQL for deduplication of permissions table."""

import csv

PERM_CSV = r'c:\laragon\www\optima\databases\Input_Data\permissions (1).csv'
RP_CSV   = r'c:\laragon\www\optima\databases\Input_Data\role_permissions (1).csv'

# ---- Load permissions ----
perms = {}
with open(PERM_CSV, encoding='utf-8') as f:
    reader = csv.DictReader(f, delimiter=';')
    for row in reader:
        perms[int(row['id'])] = {
            'module':      row['module'],
            'page':        row['page'],
            'action':      row['action'],
            'key_name':    row['key_name'],
            'display_name': row['display_name'],
            'description': row['description'],
            'category':    row['category'],
        }

# ---- Load role_permissions ----
role_perms = {}  # permission_id -> list of (rp_id, role_id, granted)
with open(RP_CSV, encoding='utf-8') as f:
    reader = csv.DictReader(f, delimiter=';')
    for row in reader:
        pid = int(row['permission_id'])
        role_perms.setdefault(pid, []).append({
            'id':       int(row['id']),
            'role_id':  int(row['role_id']),
            'granted':  int(row['granted']),
        })

# ---- Print analysis ----
print("=== DUPLICATE IDs IN PRODUCTION ===")
for pid, p in sorted(perms.items()):
    if (p['module']=='marketing' and p['page']=='kontrak') or \
       (p['module']=='service'   and p['page']=='workorder') or \
       (p['module']=='warehouse' and p['page']=='unit_inventory'):
        rp = role_perms.get(pid, [])
        print(f"  ID {pid:4d}: {p['key_name']:<55}  role_perms: {len(rp)}")

print()
print("=== CANONICAL IDs IN PRODUCTION ===")
for pid, p in sorted(perms.items()):
    if (p['module']=='marketing' and p['page']=='contract') or \
       (p['module']=='service'   and p['page']=='work_order') or \
       (p['module']=='warehouse' and p['page']=='inventory_unit'):
        rp = role_perms.get(pid, [])
        print(f"  ID {pid:4d}: {p['key_name']:<55}  role_perms: {len(rp)}")

# ---- Build action maps for canonical pages ----
# For each module, map action -> canonical_id
canonical = {}
for pid, p in perms.items():
    key = (p['module'], p['page'])
    if key in [('marketing','contract'), ('service','work_order'), ('warehouse','inventory_unit')]:
        canonical[(p['module'], p['page'], p['action'])] = pid

# ---- Generate SQL ----
lines = []
lines.append("-- ================================================================")
lines.append("-- PRODUCTION SYNC SQL: Deduplicate permissions")
lines.append("-- Run on Hostinger via phpMyAdmin")
lines.append("-- Generated automatically - review before running!")
lines.append("-- ================================================================")
lines.append("")
lines.append("START TRANSACTION;")
lines.append("")

# Duplicate groups: (module, old_page, canonical_page)
groups = [
    ('marketing', 'kontrak',        'contract'),
    ('service',   'workorder',      'work_order'),
    ('warehouse', 'unit_inventory', 'inventory_unit'),
]

def esc(s):
    return s.replace("'", "''")

for module, dup_page, can_page in groups:
    lines.append(f"-- ----------------------------------------------------------------")
    lines.append(f"-- {module}.{dup_page} -> {module}.{can_page}")
    lines.append(f"-- ----------------------------------------------------------------")

    # Find all dup entries
    dup_ids  = [(pid, p) for pid, p in perms.items()
                if p['module']==module and p['page']==dup_page]

    to_delete  = []  # (dup_id, action, canonical_id)
    to_rename  = []  # (dup_id, action)  -- no canonical exists yet

    for dup_id, p in sorted(dup_ids, key=lambda x: x[1]['action']):
        action = p['action']
        can_id = canonical.get((module, can_page, action))
        if can_id:
            to_delete.append((dup_id, action, can_id))
        else:
            to_rename.append((dup_id, action, p))

    # Step 1: Migrate role_permissions for duplicates (INSERT IGNORE to canonical)
    if to_delete:
        lines.append(f"")
        lines.append(f"-- Step 1: Migrate role_permissions for duplicates -> canonical")
        for dup_id, action, can_id in to_delete:
            rp_list = role_perms.get(dup_id, [])
            if rp_list:
                for rp in rp_list:
                    lines.append(
                        f"INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at) "
                        f"VALUES ({rp['role_id']}, {can_id}, {rp['granted']}, NULL, NOW()); "
                        f"-- migrating role={rp['role_id']} from {module}.{dup_page}.{action}({dup_id}) -> {module}.{can_page}.{action}({can_id})"
                    )

    # Step 2: Remove role_permissions for duplicates
    delete_ids = [str(d[0]) for d in to_delete]
    if delete_ids:
        lines.append(f"")
        lines.append(f"-- Step 2: Remove role_permissions for duplicate IDs")
        lines.append(f"DELETE FROM role_permissions WHERE permission_id IN ({', '.join(delete_ids)});")

    # Step 3: Delete duplicate permissions
    if delete_ids:
        lines.append(f"")
        lines.append(f"-- Step 3: Delete duplicate permission rows")
        lines.append(f"DELETE FROM permissions WHERE id IN ({', '.join(delete_ids)});")

    # Step 4: Rename unique actions to canonical page
    if to_rename:
        lines.append(f"")
        lines.append(f"-- Step 4: Rename unique '{dup_page}' actions -> '{can_page}' (these have no duplicate)")
        for dup_id, action, p in to_rename:
            new_key = f"{module}.{can_page}.{action}"
            new_display = p['display_name'].replace(dup_page.title(), can_page.replace('_',' ').title())
            lines.append(
                f"UPDATE permissions SET page='{can_page}', key_name='{esc(new_key)}' "
                f"WHERE id={dup_id}; "
                f"-- {p['key_name']} -> {new_key}"
            )

    lines.append("")

lines.append("COMMIT;")
lines.append("")
lines.append("-- ================================================================")
lines.append("-- Verification queries (run after COMMIT):")
lines.append("-- ================================================================")
lines.append("SELECT module, page, COUNT(*) cnt FROM permissions")
lines.append("  WHERE (module='marketing' AND page IN ('kontrak','contract'))")
lines.append("     OR (module='service'   AND page IN ('workorder','work_order'))")
lines.append("     OR (module='warehouse' AND page IN ('unit_inventory','inventory_unit'))")
lines.append("  GROUP BY module, page ORDER BY module, page;")

sql = "\n".join(lines)
print()
print("=== GENERATED SQL ===")
print(sql)

# Write to file
out_path = r'c:\laragon\www\optima\databases\migrations\production_dedup_sync.sql'
with open(out_path, 'w', encoding='utf-8') as f:
    f.write(sql)
print(f"\n[OK] Written to: {out_path}")
