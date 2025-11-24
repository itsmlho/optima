#!/usr/bin/env python3
"""
Script untuk verifikasi detail tabel yang terdeteksi tidak digunakan
"""

import re
import os
from collections import defaultdict

# Tabel yang perlu diverifikasi
tables_to_check = [
    'delivery_workflow_log',
    'di_workflow_stages',
    'kontrak_status_changes',
    'migration_log',
    'migration_log_di_workflow',
    'optimization_additional_log',
    'optimization_log',
    'rbac_audit_log',
    'spk_component_transactions',
    'spk_edit_permissions',
    'spk_units',
    'supplier_contacts',
    'supplier_documents',
    'supplier_performance_log',
    'unit_replacement_log',
    'unit_status_log',
    'work_order_attachments'
]

def search_comprehensive(table_name, codebase_path):
    """Search dengan berbagai pattern untuk menemukan referensi"""
    results = {
        'direct_references': [],
        'model_references': [],
        'variable_references': [],
        'sql_queries': [],
        'migrations': [],
        'views': [],
        'procedures': []
    }
    
    # Patterns untuk pencarian
    patterns = [
        (rf'\b{re.escape(table_name)}\b', 'direct'),
        (rf"['\"]{re.escape(table_name)}['\"]", 'quoted'),
        (rf'`{re.escape(table_name)}`', 'backtick'),
        (rf'\${re.escape(table_name)}', 'variable'),
        (rf'->{re.escape(table_name)}', 'method'),
        (rf'protected \$table\s*=\s*[\'"]{re.escape(table_name)}[\'"]', 'model_table'),
    ]
    
    # Search di seluruh codebase (EXCLUDE SQL dump files)
    for root, dirs, files in os.walk(codebase_path):
        # Skip vendor, node_modules, databases (SQL dumps), dan .git
        if any(skip in root for skip in ['vendor', 'node_modules', '.git', 'databases']):
            continue
            
        for file in files:
            # Only search in PHP and JS files (not SQL dumps)
            if not file.endswith(('.php', '.js')):
                continue
                
            file_path = os.path.join(root, file)
            try:
                with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    lines = content.split('\n')
                    
                    for pattern, pattern_type in patterns:
                        matches = re.finditer(pattern, content, re.IGNORECASE)
                        for match in matches:
                            # Skip if it's in a comment or string that's just defining the table
                            context = content[max(0, match.start()-50):match.end()+50]
                            if 'CREATE TABLE' in context or 'INSERT INTO' in context:
                                continue
                                
                            line_num = content[:match.start()].count('\n') + 1
                            line_content = lines[line_num - 1].strip() if line_num <= len(lines) else ''
                            
                            rel_path = os.path.relpath(file_path, codebase_path)
                            
                            if 'Model' in rel_path and pattern_type == 'model_table':
                                results['model_references'].append((rel_path, line_num, line_content))
                            elif 'Migration' in rel_path:
                                results['migrations'].append((rel_path, line_num, line_content))
                            else:
                                results['direct_references'].append((rel_path, line_num, line_content))
            except Exception as e:
                pass
    
    # Check in SQL files in app/Database/SQL (not dump files)
    app_sql_path = os.path.join(codebase_path, 'app', 'Database', 'SQL')
    if os.path.exists(app_sql_path):
        for file in os.listdir(app_sql_path):
            if file.endswith('.sql'):
                file_path = os.path.join(app_sql_path, file)
                try:
                    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                        content = f.read()
                        if re.search(rf'\b{re.escape(table_name)}\b', content, re.IGNORECASE):
                            results['sql_queries'].append((f'app/Database/SQL/{file}', 0, ''))
                except:
                    pass
    
    # Cek di SQL file untuk views dan procedures
    sql_file = os.path.join(codebase_path, 'databases', 'optima_db_24-11-25_reorganized.sql')
    if os.path.exists(sql_file):
        with open(sql_file, 'r', encoding='utf-8', errors='ignore') as f:
            sql_content = f.read()
            
            # Cek di views
            view_pattern = rf'CREATE.*VIEW.*?{re.escape(table_name)}'
            if re.search(view_pattern, sql_content, re.IGNORECASE | re.DOTALL):
                results['views'].append('Found in SQL views')
            
            # Cek di procedures
            proc_pattern = rf'CREATE.*PROCEDURE.*?{re.escape(table_name)}'
            if re.search(proc_pattern, sql_content, re.IGNORECASE | re.DOTALL):
                results['procedures'].append('Found in SQL procedures')
    
    return results

def check_replacement_table(table_name):
    """Cek apakah ada tabel pengganti"""
    replacements = {
        'spk_units': 'spk_unit_stages',
        'work_order_attachments': 'work_order_attachments (mungkin masih diperlukan)',
        'unit_status_log': 'system_activity_log atau unit_workflow_log',
        'unit_replacement_log': 'system_activity_log',
        'supplier_contacts': 'suppliers (mungkin data kontak ada di tabel suppliers)',
        'supplier_documents': 'suppliers (mungkin data dokumen ada di tabel suppliers)',
        'supplier_performance_log': 'suppliers (mungkin data performa ada di tabel suppliers)',
        'delivery_workflow_log': 'system_activity_log atau delivery_instructions',
        'di_workflow_stages': 'delivery_instructions (mungkin workflow sudah diintegrasikan)',
        'kontrak_status_changes': 'system_activity_log atau kontrak',
        'migration_log': 'migrations (tabel standar CodeIgniter)',
        'migration_log_di_workflow': 'migrations (tabel standar CodeIgniter)',
        'optimization_log': 'system_activity_log',
        'optimization_additional_log': 'system_activity_log',
        'rbac_audit_log': 'system_activity_log',
        'spk_component_transactions': 'spk_unit_stages atau spk',
        'spk_edit_permissions': 'permissions atau role_permissions',
    }
    return replacements.get(table_name, None)

