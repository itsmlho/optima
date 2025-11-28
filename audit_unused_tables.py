#!/usr/bin/env python3
"""
Script untuk audit tabel database yang tidak digunakan di aplikasi
"""

import re
import os
from collections import defaultdict

def extract_tables_from_sql(sql_file):
    """Extract all table names from SQL file"""
    tables = set()
    with open(sql_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Find all CREATE TABLE statements
    pattern = r'CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?'
    matches = re.findall(pattern, content, re.IGNORECASE)
    tables.update(matches)
    
    return sorted(tables)

def search_table_in_codebase(table_name, codebase_path):
    """Search for table name references in codebase"""
    references = {
        'models': [],
        'controllers': [],
        'views': [],
        'migrations': [],
        'helpers': [],
        'other': []
    }
    
    # Patterns to search for
    patterns = [
        rf'\b{re.escape(table_name)}\b',  # Exact word match
        rf"['\"]{re.escape(table_name)}['\"]",  # In quotes
        rf'`{re.escape(table_name)}`',  # In backticks
    ]
    
    # Search in different directories
    search_paths = {
        'models': 'app/Models',
        'controllers': 'app/Controllers',
        'views': 'app/Views',
        'migrations': 'app/Database/Migrations',
        'helpers': 'app/Helpers',
        'other': 'app'
    }
    
    for category, base_path in search_paths.items():
        full_path = os.path.join(codebase_path, base_path)
        if not os.path.exists(full_path):
            continue
            
        for root, dirs, files in os.walk(full_path):
            for file in files:
                if not file.endswith(('.php', '.js', '.sql')):
                    continue
                    
                file_path = os.path.join(root, file)
                try:
                    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                        content = f.read()
                        for pattern in patterns:
                            if re.search(pattern, content, re.IGNORECASE):
                                rel_path = os.path.relpath(file_path, codebase_path)
                                if rel_path not in references[category]:
                                    references[category].append(rel_path)
                                break
                except Exception as e:
                    pass
    
    return references

def main():
    sql_file = '/opt/lampp/htdocs/optima1/databases/optima_ci_24-11-25_reorganized.sql'
    codebase_path = '/opt/lampp/htdocs/optima1'
    
    print("=" * 80)
    print("AUDIT TABEL DATABASE - MENCARI TABEL YANG TIDAK DIGUNAKAN")
    print("=" * 80)
    print()
    
    # Extract all tables
    print("📊 Mengekstrak daftar tabel dari SQL file...")
    all_tables = extract_tables_from_sql(sql_file)
    print(f"   Ditemukan {len(all_tables)} tabel\n")
    
    # Analyze each table
    unused_tables = []
    used_tables = []
    backup_tables = []
    
    print("🔍 Mencari referensi tabel di codebase...")
    print("   (Ini mungkin memakan waktu beberapa saat...)\n")
    
    for i, table in enumerate(all_tables, 1):
        print(f"   [{i}/{len(all_tables)}] Memeriksa: {table}", end=' ... ', flush=True)
        
        # Skip obvious backup tables
        if 'backup' in table.lower() or 'old' in table.lower():
            backup_tables.append(table)
            print("⏭️  (Backup table - diabaikan)")
            continue
        
        # Search for references
        refs = search_table_in_codebase(table, codebase_path)
        total_refs = sum(len(refs[k]) for k in refs)
        
        if total_refs == 0:
            unused_tables.append((table, refs))
            print("❌ TIDAK DIGUNAKAN")
        else:
            used_tables.append((table, refs, total_refs))
            print(f"✅ Digunakan ({total_refs} referensi)")
    
    print("\n" + "=" * 80)
    print("HASIL AUDIT")
    print("=" * 80)
    print()
    
    # Summary
    print(f"📈 RINGKASAN:")
    print(f"   Total tabel: {len(all_tables)}")
    print(f"   ✅ Tabel yang digunakan: {len(used_tables)}")
    print(f"   ❌ Tabel yang TIDAK digunakan: {len(unused_tables)}")
    print(f"   ⏭️  Tabel backup: {len(backup_tables)}")
    print()
    
    # Unused tables
    if unused_tables:
        print("=" * 80)
        print("❌ TABEL YANG TIDAK DIGUNAKAN (Kandidat untuk dihapus)")
        print("=" * 80)
        print()
        for table, refs in unused_tables:
            print(f"   • {table}")
        print()
    
    # Backup tables
    if backup_tables:
        print("=" * 80)
        print("⏭️  TABEL BACKUP (Bisa dihapus jika tidak diperlukan)")
        print("=" * 80)
        print()
        for table in backup_tables:
            print(f"   • {table}")
        print()
    
    # Used tables (summary)
    print("=" * 80)
    print("✅ TABEL YANG DIGUNAKAN")
    print("=" * 80)
    print()
    print("   (Daftar lengkap ada di bawah)")
    print()
    
    # Detailed unused tables
    if unused_tables:
        print("=" * 80)
        print("📋 DETAIL TABEL YANG TIDAK DIGUNAKAN")
        print("=" * 80)
        print()
        for table, refs in unused_tables:
            print(f"\n   📌 {table}")
            print(f"      Tidak ditemukan referensi di codebase")
            print(f"      ⚠️  PERHATIKAN: Pastikan tabel ini benar-benar tidak digunakan")
            print(f"         sebelum menghapusnya!")
    
    # Save to file
    output_file = '/opt/lampp/htdocs/optima1/databases/unused_tables_audit.txt'
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write("=" * 80 + "\n")
        f.write("AUDIT TABEL DATABASE - TABEL YANG TIDAK DIGUNAKAN\n")
        f.write("=" * 80 + "\n\n")
        f.write(f"Total tabel: {len(all_tables)}\n")
        f.write(f"Tabel yang digunakan: {len(used_tables)}\n")
        f.write(f"Tabel yang TIDAK digunakan: {len(unused_tables)}\n")
        f.write(f"Tabel backup: {len(backup_tables)}\n\n")
        
        f.write("=" * 80 + "\n")
        f.write("TABEL YANG TIDAK DIGUNAKAN\n")
        f.write("=" * 80 + "\n\n")
        for table, refs in unused_tables:
            f.write(f"- {table}\n")
        
        f.write("\n" + "=" * 80 + "\n")
        f.write("TABEL BACKUP\n")
        f.write("=" * 80 + "\n\n")
        for table in backup_tables:
            f.write(f"- {table}\n")
    
    print(f"\n💾 Hasil audit disimpan ke: {output_file}")
    print()

if __name__ == '__main__':
    main()