def check_table_structure(table_name, sql_file):
    """Cek struktur tabel untuk memahami fungsinya"""
    with open(sql_file, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    pattern = rf'CREATE TABLE.*?`{re.escape(table_name)}`.*?\((.*?)\)\s*ENGINE'
    match = re.search(pattern, content, re.IGNORECASE | re.DOTALL)
    
    if match:
        table_def = match.group(1)
        # Extract key columns
        columns = re.findall(r'`(\w+)`\s+[^,]+', table_def)
        return columns[:10]  # First 10 columns
    return None

def main():
    codebase_path = '/opt/lampp/htdocs/optima1'
    sql_file = os.path.join(codebase_path, 'databases', 'optima_db_24-11-25_reorganized.sql')
    
    print("=" * 100)
    print("VERIFIKASI DETAIL TABEL YANG TIDAK DIGUNAKAN")
    print("=" * 100)
    print()
    
    results_summary = []
    
    for table in tables_to_check:
        print(f"\n{'='*100}")
        print(f"📋 MEMERIKSA: {table}")
        print(f"{'='*100}")
        
        # Cek struktur tabel
        columns = check_table_structure(table, sql_file)
        if columns:
            print(f"\n📊 Struktur Tabel (kolom utama):")
            print(f"   {', '.join(columns[:5])}...")
        
        # Cek pengganti
        replacement = check_replacement_table(table)
        if replacement:
            print(f"\n🔄 Tabel Pengganti:")
            print(f"   → {replacement}")
        
        # Cari referensi
        print(f"\n🔍 Mencari referensi di codebase...")
        refs = search_comprehensive(table, codebase_path)
        
        total_refs = sum(len(refs[k]) for k in refs)
        
        if total_refs > 0:
            print(f"   ✅ DITEMUKAN {total_refs} REFERENSI!")
            
            if refs['model_references']:
                print(f"\n   📦 Model References ({len(refs['model_references'])}):")
                for path, line, content in refs['model_references'][:3]:
                    print(f"      • {path}:{line}")
            
            if refs['direct_references']:
                print(f"\n   📝 Direct References ({len(refs['direct_references'])}):")
                for path, line, content in refs['direct_references'][:5]:
                    print(f"      • {path}:{line} - {content[:60]}...")
            
            if refs['sql_queries']:
                print(f"\n   💾 SQL Queries ({len(refs['sql_queries'])}):")
                for path, line, content in refs['sql_queries'][:3]:
                    print(f"      • {path}:{line}")
            
            if refs['migrations']:
                print(f"\n   🔄 Migrations ({len(refs['migrations'])}):")
                for path, line, content in refs['migrations']:
                    print(f"      • {path}:{line}")
            
            if refs['views']:
                print(f"\n   👁️  Views: {refs['views']}")
            
            if refs['procedures']:
                print(f"\n   ⚙️  Procedures: {refs['procedures']}")
            
            results_summary.append((table, 'DIGUNAKAN', total_refs, replacement))
        else:
            print(f"   ❌ TIDAK DITEMUKAN REFERENSI")
            if replacement:
                print(f"\n   💡 KESIMPULAN: Tabel ini mungkin sudah diganti dengan {replacement}")
            else:
                print(f"\n   💡 KESIMPULAN: Tabel ini benar-benar tidak digunakan")
            results_summary.append((table, 'TIDAK DIGUNAKAN', 0, replacement))
    
    # Summary
    print(f"\n\n{'='*100}")
    print("📊 RINGKASAN VERIFIKASI")
    print(f"{'='*100}\n")
    
    used_count = sum(1 for _, status, _, _ in results_summary if status == 'DIGUNAKAN')
    unused_count = len(results_summary) - used_count
    
    print(f"✅ Tabel yang DIGUNAKAN: {used_count}")
    print(f"❌ Tabel yang TIDAK DIGUNAKAN: {unused_count}")
    print()
    
    print("=" * 100)
    print("DETAIL HASIL VERIFIKASI")
    print("=" * 100)
    print()
    
    for table, status, ref_count, replacement in results_summary:
        status_icon = "✅" if status == 'DIGUNAKAN' else "❌"
        print(f"{status_icon} {table:40s} | Status: {status:15s} | Referensi: {ref_count:3d}")
        if replacement and status == 'TIDAK DIGUNAKAN':
            print(f"   └─ Pengganti: {replacement}")
    
    # Save detailed report
    report_file = os.path.join(codebase_path, 'databases', 'VERIFICATION_DETAILED_REPORT.md')
    with open(report_file, 'w', encoding='utf-8') as f:
        f.write("# VERIFIKASI DETAIL TABEL YANG TIDAK DIGUNAKAN\n\n")
        f.write(f"**Total Tabel Diperiksa:** {len(tables_to_check)}\n")
        f.write(f"**Tabel Digunakan:** {used_count}\n")
        f.write(f"**Tabel Tidak Digunakan:** {unused_count}\n\n")
        f.write("---\n\n")
        
        for table, status, ref_count, replacement in results_summary:
            f.write(f"## {table}\n\n")
            f.write(f"- **Status:** {status}\n")
            f.write(f"- **Jumlah Referensi:** {ref_count}\n")
            if replacement:
                f.write(f"- **Tabel Pengganti:** {replacement}\n")
            f.write("\n---\n\n")
    
    print(f"\n💾 Laporan detail disimpan ke: {report_file}")

if __name__ == '__main__':
    main()

